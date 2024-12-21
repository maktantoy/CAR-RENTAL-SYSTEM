<?php
session_start();
include 'db.php';

if (!isset($_GET['booking_id'])) {
    header("Location: booking.php");
    exit();
}

$booking_id = (int)$_GET['booking_id'];

// Fetch booking and payment details
$query = "SELECT b.*, c.name as car_name, p.transaction_id, p.payment_method 
          FROM bookings b 
          JOIN cars c ON b.car_id = c.id 
          JOIN payments p ON p.booking_id = b.id 
          WHERE b.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link rel="stylesheet" href="style/payment.css">
</head>
<body>
    <div class="container">
        <div class="confirmation-message">
            <h2>Booking Confirmed!</h2>
            <div class="confirmation-details">
                <p>Thank you for your booking. Your transaction was successful.</p>
                <p><strong>Booking ID:</strong> <?php echo $booking_id; ?></p>
                <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($booking['transaction_id']); ?></p>
                <p><strong>Car:</strong> <?php echo htmlspecialchars($booking['car_name']); ?></p>
                <p><strong>Pickup Date:</strong> <?php echo htmlspecialchars($booking['pickup_date']); ?></p>
                <p><strong>Return Date:</strong> <?php echo htmlspecialchars($booking['return_date']); ?></p>
                <a href="cars2.php" class="pay-button">Back to Cars</a>
            </div>
        </div>
    </div>
</body>
</html>