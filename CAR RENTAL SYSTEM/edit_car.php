<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

// Get car details
if (isset($_GET['id'])) {
    $car_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $car = $stmt->get_result()->fetch_assoc();
    
    if (!$car) {
        header("Location: manage_cars.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add debug line
    error_log("Form submitted: " . print_r($_POST, true));
    
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $model_year = $_POST['model_year'];
    $price_per_day = $_POST['price_per_day'];
    $passengers = $_POST['passengers'];
    $transmission = trim($_POST['transmission']);
    error_log("Transmission value from POST: '" . $transmission . "'");
    $fuel_type = $_POST['fuel_type'];
    $horsepower = $_POST['horsepower'];
    $category = $_POST['category'];
    $top_speed = $_POST['top_speed'];
    $acceleration_0_60 = $_POST['acceleration_0_60'];
    $engine_size = $_POST['engine_size'];
    $mileage = $_POST['mileage'];
    $car_id = $_POST['car_id'];

    // Add debug line before update
    error_log("About to execute update with transmission: '" . $transmission . "'");

    // Handle image upload
    $image_url = $car['image_url']; // Keep existing image by default
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "uploads/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE cars SET name=?, brand=?, model_year=?, price_per_day=?, 
                           image_url=?, passengers=?, transmission=?, fuel_type=?, horsepower=?, 
                           category=?, top_speed=?, acceleration_0_60=?, engine_size=?, mileage=? WHERE id=?");

    // Ensure the number of parameters matches the SQL statement
    $stmt->bind_param("ssiisiissssisss", $name, $brand, $model_year, $price_per_day, 
                      $image_url, $passengers, $transmission, $fuel_type, $horsepower, 
                      $category, $top_speed, $acceleration_0_60,$engine_size, $mileage, $car_id);

    if ($stmt->execute()) {
        error_log("Update successful");
        header("Location: manage_cars.php");
        exit();
    } else {
        error_log("Update failed: " . $stmt->error);
    }
}

function getTransmissionTypes() {
    return [
        'Automatic' => 'Automatic',
        'Manual' => 'Manual',
        'CVT' => 'CVT (Continuously Variable)',
        'Semi-Automatic' => 'Semi-Automatic',
        'Dual-Clutch' => 'Dual-Clutch (DCT)',
        'Automated Manual' => 'Automated Manual (AMT)',
        'Tiptronic' => 'Tiptronic'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/edit_car.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .edit-car-form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 20px auto;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #333;
    font-weight: 500;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.current-image {
    max-width: 300px;
    margin: 10px 0;
    border-radius: 5px;
    display: block;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.save-btn {
    background: #28a745;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.save-btn:hover {
    background: #218838;
}

.cancel-btn {
    background: #dc3545;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    text-align: center;
    transition: background-color 0.3s;
}

.cancel-btn:hover {
    background: #c82333;
}

.form-group input[type="file"] {
    border: none;
    padding: 10px 0;
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
            <h1>Edit Car</h1>
            
            <form class="edit-car-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?php echo $car['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand" value="<?php echo $car['brand']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="model_year">Model Year</label>
                    <input type="number" id="model_year" name="model_year" value="<?php echo $car['model_year']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="price_per_day">Price per Day</label>
                    <input type="number" id="price_per_day" name="price_per_day" value="<?php echo $car['price_per_day']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="passengers">Passengers</label>
                    <input type="number" id="passengers" name="passengers" value="<?php echo $car['passengers']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="transmission">Transmission</label>
                    <select id="transmission" name="transmission" required>
                        <?php 
                        $transmissionTypes = getTransmissionTypes();
                        $selectedTransmission = $car['transmission'];

                        foreach ($transmissionTypes as $value => $label): 
                        ?>
                            <option value="<?php echo htmlspecialchars($value); ?>" 
                                    <?php echo (trim($selectedTransmission) === trim($value)) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fuel_type">Fuel Type</label>
                    <input type="text" id="fuel_type" name="fuel_type" value="<?php echo $car['fuel_type']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="horsepower">Horsepower</label>
                    <input type="text" id="horsepower" name="horsepower" value="<?php echo $car['horsepower']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?php echo $car['category']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="top_speed">Top Speed</label>
                    <input type="text" id="top_speed" name="top_speed" value="<?php echo $car['top_speed']; ?>" required>
                </div>
                    <div class="form-group">
                        <label for="engine_size">Engine Size</label>
                        <input type="text" id="engine_size" name="engine_size" value="<?php echo $car['engine_size']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="acceleration_0_60">Acceleration</label>
                        <input type="text" id="acceleration_0_60" name="acceleration_0_60" value="<?php echo $car['acceleration_0_60']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mileage">Miles</label>
                        <input type="text" id="mileage" name="mileage" value="<?php echo $car['mileage']; ?>" required>
                    </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image">
                </div>
                <div class="form-group">
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save Changes</button>
                        <a href="manage_cars.php" class="cancel-btn">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 