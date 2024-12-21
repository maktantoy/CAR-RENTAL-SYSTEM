<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    $update_sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $first_name, $last_name, $phone, $address, $user_id);
    
    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully!";
        // Refresh user data
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $error_message = "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Car Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Include the same CSS as dashboard.php */
        /* ... existing styles ... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f8fafc;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background-color: #1e293b;
            color: white;
            padding: 24px;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }

        .sidebar .user-info {
            padding: 24px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 24px;
        }

        .sidebar .user-info h3 {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .sidebar .menu-item {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            color: #e2e8f0;
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 8px;
            margin-bottom: 4px;
        }

        .sidebar .menu-item:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateX(4px);
        }

        .sidebar .menu-item i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            padding: 32px;
        }

        .welcome-section {
            background-color: white;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            margin-bottom: 32px;
        }

        .welcome-section h1 {
            font-size: 1.875rem;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .welcome-section p {
            color: #64748b;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }

        .stat-card {
            background-color: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-card h3 {
            color: #64748b;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .stat-card p {
            color: #1e293b;
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Additional styles for profile page */
        .profile-form {
            background-color: white;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1e293b;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .submit-btn {
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #2563eb;
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Same sidebar as dashboard.php -->
        <div class="sidebar">
            <div class="user-info">
                <h3>Welcome,</h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <a href="dashboard.php" class="menu-item">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="profile.php" class="menu-item">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="rentals.php" class="menu-item">
                <i class="fas fa-car"></i> My Rentals
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="form2.php" class="menu-item">
                <i class="fas fa-calendar-plus"></i> Book Now
            </a>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <div class="main-content">
            <div class="welcome-section">
                <h1>Profile</h1>
                <p>Manage your personal information</p>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="profile-form">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name"
                               value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address"
                               value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="submit-btn">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>