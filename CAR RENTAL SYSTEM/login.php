<?php
session_start();
include "db.php";

if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT id, email, password, role, is_admin FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    error_log("Query executed for email: " . $email);
    error_log("Password received (length): " . strlen($password));
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            if ($user['password'] === $password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_password, $user['id']);
                $update_stmt->execute();
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = (bool)$user['is_admin'];
            
            error_log("Session data set - Role: " . $_SESSION['role'] . ", Is Admin: " . $_SESSION['is_admin']);
            
            if ($user['is_admin'] === 1 || strtolower($user['role']) == 'admin') {
                error_log("Redirecting to admin dashboard");
                header("Location: dashboard.php");
                exit();
            } else {
                error_log("Redirecting to user dashboard");
                header("Location: form2.php");
                exit();
            }
        } else {
            $error = "Invalid password";
            error_log("Password verification failed");
        }
    } else {
        $error = "User not found";
        error_log("No user found with email: " . $email);
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Car Rental</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
   body {
    background-image: url('car2.webp');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
    margin: 0;
    font-family: 'Arial', sans-serif;
}

.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-form {
    background: white;
    backdrop-filter: blur(10px);
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.login-form h2 {
    color: #333;
    text-align: center;
    margin-bottom: 10px;
}

.subtitle {
    color: white;
    text-shadow: 5px 5px 7px rgba(0, 0, 0, 0.10);
    text-align: center; 
}

.form-group {
    position: relative;
    margin-bottom: 20px;
}

.form-group i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.form-group input {
    width: 70%;
    padding: 12px 60px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-group input:focus {
    border-color: #ff0000;
    outline: none;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    font-size: 14px;
}

.forgot-password {
    color: #ff0000;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

.login-btn {
    width: 100%;
    padding: 12px;
    background: #ff0000;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.login-btn:hover {
    background: #cc0000;
}

.register-link {
    text-align: center;
    margin-top: 20px;
    font-size: 14px;
}

.register-link a {
    color: #ff0000;
    text-decoration: none;
}

.register-link a:hover {
    text-decoration: underline;
}

.error-message {
    background: #ffe6e6;
    color: #ff0000;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    text-align: center;
}
</style>

</style>
<body>
    <div class="login-container">
        <form class="login-form" method="POST" action="">
            <h2>Welcome to MTT CarRental</h2>
            <p class="subtitle">Please login to your account</p>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>

            <div class="register-link">
                Don't have an account? <a href="register.php">Sign Up</a>
            </div>
        </form>
    </div>
</body>
</html>