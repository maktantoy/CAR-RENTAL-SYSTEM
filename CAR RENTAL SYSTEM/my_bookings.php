<?php
include "db.php";
session_start();

// Check if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

// Get the logged-in user's name
$user_name = $_SESSION['name'] ?? '';
$user_id = $_SESSION['user_id']; // Now safe to access

// Fetch bookings for the logged-in user
$query = "SELECT b.*, c.name AS car_name 
          FROM bookings b 
          LEFT JOIN cars c ON b.car_id = c.id 
          WHERE b.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // Bind user_id as integer
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .booking-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .section-title {
            color: #333;
            border-bottom: 2px solid #ff0000;
            padding-bottom: 5px;
            margin-bottom: 15px;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
            color: #666;
        }

        .info-value {
            flex: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.9em;
            background-color: #ffc107;
            color: #000;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn-edit {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-edit:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        .btn-back:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .btn i {
            margin-right: 5px;
        }
        body {
    background-image: url('car2.webp');
    font-family: 'Arial', sans-serif;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding-top: 80px;
    min-height: 80vh;
    margin: 0;
    color: #333;
}
.hero {
    background-image: linear-gradient(rgba(1, 1, 1, 0.5), rgba(1, 1, 0, 0.5));
    background-size: auto;
    background-position: center;
    min-height: 100vh;
    position: relative;
    color: white;
    padding: 5rem;
    display: flex;
    align-items: center;
}

.hero-content {
    max-width: 600px;
}

.hero h1 {
    font-size: 40px;
    font-weight: bolder;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: white;
}
.collection-header {
    text-align: center;
    margin-bottom: 3rem;
}

.collection-label {
    background-color: #f8f9fa;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    color: #ff0000;
    display: inline-block;
    margin-bottom: 1rem;
}

.collection-label i {
    margin-right: 0.5rem;
}

.collection-header h1 {
    font-size: 2.5rem;
    color: #1a1a1a;
    margin-top: 0.5rem;
}
.logo span:first-child {
    color: #ff0000;
    font-weight: bold;
    font-size: 24px;
}

.logo span:last-child {
    color: white;
    font-weight: bold;
    font-size: 24px;
}
.car-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.car-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.car-card:hover {
    transform: translateY(-5px);
}

.car-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.car-info {
    padding: 1.5rem;
}

.price-book {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.price {
    font-size: 1.25rem;
    font-weight: bold;
    color: #1a1a1a;
}

.book-btn {
    background-color: #ff0000;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.book-btn:hover {
    background-color: #cc0000;
}

.car-info h3 {
    font-size: 1.5rem;
    color: #1a1a1a;
    margin-bottom: 1rem;
}

.car-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
}

.detail i {
    color: #ff0000;
    font-size: 1rem;
}

.text-red {
    color: #ff0000;
}

.hero-description {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.pricing {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
}

.price {
    color: #ff0000;
    font-size: 2rem;
    font-weight: bold;
    margin-left: 0.5rem;
}

.book-btn {
    background-color: #ff0000;
    color: white;
    border: none;
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.book-btn:hover {
    background-color: #cc0000;
}

.features-badge {
    position: absolute;
    right: 4rem;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    gap: 2rem;
}

.discount-circle {
    background-color: #ff0000;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 1rem;
}

.discount-circle span:first-child {
    font-size: 2rem;
    font-weight: bold;
}

.discount-circle span:last-child {
    font-size: 0.9rem;
}

.feature-list {
    list-style: none;
    padding: 0;
    background-color: rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
    border-radius: 10px;
}

.feature-list li {
    margin-bottom: 0.8rem;
    font-size: 1.1rem;
}

.feature-list li:last-child {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .hero {
        padding: 2rem;
        text-align: center;
    }

    .hero h1 {
        font-size: 3rem;
    }

    .features-badge {
        position: static;
        transform: none;
        flex-direction: column;
        margin-top: 2rem;
    }
}
.container {
    background-color: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

h1 {
    color: #dc3545;
    margin-bottom: 30px;
    font-weight: bold;
    position: relative;
    padding-bottom: 15px;
    text-align: center;
}

h1:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background-color: #dc3545;
}

.form-control {
    height: 50px;
    border-radius: 5px;
    border: 1px solid #ddd;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

select.form-control {
    appearance: none;
    -webkit-appearance: none;
    background-image: url('car2.webp');
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1em;
}

.btn-danger {
    height: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background-color: #dc3545;
    border: none;
    transition: all 0.3s ease;
}

.btn-danger:hover {
    background-color: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.mt-4 h3 {
    font-size: 1.2rem;
    color: #555;
    position: relative;
    padding: 20px 0;
}

.mt-4 .col-md-4 {
    position: relative;
}

.mt-4 .col-md-4:not(:last-child):after {
    content: '';
    position: absolute;
    top: 50%;
    right: 0;
    width: 50px;
    height: 2px;
    background: #ddd;
    transform: translateX(50%);
}
.navbar {
    background-color: rgba(0, 0, 0, 0.8);
    padding: 15px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: fixed;    
    top: 0;     
    left: 0;    
    right: 0;   
    z-index: 1000;  
}

.logo {
    display: flex;
    align-items: center;
}

.logo span:first-child {
    color: #ff0000;
    font-weight: bold;
    font-size: 24px;
}

.logo span:last-child {
    color: white;
    font-weight: bold;
    font-size: 24px;
}

.nav-links {
    display: flex;
    gap: 30px;
    align-items: center;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    transition: color 0.3s ease;
}

.nav-links a:hover {
    color: #ff0000;
}

.nav-links a.active {
    color: #ff0000;
}

.book-now-btn {
    background-color: #ff0000;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.book-now-btn:hover {
    background-color: #cc0000;
}

.search-icon {
    color: white;
    margin-right: 20px;
    cursor: pointer;
}
.car-collection {
    padding: 40px 0;
}
.car-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}
.car-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 300px;
    overflow: hidden;
}
.car-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}
.car-card a:hover img {
    opacity: 0.9;
    cursor: pointer;
}
.car-details {
    padding: 15px;
}
.price-book {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    border-top: 1px solid #eee;
}
.price {
    font-size: 1.2em;
    font-weight: bold;
}
.book-btn {
    background: #ff0000;
    color: white;
    padding: 8px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.car-specs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 10px;
}
.spec-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9em;
}
.search-container {
    display: flex;
    align-items: center;
    position: relative;
}

