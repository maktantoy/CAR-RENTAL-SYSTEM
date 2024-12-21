<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Guest';

$cars_result = $conn->query("SELECT id, name, brand FROM cars WHERE availability = 'available'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['car_id'])) {
        $car_id = (int)$_POST['car_id'];
        $pickup_location = $_POST['pickup_location'];
        $pickup_date = $_POST['pickup_date'];
        $pickup_time = $_POST['pickup_time'];
        $return_date = $_POST['return_date'];
        $status = 'pending';

        $stmt = $conn->prepare("INSERT INTO bookings (user_id, car_id, pickup_location, pickup_date, pickup_time, return_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssss", $user_id, $car_id, $pickup_location, $pickup_date, $pickup_time, $return_date, $status);
        
        if ($stmt->execute()) {
            header("Location: my_bookings.php");
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error: Car ID is not set.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Booking - Car Rental</title>
    <style>
        body {
            background-image: url('car2.webp');
            background-size: cover;
            padding-top: 80px;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
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
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <span>MTT</span><span>CarRental</span>
        </div>
        <div class="nav-links">
            <a href="form2.php">Home</a>
            <a href="cars2.php">Cars</a>
            <a href="booking.php" class="active">Booking</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="#">Contact us</a>
            <a href="form.php" type="submit" class="book-btn">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Create Booking</h1>
        <form method="POST" action="">
            <input type="hidden" name="car_id" value="<?php echo htmlspecialchars($car_id); ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" placeholder="Your name" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" name="phone" class="form-control" placeholder="Phone number" required>
            </div>
            <div class="form-group">
                <label for="persons">Number of Persons</label>
                <input type="number" name="persons" class="form-control" placeholder="Number of Persons" required>
            </div>
            <div class="form-group">
                <label for="car_id">Select Car:</label>
                <select name="car_id" class="form-control" required>
                    <option value="">Select a car</option>
                    <?php while ($car = $cars_result->fetch_assoc()): ?>
                        <option value="<?php echo $car['id']; ?>"><?php echo htmlspecialchars($car['name'] . ' - ' . $car['brand']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pickup_location">Pickup Location</label>
                <input type="text" name="pickup_location" class="form-control" placeholder="Pickup Location" required>
            </div>
            <div class="form-group">
                <label for="pickup_date">Pickup Date</label>
                <input type="date" name="pickup_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="pickup_time">Pickup Time</label>
                <input type="time" name="pickup_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="return_date">Return Date</label>
                <input type="date" name="return_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger btn-block">Create Booking</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
