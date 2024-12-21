<?php
session_start();

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $message = 'All fields are required';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format';
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'Email already exists';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (email, password, is_admin) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssi", $email, $hashed_password, $is_admin);
            
            if ($insert_stmt->execute()) {
                $message = 'User added successfully';
                // Optionally redirect back to dashboard or users list
                header("Location: manage_users.php");
                exit();
            } else {
                $message = 'Error creating user: ' . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/add_user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    .form-container {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: 2rem auto;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.form-group input[type="email"],
.form-group input[type="password"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.form-group.checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group.checkbox label {
    margin-bottom: 0;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-primary,
.btn-secondary {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s;
}

.btn-primary {
    background: #dc3545;
    color: white;
}

.btn-primary:hover {
    background: #c82333;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.message {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 5px;
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
<body>
    <div class="dashboard-container">
        <!-- Include your sidebar here (same as dashboard.php) -->
        <div class="main-content">
            <h1>Add New User</h1>
            
            <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="add_user.php">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group checkbox">
                        <input type="checkbox" id="is_admin" name="is_admin">
                        <label for="is_admin">Admin User</label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Add User</button>
                        <button type="button" class="btn-secondary" onclick="location.href='dashboard.php'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 