<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // First, check if the user exists and get their current status
    $check_sql = "SELECT active FROM users WHERE id = ? AND is_admin = 0";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $new_status = $user['active'] ? 0 : 1;  // Toggle the status
        
        // Update the user's status
        $update_sql = "UPDATE users SET active = ? WHERE id = ? AND is_admin = 0";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_status, $user_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['success_message'] = "User status updated successfully.";
        } else {
            $_SESSION['error_message'] = "Error updating user status.";
        }
    } else {
        $_SESSION['error_message'] = "User not found.";
    }
}

// Redirect back to manage_users.php
header("Location: manage_users.php");
exit();
?> 