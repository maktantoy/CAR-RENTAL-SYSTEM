<?php
session_start();

// Add debugging


if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    echo "Redirecting: user_id exists: " . isset($_SESSION['user_id']) . 
         ", is_admin value: " . (isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 'not set');
    header("Location: login.php");
    exit();
}

include "db.php";

$user_id = $_SESSION['user_id'];

// Get total users count
$users_sql = "SELECT COUNT(*) as count FROM users";
$stmt = $conn->prepare($users_sql);
if ($stmt === false) {
    die("Error preparing users query: " . $conn->error);
}
$stmt->execute();
$total_users = $stmt->get_result()->fetch_assoc()['count'];

// Get total bookings count
$bookings_sql = "SELECT COUNT(*) as count FROM bookings";
$stmt = $conn->prepare($bookings_sql);
if ($stmt === false) {
    die("Error preparing bookings query: " . $conn->error);
}
$stmt->execute();
$total_bookings = $stmt->get_result()->fetch_assoc()['count'];

// Get total revenue
$revenue_sql = "
    SELECT SUM(c.price_per_day * DATEDIFF(b.return_date, b.pickup_date)) as total 
    FROM bookings b 
    JOIN cars c ON b.car_id = c.id";
$stmt = $conn->prepare($revenue_sql);
if ($stmt === false) {
    die("Error preparing revenue query: " . $conn->error);
}
$stmt->execute();
$total_revenue = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Get recent bookings
$recent_bookings_sql = "
    SELECT b.*,
           b.name as user_email,
           c.name as car 
    FROM bookings b 
    LEFT JOIN users u ON b.name = u.email 
    LEFT JOIN cars c ON b.car_id = c.id 
    ORDER BY b.pickup_date DESC 
    LIMIT 5";

$stmt = $conn->prepare($recent_bookings_sql);
if ($stmt === false) {
    die("Error preparing recent bookings query: " . $conn->error);
}
$stmt->execute();
$recent_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Debug: Print recent bookings
echo "<!-- Debug: Recent Bookings = " . json_encode($recent_bookings) . " -->";
$query = "SELECT total_revenue FROM dashboard LIMIT 1"; // Assuming there's only one record
$result = mysqli_query($conn, $query);
$total_revenue = 0.00; // Default value

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $total_revenue = $row['total_revenue'];
} else {
    // Handle the case where no results are returned
    $total_revenue = 0.00; // or set to a default value
}

// Fetch total revenue and bookings by car
$query = "
    SELECT c.name AS car_name, COUNT(b.id) AS total_bookings, SUM(c.price_per_day * DATEDIFF(b.return_date, b.pickup_date)) AS total_revenue
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    GROUP BY c.id
";
$result = mysqli_query($conn, $query);
$revenue_data = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $revenue_data[] = $row;
    }
} else {
    // Debugging: Output the error
    echo "Error: " . mysqli_error($conn);
}

// Retrieve the booked car name for display
if (isset($_SESSION['booked_car_name'])) {
    echo "<p>Recently booked car: " . $_SESSION['booked_car_name'] . "</p>";
    // Optionally, clear the session variable after displaying
    unset($_SESSION['booked_car_name']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
        }
        
        .admin-actions {
            margin-bottom: 30px;
        }
        
        .action-btn {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .action-btn:hover {
            background: #c82333;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .bookings-table th,
        .bookings-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .bookings-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
        }
        
        .status-pending {
            background: #ffeeba;
            color: #856404;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="user-info">
                <h3>Admin Panel</h3>
                <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            
            <a href="dashboard.php" class="menu-item active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="manage_users.php" class="menu-item">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="manage_cars.php" class="menu-item">
                <i class="fas fa-car"></i> Manage Cars
            </a>
            <a href="manage_bookings.php" class="menu-item">
                <i class="fas fa-calendar-check"></i> Manage Bookings
            </a>
            <a href="reports.php" class="menu-item">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="feedback_dashboard.php" class="menu-item">
                <i class="fas fa-chart-bar"></i> Feedback
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <form action="logout.php" method="POST" style="margin-top: auto;">
                <button type="submit" class="menu-item" style="width: 100%; text-align: left; border: none; background: none; color: inherit;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
        
        <div class="main-content">
            <h1>Admin Dashboard</h1>
            
            <div class="admin-stats">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="value"><?php echo $total_users; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Bookings</h3>
                    <div class="value"><?php echo $total_bookings; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="value">₱<?php echo number_format($total_revenue, 2); ?></div>
                </div>
            </div>
            
            <div class="admin-actions">
                <button class="action-btn" onclick="location.href='add_car.php'">Add New Car</button>
                <button class="action-btn" onclick="location.href='add_user.php'">Add New User</button>
            </div>
            
            <h2>Recent Bookings</h2>
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        
                        <th>Car</th>
                        <th>Pickup Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $booking): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($booking['id']); ?></td>
                       
                        <td><?php echo htmlspecialchars($booking['car']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></td>
                        <td><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($booking['status'] ?? 'pending'); ?>">
                                <?php echo htmlspecialchars($booking['status'] ?? 'Pending'); ?>
                            </span>
                        </td>
                        <td>
                            <a href="view_booking.php?id=<?php echo $booking['id']; ?>" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_booking.php?id=<?php echo $booking['id']; ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete_booking.php?id=<?php echo $booking['id']; ?>" onclick="deleteBooking(<?php echo $booking['id']; ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function deleteBooking(bookingId) {
        if (confirm('Are you sure you want to delete this booking?')) {
            window.location.href = `delete_booking.php?id=${bookingId}`;
        }
    }
    </script>
</body>
</html>