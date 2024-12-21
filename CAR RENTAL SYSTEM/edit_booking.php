<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_date = $_POST['pickup_date'];
    $return_date = $_POST['return_date'];
    $status = $_POST['status'];
    
    $update_sql = "UPDATE bookings SET pickup_date = ?, return_date = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    
    if ($stmt === false) {
        $error_message = "Error preparing statement: " . $conn->error;
    } else {
        $stmt->bind_param("sssi", $pickup_date, $return_date, $status, $booking_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Booking updated successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Error updating booking: " . $conn->error;
        }
    }
}

// Get booking details
$booking_sql = "
    SELECT b.*,
           b.name as user_name,
           c.name as car_name,
           c.price_per_day 
    FROM bookings b 
    LEFT JOIN users u ON b.name = u.email 
    LEFT JOIN cars c ON b.car_id = c.id 
    WHERE b.id = ?";

$stmt = $conn->prepare($booking_sql);
if ($stmt === false) {
    die("Error preparing booking query: " . $conn->error);
}

$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking - Car Rental</title>
    <link rel="stylesheet" href="style/edit_booking.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

h1 {
    color: #333;
    margin-bottom: 30px;
    text-align: center;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.edit-form {
    display: grid;
    gap: 20px;
}

.form-group {
    display: grid;
    gap: 8px;
}

.form-group label {
    font-weight: 600;
    color: #666;
}

.form-group input,
.form-group select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.form-group input[readonly] {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

.button-group {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.btn-save,
.btn-cancel {
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    text-decoration: none;
    text-align: center;
}

.btn-save {
    background: #28a745;
    color: white;
    border: none;
}

.btn-save:hover {
    background: #218838;
}

.btn-cancel {
    background: #dc3545;
    color: white;
    border: none;
}

.btn-cancel:hover {
    background: #c82333;
}
</style>
<body>
    <div class="container">
        <h1>Edit Booking #<?php echo $booking_id; ?></h1>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" class="edit-form">
            <div class="form-group">
                <label>User Email:</label>
                <input type="text" value="<?php echo htmlspecialchars($booking['user_name']); ?>" readonly>
            </div>

            <div class="form-group">
                <label>Car:</label>
                <input type="text" value="<?php echo htmlspecialchars($booking['car_name']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="pickup_date">Pickup Date:</label>
                <input type="date" id="pickup_date" name="pickup_date" 
                       value="<?php echo $booking['pickup_date']; ?>" required>
            </div>

            <div class="form-group">
                <label for="return_date">Return Date:</label>
                <input type="date" id="return_date" name="return_date" 
                       value="<?php echo $booking['return_date']; ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Pending" <?php echo $booking['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Confirmed" <?php echo $booking['status'] === 'Confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="Completed" <?php echo $booking['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo $booking['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-save">Save Changes</button>
                <a href="dashboard.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>