<?php
session_start();

// Check if user is logged in
if (!isset($_POST['rentalId']) || !is_numeric($_POST['rental_id'])){
    http_response_code(400);
    echo json_encode(['error' => 'Invalid rental ID']);
    exit();
}

include "db.php";

// Validate rental ID
if (!isset($_POST['rentalId']) || !is_numeric($_POST['rental_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid rental ID']);
    exit();
}

$rental_id = $_POST['rentalId'];
$user_id = $_SESSION['user_id'];

// Verify rental belongs to user and is active
$check_sql = "SELECT id FROM rentals WHERE id = ? AND user_id = ? AND status = 'active'";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $rental_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Rental not found or cannot be cancelled']);
    exit();
}

// Update rental status to cancelled
$update_sql = "UPDATE rentals SET status = 'cancelled' WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $rental_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to cancel rental']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rentals - Car Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="rentals.css">
</head>
<style>
    /* Reuse base styles from dashboard */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', Arial, sans-serif;
    background-color: #f8fafc;
}

/* Dashboard container and sidebar styles (same as dashboard.php) */
.dashboard-container {
    display: flex;
    min-height: 100vh;
}

/* Add styles specific to rentals page */
.rentals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 24px;
    margin-top: 24px;
}

.rental-card {
    background-color: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.rental-card:hover {
    transform: translateY(-4px);
}

.rental-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.rental-header h3 {
    color: #1e293b;
    font-size: 1.1rem;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.active .status-badge {
    background-color: #dcfce7;
    color: #166534;
}

.completed .status-badge {
    background-color: #e0f2fe;
    color: #075985;
}

.cancelled .status-badge {
    background-color: #fee2e2;
    color: #991b1b;
}

.rental-details {
    color: #64748b;
    font-size: 0.875rem;
}

.rental-details p {
    margin-bottom: 8px;
}

.rental-details i {
    width: 20px;
    margin-right: 8px;
}

.cancel-btn {
    margin-top: 16px;
    background-color: #ef4444;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.cancel-btn:hover {
    background-color: #dc2626;
}

.no-rentals {
    grid-column: 1 / -1;
    text-align: center;
    padding: 48px;
    background-color: white;
    border-radius: 16px;
    color: #64748b;
}

.no-rentals i {
    margin-bottom: 16px;
}

.browse-cars-btn {
    display: inline-block;
    margin-top: 16px;
    background-color: #3b82f6;
    color: white;
    text-decoration: none;
    padding: 8px 24px;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.browse-cars-btn:hover {
    background-color: #2563eb;
}
</style>
<body>
    <div class="dashboard-container">
        <!-- Sidebar (same as dashboard) -->
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
            <a href="rentals.php" class="menu-item active">
                <i class="fas fa-car"></i> My Rentals
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <div class="main-content">
            <div class="welcome-section">
                <h1>My Rentals</h1>
                <p>View and manage your car rentals</p>
            </div>

            <div class="rentals-grid">
                <?php if ($rentals->num_rows > 0): ?>
                    <?php while ($rental = $rentals->fetch_assoc()): ?>
                        <div class="rental-card <?php echo $rental['status']; ?>">
                            <div class="rental-header">
                                <h3><?php echo htmlspecialchars($rental['brand'] . ' ' . $rental['model'] . ' ' . $rental['year']); ?></h3>
                                <span class="status-badge"><?php echo ucfirst($rental['status']); ?></span>
                            </div>
                            <div class="rental-details">
                                <p><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($rental['start_date'])); ?> - <?php echo date('M d, Y', strtotime($rental['end_date'])); ?></p>
                                <p><i class="fas fa-dollar-sign"></i> Total: $<?php echo number_format($rental['total_cost'], 2); ?></p>
                            </div>
                            <?php if ($rental['status'] === 'active'): ?>
                                <button class="cancel-btn" onclick="cancelRental(<?php echo $rental['id']; ?>)">Cancel Rental</button>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-rentals">
                        <i class="fas fa-car fa-3x"></i>
                        <p>You haven't made any rentals yet.</p>
                        <a href="cars.php" class="browse-cars-btn">Browse Cars</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function cancelRental(rentalId) {
            if (confirm('Are you sure you want to cancel this rental?')) {
                // Add AJAX call to cancel rental
                // You'll need to create a cancel_rental.php endpoint
            }
        }
        function cancelRental(rentalId) {
    if (confirm('Are you sure you want to cancel this rental?')) {
        fetch('cancel_rental.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'rental_id=' + rentalId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the page to show updated status
                window.location.reload();
            } else {
                alert('Failed to cancel rental: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error cancelling rental: ' + error);
        });
    }
}
    </script>
</body>
</html>