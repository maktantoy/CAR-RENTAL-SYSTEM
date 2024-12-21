<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch booking details with related information
$booking_sql = "
    SELECT 
        b.id,
        b.name,
        b.phone,
        b.persons,
        b.pickup_location,
        b.pickup_date,
        b.pickup_time,
        b.return_date,
        b.booking_date,
        b.status,
        c.name as car_name,
        c.price_per_day,
        c.image_url,
        c.model_year,
        c.brand,
        c.passengers,
        c.transmission,
        c.category,
        c.horsepower,
        c.mileage,
        c.fuel_type,
        c.engine_size,
        c.top_speed,
        c.acceleration_0_60
    FROM bookings b
    LEFT JOIN cars c ON b.car_id = c.id
    WHERE b.id = ?";

$stmt = $conn->prepare($booking_sql);
if ($stmt === false) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();





$pickup_date = new DateTime($booking['pickup_date']);
$return_date = new DateTime($booking['return_date']);
$total_days = $pickup_date->diff($return_date)->days;
$total_cost = $total_days * $booking['price_per_day'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - Car Rental</title>
    <link rel="stylesheet" href="style/view_booking.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.booking-details {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 30px;
}

.booking-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
    margin-top: 20px;
}

.car-info {
    text-align: center;
}

.car-info img {
    width: 100%;
    max-width: 400px;
    border-radius: 10px;
    margin-bottom: 15px;
}

.details-section h3 {
    color: #333;
    margin: 20px 0 10px 0;
    border-bottom: 2px solid #dc3545;
    padding-bottom: 5px;
}

.details-section p {
    margin: 10px 0;
    color: #666;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 14px;
}

.status-pending {
    background: #ffeeba;
    color: #856404;
}

.status-confirmed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.booking-actions {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 20px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
}

.btn-edit {
    background: #007bff;
    color: white;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-back {
    background: #6c757d;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .booking-grid {
        grid-template-columns: 1fr;
    }
    
    .booking-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

.car-specs {
    margin-top: 20px;
    text-align: left;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.car-specs h3 {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 5px;
    border-bottom: 2px solid #dc3545;
}

.car-specs p {
    margin: 8px 0;
    color: #666;
    display: flex;
    justify-content: space-between;
}

.car-specs strong {
    color: #333;
}

@media (max-width: 768px) {
    .car-specs {
        margin-bottom: 20px;
    }
}
</style>
<body>
    <div class="container">
        <div class="booking-details">
            <h1>Booking Details #<?php echo $booking['id']; ?></h1>
            
            <div class="booking-grid">
                <div class="car-info">
                    <div class="car-specs">
                        <h3>Vehicle Specifications</h3>
                        <?php 
                        // Debugging: Output the image URL
                        if (empty($booking['image_url'])) {
                            echo '<p style="color: red;">Image URL is empty!</p>'; 
                        } else {
                            echo '<p>Image URL: ' . htmlspecialchars($booking['image_url']) . '</p>'; 
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="<?php echo htmlspecialchars($booking['car_name']); ?>" style="width: 100%; max-width: 300px; border-radius: 8px; margin-bottom: 15px;">
                        <p><strong>Year:</strong> <?php echo htmlspecialchars($booking['model_year']); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($booking['category']); ?></p>
                        <p><strong>Transmission:</strong> <?php echo htmlspecialchars($booking['transmission']); ?></p>
                        <p><strong>Passengers:</strong> <?php echo htmlspecialchars($booking['passengers']); ?></p>
                        <p><strong>Fuel Type:</strong> <?php echo htmlspecialchars($booking['fuel_type']); ?></p>
                        <p><strong>Engine:</strong> <?php echo htmlspecialchars($booking['engine_size']); ?></p>
                        <p><strong>Horsepower:</strong> <?php echo htmlspecialchars($booking['horsepower']); ?> HP</p>
                        <p><strong>Top Speed:</strong> <?php echo htmlspecialchars($booking['top_speed']); ?> km/h</p>
                        <p><strong>0-60 mph:</strong> <?php echo htmlspecialchars($booking['acceleration_0_60']); ?> sec</p>
                        <p><strong>Mileage:</strong> <?php echo htmlspecialchars($booking['mileage']); ?> km/l</p>
                    </div>
                    <h2><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['car_name']); ?></h2>  
                </div>

                <div class="details-section">
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['name']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
                    <p><strong>Number of Persons:</strong> <?php echo htmlspecialchars($booking['persons']); ?></p>
                    <p><strong>Pickup Location:</strong> <?php echo htmlspecialchars($booking['pickup_location']); ?></p>
                    <p><strong>Pickup Time:</strong> <?php echo htmlspecialchars($booking['pickup_time']); ?></p>
                    <p><strong>Booking Date:</strong> <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></p>

                    <h3>Booking Information</h3>
                    <p><strong>Status:</strong> 
                        <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo htmlspecialchars($booking['status']); ?>
                        </span>
                    </p>
                    <p><strong>Pickup Date:</strong> <?php echo date('M d, Y', strtotime($booking['pickup_date'])); ?></p>
                    <p><strong>Return Date:</strong> <?php echo date('M d, Y', strtotime($booking['return_date'])); ?></p>
                    <p><strong>Total Days:</strong> <?php echo $total_days; ?></p>
                    <p><strong>Daily Rate:</strong> ₱<?php echo number_format($booking['price_per_day'], 2); ?></p>
                    <p><strong>Total Cost:</strong> ₱<?php echo number_format($total_cost, 2); ?></p>

                    <div class="booking-actions">
                        <a href="edit_booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit Booking
                        </a>
                        <button onclick="deleteBooking(<?php echo $booking['id']; ?>)" class="btn btn-delete">
                            <i class="fas fa-trash"></i> Delete Booking
                        </button>
                        <a href="dashboard.php" class="btn btn-back">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
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