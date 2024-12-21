<?php
session_start();
include "db.php";

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Check if the car ID is provided
if (isset($_GET['id'])) {
    $carId = intval($_GET['id']);
    
    // Prepare the delete statement
    $delete_sql = "DELETE FROM cars WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    
    if ($stmt === false) {
        die("Error preparing delete query: " . $conn->error);
    }
    
    // Bind the parameter and execute
    $stmt->bind_param("i", $carId);
    if ($stmt->execute()) {
        // Redirect back to manage cars page with success message
        header("Location: manage_cars.php?message=Car deleted successfully");
    } else {
        // Redirect back with error message
        header("Location: manage_cars.php?error=Error deleting car");
    }
    
    $stmt->close();
} else {
    header("Location: manage_cars.php?error=No car ID provided");
}
$conn->close();
?> 