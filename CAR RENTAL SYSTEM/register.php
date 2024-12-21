<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } else {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Email already registered";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (first_name, last_name, email, phone, address, password) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $address, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['registration_success'] = true;
                header("Location: form2.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
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
    <title>Register - Car Rental</title>
    <link rel="stylesheet" href="register.css">
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

.register-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.register-form {
    background: white;
    backdrop-filter: blur(10px);
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.register-form h2 {
    color: #333;
    text-align: center;
    margin-bottom: 10px;
}

.subtitle {
    color: black;
    text-shadow: 5px 5px 7px rgba(0, 0, 0, 0.10);
    text-align: center; 
}

.form-group {
    position: relative;
    margin-bottom: 1.2rem;
}

.form-group i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.form-group input {
    width: 100%;
    padding: 12px 40px;
    border: 1px solid #666161;
    border-radius: 5px;
    font-size: 1rem;
    box-sizing: border-box;
}

.form-group input:focus {
    outline: none;
    border-color: #da2b2b;
}

.register-btn {
    width: 100%;
    padding: 12px;
    background-color: #f11010;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 1rem;
}

.register-btn:hover {
    background-color: #970606;
}

.login-link {
    text-align: center;
    margin-top: 1rem;
    color: #666;
}

.login-link a {
    color: #e61c1c;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

.error-message {
    background-color: #ffebee;
    color: #c62828;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 1rem;
    text-align: center;
}
</style>
<body>
    <div class="register-container">
        <form class="register-form" method="POST" action="">
            <h2>Create Account</h2>
            <p class="subtitle">Please fill in your details</p>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="first_name" placeholder="First Name" required>
            </div>

            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>

            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="text" name="phone" placeholder="Phone Number" required>
            </div>
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="text" name="address" placeholder="Address" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="register-btn">Create Account</button>

            <div class="login-link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </form>
    </div>
</body>
</html>