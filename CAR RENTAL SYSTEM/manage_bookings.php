<?php
session_start();
include "db.php"; // Include your database connection

// Fetch available cars
$cars_result = $conn->query("SELECT id, name, brand FROM cars WHERE availability = 'available'");

// Fetch existing bookings with car names
$result = $conn->query("
    SELECT b.*, c.name AS car_name 
    FROM bookings b 
    LEFT JOIN cars c ON b.car_id = c.id
");

$total_users_result = $conn->query("SELECT COUNT(*) AS total FROM users");
$total_users = $total_users_result->fetch_assoc()['total'];

$total_bookings_result = $conn->query("SELECT COUNT(*) AS total FROM bookings");
$total_bookings = $total_bookings_result->fetch_assoc()['total'];



$total_revenue = 0; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['create'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $persons = (int)$_POST['persons'];
        $car_id = (int)$_POST['car_id']; 
        $pickup_location = $_POST['pickup_location'];
        $pickup_date = $_POST['pickup_date'];
        $pickup_time = $_POST['pickup_time'];
        $return_date = $_POST['return_date'];
        $status = 'pending';

        $stmt = $conn->prepare("INSERT INTO bookings (name, phone, persons, car_id, pickup_location, pickup_date, pickup_time, return_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissssss", $name, $phone, $persons, $car_id, $pickup_location, $pickup_date, $pickup_time, $return_date, $status);
        $stmt->execute();
    }
    // Handle booking deletion
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM bookings WHERE id = $id");
    }
    // Handle status update
    if (isset($_POST['update_status'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style/manage_bookings.css">
</head>
<style>
    
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

h1, h2 {
    color: #333;
}

form {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}
.dashboard-container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 280px;
    background-color: #1e293b;
    color: white;
    padding: 24px;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
}

.sidebar .user-info {
    padding: 24px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 24px;
}

.sidebar .user-info h3 {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #94a3b8;
    margin-bottom: 8px;
}

.sidebar .menu-item {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    color: #e2e8f0;
    text-decoration: none;
    transition: all 0.2s ease;
    border-radius: 8px;
    margin-bottom: 4px;
}

.sidebar .menu-item:hover {
    background-color: rgba(255,255,255,0.1);
    transform: translateX(4px);
}

.sidebar .menu-item i {
    margin-right: 12px;
    font-size: 1.1rem;
    width: 24px;
    text-align: center;
}

.main-content {
    flex: 1;
    padding: 32px;
}

.welcome-section {
    background-color: white;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    margin-bottom: 32px;
}

.welcome-section h1 {
    font-size: 1.875rem;
    color: #1e293b;
    margin-bottom: 8px;
}

.welcome-section p {
    color: #64748b;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 24px;
}

.stat-card {
    background-color: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
}

.stat-card h3 {
    color: #64748b;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 12px;
}

.stat-card p {
    color: #1e293b;
    font-size: 1.5rem;
    font-weight: 600;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="time"],
select {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    background-color: #28a745;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background-color: #218838;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
}
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

    /* Improved styles for buttons and dropdowns in the bookings table */
    
    /* Button styles */
    button {
        background-color: #28a745; /* Green background */
        color: white;
        padding: 10px 15px; /* Increased padding for better click area */
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s; /* Added transform transition */
        font-size: 14px; /* Increased font size for better readability */
    }

    button:hover {
        background-color: #218838; /* Darker green on hover */
        transform: scale(1.05); /* Slightly enlarge button on hover */
    }

    /* Select element styles */
    select {
        width: 100%;
        padding: 10px; /* Increased padding for better usability */
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        transition: border-color 0.3s; /* Smooth border transition */
        font-size: 14px; /* Increased font size for better readability */
    }

    select:focus {
        border-color: #28a745; /* Highlight border on focus */
        outline: none; /* Remove default outline */
    }

    /* Table cell styles */
    td {
        vertical-align: middle; /* Center align buttons and dropdowns */
    }

    /* Status badge styles */
    .status-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        display: inline-block; /* Ensure badges are inline */
    }

    .status-pending {
        background: #ffeeba;
        color: #856404;
    }

    .status-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .status-completed {
        background: #cce5ff;
        color: #004085;
    }

    .status-canceled {
        background: #f8d7da;
        color: #721c24;
    }
</style>
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
            
           
    <h1>Manage Bookings</h1>
    <form method="POST">
        <h2>Create Booking</h2>
        <input type="text" name="name" placeholder="Name" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="number" name="persons" placeholder="Number of Persons" required>
        
        <label for="car_id">Select Car:</label>
        <select name="car_id" required>
            <option value="">Select a car</option>
            <?php while ($car = $cars_result->fetch_assoc()): ?>
                <option value="<?php echo $car['id']; ?>"><?php echo htmlspecialchars($car['name'] . ' - ' . $car['brand']); ?></option>
            <?php endwhile; ?>
        </select>

        <input type="text" name="pickup_location" placeholder="Pickup Location" required>
        <input type="date" name="pickup_date" required>
        <input type="time" name="pickup_time" required>
        <input type="date" name="return_date" required>
        <button type="submit" name="create">Create Booking</button>
    </form>

    <h2>Existing Bookings</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Persons</th>
                <th>Car</th>
                <th>Pickup Location</th>
                <th>Pickup Date</th>
                <th>Pickup Time</th>
                <th>Return Date</th>
                <th>Status</th>
                <th>Update Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo $row['persons']; ?></td>
                <td><?php echo htmlspecialchars($row['car_name']); ?></td>
                <td><?php echo htmlspecialchars($row['pickup_location']); ?></td>
                <td><?php echo $row['pickup_date']; ?></td>
                <td><?php echo $row['pickup_time']; ?></td>
                <td><?php echo $row['return_date']; ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <select name="status" required>
                            <option value="pending" <?php echo $row['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $row['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="completed" <?php echo $row['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="canceled" <?php echo $row['status'] === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                        </select>
                        <button type="submit" name="update_status">Update</button>
                    </form>
                </td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>