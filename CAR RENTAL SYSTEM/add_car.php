<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $model_year = $_POST['model_year'];
    $passengers = $_POST['passengers'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $price_per_day = $_POST['price_per_day'];
    
    // Handle file upload
    $image_url = '';
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
        $target_dir = "uploads/cars/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["car_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $sql = "INSERT INTO cars (name, brand, model_year, passengers, transmission, 
            fuel_type, price_per_day, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiissds", $name, $brand, $model_year, $passengers, 
                      $transmission, $fuel_type, $price_per_day, $image_url);
    
    if ($stmt->execute()) {
        header("Location: manage_cars.php");
        exit();
    } else {
        $error = "Error adding car: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Car - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/add_car.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .add-car-form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    max-width: 600px;
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

.form-group input[type="file"] {
    border: none;
    padding: 0;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.submit-btn,
.cancel-btn {
    padding: 10px 20px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    text-align: center;
}

.submit-btn {
    background: #dc3545;
    color: white;
}

.submit-btn:hover {
    background: #c82333;
}

.cancel-btn {
    background: #6c757d;
    color: white;
}

.cancel-btn:hover {
    background: #5a6268;
}
</style>
<body>
    <div class="dashboard-container">
        <!-- Copy the sidebar from manage_cars.php -->
        
        <div class="main-content">
            <h1>Add New Car</h1>
            
            <form class="add-car-form" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Car Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand" required>
                </div>

                <div class="form-group">
                    <label for="model_year">Model Year</label>
                    <input type="number" id="model_year" name="model_year" required>
                </div>

                <div class="form-group">
                    <label for="passengers">Number of Passengers</label>
                    <input type="number" id="passengers" name="passengers" required>
                </div>

                <div class="form-group">
                    <label for="transmission">Transmission</label>
                    <select id="transmission" name="transmission" required>
                        <option value="Automatic">Automatic</option>
                        <option value="Manual">Manual</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fuel_type">Fuel Type</label>
                    <select id="fuel_type" name="fuel_type" required>
                        <option value="Gasoline">Gasoline</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Electric">Electric</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price_per_day">Price per Day (₱)</label>
                    <input type="number" id="price_per_day" name="price_per_day" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="car_image">Car Image</label>
                    <input type="file" id="car_image" name="car_image" accept="image/*">
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">Add Car</button>
                    <a href="manage_cars.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 