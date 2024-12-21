<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($booking_id > 0) {
    // Prepare delete statement
    $delete_sql = "DELETE FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    
    if ($stmt === false) {
        $_SESSION['error_message'] = "Error preparing delete statement: " . $conn->error;
    } else {
        $stmt->bind_param("i", $booking_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Booking #$booking_id has been deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Error deleting booking: " . $conn->error;
        }
    }
} else {
    $_SESSION['error_message'] = "Invalid booking ID.";
}


header("Location: dashboard.php");
exit();
?> 