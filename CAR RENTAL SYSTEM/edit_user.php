<?php
session_start();

// Check authentication and admin status
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $active = isset($_POST['active']) ? 1 : 0;
    
    // Update user information
    $update_sql = "UPDATE users SET name = ?, email = ?, active = ? WHERE id = ? AND is_admin = 0";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssii", $name, $email, $active, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User updated successfully";
        header("Location: manage_users.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error updating user";
    }
}

// Get user data
$user_sql = "SELECT * FROM users WHERE id = ? AND is_admin = 0 LIMIT 1";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    $_SESSION['error_message'] = "User not found";
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/edit_user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .edit-form-container {
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 20px auto;
}

.edit-user-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 500;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="email"] {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
}

.checkbox-group {
    flex-direction: row;
    align-items: center;
    gap: 10px;
}

.checkbox-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.save-btn, .cancel-btn {
    padding: 10px 20px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    text-decoration: none;
}

.save-btn {
    background-color: #4CAF50;
    color: white;
}

.save-btn:hover {
    background-color: #45a049;
}

.cancel-btn {
    background-color: #f8f9fa;
    color: #666;
    border: 1px solid #ddd;
}

.cancel-btn:hover {
    background-color: #e9ecef;
}
</style>
<body>
    <div class="dashboard-container">
        <!-- Include your sidebar here (same as manage_users.php) -->
        
        <div class="main-content">
            <h1>Edit User</h1>
            
            <div class="edit-form-container">
                <form method="POST" class="edit-user-form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="active" <?php echo $user['active'] ? 'checked' : ''; ?>>
                            Active Status
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="save-btn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="manage_users.php" class="cancel-btn">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 