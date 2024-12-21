<?php
session_start();
include 'db.php';

if (!isset($_GET['booking_id'])) {
    header("Location: booking.php");
    exit();
}

$booking_id = (int)$_GET['booking_id'];

// Fetch booking details
$query = "SELECT b.*, c.price_per_day, c.name as car_name 
          FROM bookings b 
          JOIN cars c ON b.car_id = c.id 
          WHERE b.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    header("Location: booking.php");
    exit();
}

// Calculate total days and amount
$pickup_date = new DateTime($booking['pickup_date']);
$return_date = new DateTime($booking['return_date']);
$days = $pickup_date->diff($return_date)->days + 1;
$total_amount = $days * $booking['price_per_day'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process payment
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $transaction_id = 'TXN' . time() . rand(1000, 9999);
    
    // Insert payment record
    $payment_sql = "INSERT INTO payments (booking_id, amount, payment_method, payment_status, transaction_id) 
                   VALUES (?, ?, ?, 'completed', ?)";
    $payment_stmt = mysqli_prepare($conn, $payment_sql);
    mysqli_stmt_bind_param($payment_stmt, "idss", $booking_id, $total_amount, $payment_method, $transaction_id);
    
    if (mysqli_stmt_execute($payment_stmt)) {
        // Update booking status
        $update_sql = "UPDATE bookings SET status = 'confirmed' WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "i", $booking_id);
        mysqli_stmt_execute($update_stmt);
        
        // Update revenue in the dashboard
        $revenue_sql = "UPDATE dashboard SET total_revenue = total_revenue + ?";
        $revenue_stmt = mysqli_prepare($conn, $revenue_sql);
        mysqli_stmt_bind_param($revenue_stmt, "d", $total_amount);
        mysqli_stmt_execute($revenue_stmt);
        
        $_SESSION['success_message'] = "Payment successful! Your booking is confirmed.";
        header("Location: booking_confirmation.php?booking_id=" . $booking_id);
        exit();
    } else {
        $_SESSION['error_message'] = "Payment failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - CarRental</title>
    <link rel="stylesheet" href="style/payment.css">
</head>
<body>
    <div class="container">
        <div class="payment-summary">
            <h2>Booking Summary</h2>
            <div class="summary-details">
                <p><strong>Car:</strong> <?php echo htmlspecialchars($booking['car_name']); ?></p>
                <p><strong>Duration:</strong> <?php echo $days; ?> days</p>
                <p><strong>Pickup Date:</strong> <?php echo htmlspecialchars($booking['pickup_date']); ?></p>
                <p><strong>Return Date:</strong> <?php echo htmlspecialchars($booking['return_date']); ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($total_amount, 2); ?></p>
            </div>
        </div>

        <div class="payment-form">
            <h2>Payment Details</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" pattern="\d*" maxlength="16" placeholder="Card Number" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="text" placeholder="MM/YY" maxlength="5" required>
                    </div>
                    <div class="form-group">
                        <label>CVV</label>
                        <input type="text" pattern="\d*" maxlength="3" placeholder="CVV" required>
                    </div>
                </div>

                <button type="submit" class="pay-button">Pay Now</button>
                <a href="booking.php" class="cancel-button">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>