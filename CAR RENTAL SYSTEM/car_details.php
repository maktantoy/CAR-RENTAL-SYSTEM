<?php
include 'db.php';

$car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;  // Changed from 'id' to 'car_id'

$query = "SELECT * FROM cars WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $car_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($result);

if (!$car) {
    header("Location: cars2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($car['name']); ?> - Details</title>
    <link rel="stylesheet" href="/style/car_details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f5f5;
    padding: 2rem;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.car-details {
    background: white;
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    display: flex;
    margin-top: 20px;
    margin-left: 20px;
    margin-right: 20px;
    font-weight: bolder;
}

.car-image {
    flex: 2;
    padding: 10px;
    margin: 10px;
}

.car-image img {
    width: 100%;
    height: 100%;
    border-radius: 10px;
    object-fit: fill;
}

.car-info {
    flex: 1;
    padding: 40px;
}

h1 {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 20px;
}

.price {
    font-size: 1.5rem;
    color: #666;
    margin-bottom: 30px;
}

.price span {
    color: #ff0000;
    font-weight: bold;
    font-size: 2rem;
}

.specs-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

.spec-item {
    background: #f8f8f8;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}

.spec-item i {
    font-size: 24px;
    color: #ff0000;
    margin-bottom: 10px;
}

.spec-item span {
    display: block;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.spec-item strong {
    display: block;
    color: #333;
    font-size: 1.1rem;
}

.actions {
    display: flex;
    gap: 20px;
}

.book-btn, .back-btn {
    flex: 1;
    padding: 15px 30px;
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.book-btn {
    background: #ff0000;
    color: white;
}

.back-btn {
    background: #333;
    color: white;
}

.book-btn:hover, .back-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.back-link {
    text-align: right;
    display: block;
    margin-bottom: 20px;
    padding: auto;
}

@media (max-width: 768px) {
    .car-details {
        flex-direction: column;
    }
    
    .specs-grid {
        grid-template-columns: 1fr;
    }
    
    .actions {
        flex-direction: column;
    }
} 
</style>
<body>
    <div class="container">
        <div class="car-details">
            <a href="cars2.php" class="back-link" class="fas fa-close">X</a>
            <div class="car-image">
                <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                     alt="<?php echo htmlspecialchars($car['name']); ?>">
            </div>
            
            <div class="car-info">
                <h1><?php echo htmlspecialchars($car['name']); ?></h1>
                <div class="price">
                    <span>$<?php echo htmlspecialchars($car['price_per_day']); ?></span> per day
                </div>
                
                <div class="specs-grid">
                    <div class="spec-item">
                        <i class="fas fa-horse"></i>
                        <span>Horsepower</span>
                        <strong><?php echo htmlspecialchars($car['horsepower']); ?> HP</strong>
                    </div>
                    
                    <div class="spec-item">
                        <i class="fas fa-road"></i>
                        <span>Mileage</span>
                        <strong><?php echo htmlspecialchars($car['mileage']); ?> miles</strong>
                    </div>
                    
                    <div class="spec-item">
                        <i class="fas fa-gas-pump"></i>
                        <span>Fuel Type</span>
                        <strong><?php echo htmlspecialchars($car['fuel_type']); ?></strong>
                    </div>
                    
                    <div class="spec-item">
                        <i class="fas fa-engine"></i>
                        <span>Engine</span>
                        <strong><?php echo htmlspecialchars($car['engine_size']); ?></strong>
                    </div>
                    
                    <div class="spec-item">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Top Speed</span>
                        <strong><?php echo htmlspecialchars($car['top_speed']); ?> mph</strong>
                    </div>
                    
                    <div class="spec-item">
                        <i class="fas fa-stopwatch"></i>
                        <span>0-60 mph</span>
                        <strong><?php echo htmlspecialchars($car['acceleration_0_60']); ?>s</strong>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="booking.php?car_id=<?php echo $car['id']; ?>" class="book-btn">Book Now</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 