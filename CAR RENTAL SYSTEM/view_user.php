<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

// Get user ID from URL and validate
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id === 0) {
    header("Location: manage_users.php");
    exit();
}

// Fetch user details with booking statistics
$user_sql = "
    SELECT u.*,
           COUNT(DISTINCT b.id) as total_bookings,
           SUM(CASE WHEN b.status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
           SUM(CASE WHEN b.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
           MAX(b.pickup_date) as last_booking,
           0 as total_spent
    FROM users u
    LEFT JOIN bookings b ON u.email = b.name
    WHERE u.id = ?
    GROUP BY u.id";

$stmt = $conn->prepare($user_sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: manage_users.php");
    exit();
}

// Fetch recent bookings with simplified query
$bookings_sql = "
    SELECT *
    FROM bookings b
    WHERE b.name = (SELECT email FROM users WHERE id = ?)
    ORDER BY b.booking_date DESC
    LIMIT 5";

$stmt = $conn->prepare($bookings_sql);
if ($stmt === false) {
    die("Error preparing statement for bookings: " . $conn->error . "\nQuery: " . $bookings_sql);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/view_user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .back-button {
    margin-bottom: 20px;
}

.back-button a {
    color: #666;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.back-button a:hover {
    color: #dc3545;
}

.user-profile {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.user-avatar i {
    font-size: 64px;
    color: #666;
}

.user-info h1 {
    margin: 0 0 5px 0;
    font-size: 24px;
}

.user-info .email {
    color: #666;
    margin: 0 0 10px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.stat-card i {
    font-size: 24px;
    color: #dc3545;
    margin-bottom: 10px;
}

.stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
    color: #666;
}

.stat-card p {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.recent-bookings {
    margin-top: 30px;
}

.recent-bookings h2 {
    margin-bottom: 20px;
}

.bookings-table {
    width: 100%;
    border-collapse: collapse;
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

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}
</style>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="logo">
                <i class="fas fa-car"></i>
                <span>Car Rental</span>
            </div>
            <a href="dashboard.php" class="menu-item">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="manage_users.php" class="menu-item active">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="manage_cars.php" class="menu-item">
                <i class="fas fa-car"></i> Manage Cars
            </a>
            <a href="manage_bookings.php" class="menu-item">
                <i class="fas fa-calendar-alt"></i> Manage Bookings
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <form action="logout.php" method="POST" style="margin-top: auto;">
                <button type="submit" class="menu-item" style="width: 100%; text-align: left; border: none; background: none; color: inherit; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
        
        <div class="main-content">
            <div class="back-button">
                <a href="manage_users.php"><i class="fas fa-arrow-left"></i> Back to Users</a>
            </div>

            <div class="user-profile">
                <div class="profile-header">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-info">
                        <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                        <p class="email"><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="status-badge status-<?php echo $user['active'] ? 'active' : 'inactive'; ?>">
                            <?php echo $user['active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3>Total Bookings</h3>
                        <p><?php echo $user['total_bookings']; ?></p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-check-circle"></i>
                        <h3>Completed</h3>
                        <p><?php echo $user['completed_bookings']; ?></p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-times-circle"></i>
                        <h3>Cancelled</h3>
                        <p><?php echo $user['cancelled_bookings']; ?></p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-dollar-sign"></i>
                        <h3>Total Spent</h3>
                        <p>$<?php echo number_format($user['total_spent'], 2); ?></p>
                    </div>
                </div>

                <div class="recent-bookings">
                    <h2>Recent Bookings</h2>
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pickup Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_bookings as $booking): ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['return_date'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking['status']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($booking['pickup_location']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 