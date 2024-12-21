<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include "db.php";

// Get date range from request, default to last 30 days
$end_date = date('Y-m-d');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : $end_date;

// Get revenue by car
$revenue_by_car_sql = "
    SELECT 
        c.name,
        COUNT(DISTINCT b.id) as total_bookings,
        COALESCE(SUM(
            CASE 
                WHEN b.pickup_date IS NOT NULL AND b.return_date IS NOT NULL 
                THEN c.price_per_day * DATEDIFF(b.return_date, b.pickup_date)
                ELSE 0 
            END
        ), 0) as revenue
    FROM cars c
    LEFT JOIN bookings b ON c.id = b.car_id 
        AND b.pickup_date BETWEEN ? AND ?
        AND b.status != 'cancelled'  /* Exclude cancelled bookings */
    GROUP BY c.id, c.name
    HAVING total_bookings > 0  /* Only show cars with bookings */
    ORDER BY revenue DESC";

$stmt = $conn->prepare($revenue_by_car_sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$revenue_by_car = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get bookings by status
$bookings_by_status_sql = "
    SELECT 
        COALESCE(status, 'pending') as status,
        COUNT(*) as count
    FROM bookings
    WHERE pickup_date BETWEEN ? AND ?
    GROUP BY status
    HAVING count > 0";

$stmt = $conn->prepare($bookings_by_status_sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$bookings_by_status = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Car Rental</title>
    <link rel="stylesheet" href="style/dashboard.css">
    <link rel="stylesheet" href="style/reports.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    .reports-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.report-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.report-card.full-width {
    grid-column: 1 / -1;
}

.date-filter {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.date-filter form {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.date-filter label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
    color: #333;
}

.date-filter input[type="date"] {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    color: #333;
    background-color: #f8f9fa;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.date-filter input[type="date"]:focus {
    outline: none;
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
}

.date-filter .action-btn {
    padding: 8px 20px;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s;
}

.date-filter .action-btn:hover {
    background-color: #c82333;
}

.reports-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.reports-table th,
.reports-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.reports-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

canvas {
    width: 100% !important;
    height: 300px !important;
}
</style>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="user-info">
                <h3>Admin Panel</h3>
                <p><?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            
            <a href="dashboard.php" class="menu-item">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="manage_users.php" class="menu-item">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="manage_cars.php" class="menu-item">
                <i class="fas fa-car"></i> Manage Cars
            </a>
            <a href="manage_bookings.php" class="menu-item">
                <i class="fas fa-calendar-check"></i> Manage Bookings
            </a>
            <a href="reports.php" class="menu-item active">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="feedback_dashboard.php" class="menu-item">
                <i class="fas fa-chart-bar"></i> Feedback
            </a>
            <a href="settings.php" class="menu-item">
                <i class="fas fa-cog"></i> Settings
            </a>
            <form action="logout.php" method="POST" style="margin-top: auto;">
                <button type="submit" class="menu-item" style="width: 100%; text-align: left; border: none; background: none; color: inherit;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
        <div class="main-content">
            <h1>Reports</h1>
            
            <div class="date-filter">
                <form method="GET">
                    <label>From: <input type="date" name="start_date" value="<?php echo $start_date; ?>"></label>
                    <label>To: <input type="date" name="end_date" value="<?php echo $end_date; ?>"></label>
                    <button type="submit" class="action-btn">Filter</button>
                </form>
            </div>

            <div class="reports-grid">
                <div class="report-card">
                    <h2>Revenue by Car</h2>
                    <canvas id="revenueChart"></canvas>
                </div>
                
                <div class="report-card">
                    <h2>Bookings by Status</h2>
                    <canvas id="statusChart"></canvas>
                </div>
                
                <div class="report-card full-width">
                    <h2>Detailed Revenue Report</h2>
                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>Car</th>
                                <th>Total Bookings</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($revenue_by_car as $car): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($car['name']); ?></td>
                                <td><?php echo $car['total_bookings']; ?></td>
                                <td>₱<?php echo number_format($car['revenue'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($revenue_by_car, 'name')); ?>,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: <?php echo json_encode(array_column($revenue_by_car, 'revenue')); ?>,
                    backgroundColor: '#dc3545'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($bookings_by_status, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($bookings_by_status, 'count')); ?>,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html> 