.search-input {
    padding: 8px 32px 8px 12px;
    border: 1px solid #ddd;
    border-radius: 20px;
    outline: none;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.3);
}

.search-icon {
    position: absolute;
    right: 10px;
    cursor: pointer;
    color: #666;
}

.search-icon:hover {
    color: #007bff;
}
.car-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}
.clear-btn {
    background-color: #ff0000;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 8px 15px;
    margin-left: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.clear-btn:hover {
    background-color: #cc0000;
}
.search-icon {
    color: #666;
    font-size: 16px;
    cursor: pointer;
    transition: color 0.3s ease;
    padding: 8px;
}

.search-icon:hover {
    color: #ff0000;
}

.search-container {
    position: relative;
    display: flex;
    align-items: center;
    margin-left: 10px;
}

.search-icon {
    position: absolute;
    right: 45px;    
}


.search-input {
    padding-right: 70px;
}
.navbar {
    background-color: rgba(0, 0, 0, 0.8);
    padding: 15px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: fixed;    
    top: 0;     
    left: 0;    
    right: 0;   
    z-index: 1000;  
}

.logo {
    display: flex;
    align-items: center;
}

.logo span:first-child {
    color: #ff0000;
    font-weight: bold;
    font-size: 24px;
}

.logo span:last-child {
    color: white;
    font-weight: bold;
    font-size: 24px;
}

.nav-links {
    display: flex;
    gap: 30px;
    align-items: center;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    transition: color 0.3s ease;
}

.nav-links a:hover {
    color: #ff0000;
}

.nav-links a.active {
    color: #ff0000;
}

.book-now-btn {
    background-color: #ff0000;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.book-now-btn:hover {
    background-color: #cc0000;
}
/* Add this CSS to style the table in your my_bookings.php file */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

th, td {
    padding: 15px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
    color: #333;
    font-weight: bold;
}

tbody tr:hover {
    background-color: #f1f1f1;
    transition: background-color 0.3s ease;
}

@media (max-width: 768px) {
    .container {
        padding: 20px;
    }
    
    .mt-4 .col-md-4:after {
        display: none;
    }
    
    h1 {
        font-size: 1.8rem;
    }
}


.form-control:invalid {
    border-color: #dc3545;
}

.form-control:valid {
    border-color: #28a745;
}


::placeholder {
    color: #999;
    opacity: 1;
}


input[type="date"],
input[type="time"] {
    cursor: pointer;
}


.form-control:hover {
    border-color: #aaa;
}


@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.02);
    }
    100% {
        transform: scale(1);
    }
}

.btn-danger:active {
    animation: pulse 0.3s ease-in-out;
}
    </style>
</head>
<body>
    <!-- Replace navbar include with actual navbar code -->
    <nav class="navbar">
    <div class="logo">
        <span>MTT</span><span>CarRental</span>
    </div>
    
    <div class="nav-links">
        <a href="form2.php">Home</a>
        <a href="cars2.php">Cars</a>
        <a href="booking.php">Booking</a>
        <a href="my_bookings.php" class="active">My Bookings</a>
        <a href="feedback_form.php">Contact us</a>
        <a href="form.php" type="submit" class="book-btn">Logout</a>  
    </div>
</nav>

    <div class="container mt-5">
    <h1>My Bookings</h1>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Car</th>
                <th>Pickup Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                    <td><?php echo htmlspecialchars($booking['car_name']); ?></td>
                    <td><?php echo htmlspecialchars($booking['pickup_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['return_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>