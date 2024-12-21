<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}
include "db.php";

$cars_sql = "SELECT * FROM cars ORDER BY id DESC";
$stmt = $conn->prepare($cars_sql);
if ($stmt === false) {
    die("Error preparing cars query: " . $conn->error);
}
$stmt->execute();
$cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
if (isset($_GET['message'])) {
    echo '<div class="message success">' . htmlspecialchars($_GET['message']) . '</div>';
} elseif (isset($_GET['error'])) {
    echo '<div class="message error">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/manage_cars.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
.actions {
    margin-bottom: 20px;
}

.add-car-btn {
    background: #dc3545;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.add-car-btn:hover {
    background: #c82333;
}

.cars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.car-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.car-image {
    height: 200px;
    overflow: hidden;
}

.car-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.car-details {
    padding: 15px;
}

.car-details h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.model {
    color: #666;
    margin: 5px 0;
}

.price {
    font-weight: bold;
    color: #dc3545;
    margin: 5px 0;
}

.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    margin-top: 10px;
}

.status-available {
    background: #d4edda;
    color: #155724;
}

.status-rented {
    background: #fff3cd;
    color: #856404;
}

.status-maintenance {
    background: #f8d7da;
    color: #721c24;
}

.car-actions {
    padding: 15px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.car-actions a {
    color: #666;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
    transition: all 0.3s;
}

.edit-btn:hover {
    color: #007bff;
    background: rgba(0,123,255,0.1);
}

.delete-btn:hover {
    color: #dc3545;
    background: rgba(220,53,69,0.1);
}
.brand {
    color: #666;
    font-weight: 500;
    margin: 5px 0;
}

.specs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.spec-item {
    font-size: 0.9em;
    color: #666;
    display: flex;
    align-items: center;
    gap: 5px;
}

.spec-item i {
    color: #dc3545;
    font-size: 0.9em;
}

.message {
    padding: 10px;
    margin: 20px 0;
    border-radius: 5px;
    display: none; /* Hidden by default */
}

.message.success {
    background-color: #d4edda;
    color: #155724;
}

.message.error {
    background-color: #f8d7da;
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
                <button type="submit" class="menu-item" style="width: 100%; text-align: left; border: none; background: none; color: inherit; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
        
        <div class="main-content">
            <h1>Manage Cars</h1>
            
            <div class="actions">
                <button class="add-car-btn" onclick="location.href='add_car.php'">
                    <i class="fas fa-plus"></i> Add New Car
                </button>
            </div>
            
            <div class="cars-grid">
                <?php foreach ($cars as $car): ?>
                <div class="car-card">
                    <div class="car-image">
                        <img src="<?php echo htmlspecialchars($car['image_url'] ?? 'images/default-car.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($car['name']); ?>">
                    </div>
                    <div class="car-details">
                        <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                        <p class="brand"><?php echo htmlspecialchars($car['brand']); ?></p>
                        <p class="model"><?php echo htmlspecialchars($car['model_year']); ?></p>
                        <p class="price">₱<?php echo number_format($car['price_per_day'], 2); ?> / day</p>
                        <div class="specs">
                            <span class="spec-item">
                                <i class="fas fa-users"></i> <?php echo $car['passengers']; ?> seats
                            </span>
                            <span class="spec-item">
                                <i class="fas fa-gear"></i> 
                                <?php echo !empty($car['transmission']) ? htmlspecialchars($car['transmission']) :$car['transmission']; ?>
                            </span>
                            <span class="spec-item">
                                <i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($car['fuel_type']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="car-actions">
                        <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="edit-btn" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_car.php?id=<?php echo $car['id']; ?>" class="delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this car?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
    function deleteCar(carId) {
        if (confirm('Are you sure you want to delete this car?')) {
            window.location.href = `delete_car.php?id=${carId}`;
        }
    }
    </script>
</body>
</html> 