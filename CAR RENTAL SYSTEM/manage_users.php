<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

// Get users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

$users_sql = "
    SELECT u.*, 
       COUNT(b.id) as total_bookings,
       MAX(b.pickup_date) as last_booking
FROM users u
LEFT JOIN bookings b ON u.id = b.user_id
WHERE u.is_admin = 0
GROUP BY u.id
    ORDER BY u.created_at DESC
    LIMIT ? OFFSET ?";

$stmt = $conn->prepare($users_sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();

if ($stmt->error) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get total users for pagination
$total_sql = "SELECT COUNT(*) as count FROM users WHERE is_admin = 0 AND (email LIKE ? OR name LIKE ?)";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("ss", $search, $search);
$total_stmt->execute();
$total_users = $total_stmt->get_result()->fetch_assoc()['count'];
$total_pages = ceil($total_users / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/manage_users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.search-bar {
    position: relative;
    width: 300px;
}

.search-bar input {
    width: 100%;
    padding: 10px 35px 10px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.search-bar i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.users-table th,
.users-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.users-table th {
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

.actions {
    display: flex;
    gap: 10px;
}

.actions a {
    color: #666;
    text-decoration: none;
}

.actions a:hover {
    color: #dc3545;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
}

.pagination a {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #666;
}

.pagination a.active {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.pagination a:hover:not(.active) {
    background: #f8f9fa;
}

/* Action Bar Styles */
.action-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    transition: background-color 0.2s;
}

.action-btn:hover {
    background-color: #45a049;
}

.action-btn i {
    font-size: 16px;
}

/* Search Bar Styles */
.search-bar {
    position: relative;
    width: 300px;
}

.search-bar input {
    width: 100%;
    padding: 10px 35px 10px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.search-bar input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
}

.search-bar i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    font-size: 14px;
}

/* Actions Bar Container */
.actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
/* Status toggle animation */
.status-badge {
    transition: all 0.3s ease;
}

.status-badge.status-active {
    animation: statusChange 0.3s ease;
}

.status-badge.status-inactive {
    animation: statusChange 0.3s ease;
}

@keyframes statusChange {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}
.message {
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
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
                <button type="submit" class="menu-item" style="width: 100%; text-align: left; border: none; background: none; color: inherit; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
        
        <div class="main-content">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php 
                        echo htmlspecialchars($_SESSION['success_message']); 
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="message error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php 
                        echo htmlspecialchars($_SESSION['error_message']); 
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <h1>Manage Users</h1>
            
            <div class="actions-bar">
                <button class="action-btn" onclick="location.href='add_user.php'">
                    <i class="fas fa-plus"></i> Add New User
                </button>
                <form action="" method="GET" class="search-bar">
                    <input type="text" id="userSearch" name="search" placeholder="Search users..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <i class="fas fa-search"></i>
                </form>
            </div>

            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Total Bookings</th>
                        <th>Last Booking</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['total_bookings']; ?></td>
                        <td><?php echo $user['last_booking'] ? date('M d, Y', strtotime($user['last_booking'])) : 'Depende'; ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $user['active'] ? 'active' : 'inactive'; ?>">
                                <?php echo $user['active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="view_user.php?id=<?php echo $user['id']; ?>" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" onclick="return toggleUserStatus(<?php echo $user['id']; ?>)" title="Toggle Status">
                                <i class="fas fa-power-off"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo $page === $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <script>
    function toggleUserStatus(userId) {
        if (confirm('Are you sure you want to toggle this user\'s status?')) {
            window.location.href = `toggle_user_status.php?id=${userId}`;
        }
        return false; // Prevents the default link behavior
    }

    document.getElementById('userSearch').addEventListener('input', debounce(function(e) {
        const searchValue = e.target.value.trim();
        const currentUrl = new URL(window.location.href);
        
        if (searchValue) {
            currentUrl.searchParams.set('search', searchValue);
        } else {
            currentUrl.searchParams.delete('search');
        }
        
        // Reset to page 1 when searching
        currentUrl.searchParams.set('page', '1');
        window.location.href = currentUrl.toString();
    }, 500));

    // Debounce function to prevent too many requests
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    </script>
</body>
</html> 