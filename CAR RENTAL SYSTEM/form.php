<?php
include "db.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $persons = mysqli_real_escape_string($conn, $_POST['persons']);
    $car = mysqli_real_escape_string($conn, $_POST['car']);
    $pickup_location = mysqli_real_escape_string($conn, $_POST['pickup_location']);
    $pickup_date = mysqli_real_escape_string($conn, $_POST['pickup_date']);
    $pickup_time = mysqli_real_escape_string($conn, $_POST['pickup_time']);
    $return_date = mysqli_real_escape_string($conn, $_POST['return_date']);
    
    $sql = "INSERT INTO bookings (name, phone, persons, car, pickup_location, pickup_date, pickup_time, return_date) 
            VALUES ('$name', '$phone', '$persons', '$car', '$pickup_location', '$pickup_date', '$pickup_time', '$return_date')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Booking successful!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
function validateBookingData($data) {
    $errors = [];
    
    // Validate date ranges
    $pickup = strtotime($data['pickup_date']);
    $return = strtotime($data['return_date']);
    $today = strtotime(date('Y-m-d'));
    
    if ($pickup < $today) {
        $errors[] = "Pickup date cannot be in the past";
    }
    
    if ($return < $pickup) {
        $errors[] = "Return date must be after pickup date";
    }
    
    // Validate phone number
    if (!preg_match("/^[0-9]{10}$/", $data['phone'])) {
        $errors[] = "Invalid phone number format";
    }
    
    return $errors;
}
$query = "SELECT id, name, price_per_day, model_year, brand, passengers, transmission, image_url, category 
         FROM cars 
         ORDER BY price_per_day ASC";
$result = mysqli_query($conn, $query);
$cars = mysqli_fetch_all($result, MYSQLI_ASSOC);


?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental Booking</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/style/style.css">

</head>
<style>
    /* General Styles */
body {
    background-image: url('car2.webp');
    font-family: 'Arial', sans-serif;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    min-height: 100vh;
    margin: 0;
    padding-top: 80px;
}
.hero {
   
    background-size: cover;
    background-position: center;
    min-height: 10px;
    position: relative;
    color: white;
    padding: 4rem;
    display: flex;
    align-items: center;
}

.hero-content {
    max-width: 600px;
}

.hero h1 {
    font-size: 40px;
    font-weight: bold;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: white;
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

/* Responsive Design */
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

/* Header Styles */
h1 {
    color: #333;
    margin-bottom: 30px;
    font-weight: bold;
    position: relative;
    padding-bottom: 15px;
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

/* Form Styles */
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

/* Button Styles */
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

/* Steps Section */
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
    position: fixed; /* Add this */
    top: 0; /* Add this */
    left: 0; /* Add this */
    right: 0; /* Add this */
    z-index: 1000; /* Add this */
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
/* Responsive Adjustments */
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

/* Form Validation Styles */
.form-control:invalid {
    border-color: #dc3545;
}

.form-control:valid {
    border-color: #28a745;
}

/* Placeholder Styles */
::placeholder {
    color: #999;
    opacity: 1;
}

/* Date and Time Input Styles */
input[type="date"],
input[type="time"] {
    cursor: pointer;
}

/* Hover Effects */
.form-control:hover {
    border-color: #aaa;
}

/* Animation for Submit Button */
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
<body>
    

<nav class="navbar">
    <div class="logo">
        <span>MTT</span><span>CarRental</span>
    </div>
    
    <div class="nav-links">
        <a href="form.php" class="active">Home</a>
        <a href="cars.php">Cars</a>
        <a href="login.php">Booking</a>
        <a href="#">Contact us</a>
        <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Search cars...">
        <i class="search-icon fas fa-search"></i>
</div>
        <a href="login.php" type="submit" class="book-btn">Book Now</a>
        
    </div>
</nav>
<section class="hero">
    <div class="hero-content">
        <h1>
            ELEVATE YOUR<br>
            <span class="text-black">JOURNEY</span> WITH<br>
            <span class="text-red">MTT</span> CAR RENTAL
        </h1>
        
        
        <button class="book-btn" onclick="document.querySelector('.collection-header').scrollIntoView({ behavior: 'smooth' })">
            🚗 BOOK A CAR
        </button>
    </div>
</section>
     <div class="collection-header">
        <br>
        <span class="collection-label"><i class="fas fa-car"></i> OUR COLLECTIONS</span>
        <h1><span class="text-red">Explore Our Top-Rated Vehicles</span></h1>
    </div>
    <div class="car-grid">
        <?php foreach($cars as $car): ?>
        <div class="car-card">
        <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
         alt="<?php echo htmlspecialchars($car['name']); ?>"
         onerror="this.src='images/h.webp'"
         class="car-image">
                <div class="car-info">
                    <div class="price-book">
                        <span class="price">₱<?php echo htmlspecialchars($car['price_per_day']); ?>/day</span>
                        <a href="login.php" class="book-btn">Book</a>
                    </div>
                    <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                    <div class="car-details">
                        <div class="detail">
                            <i class="far fa-calendar"></i>
                            <span><?php echo htmlspecialchars($car['model_year']); ?></span>
                        </div>
                        <div class="detail">
                            <i class="fas fa-car"></i>
                            <span><?php echo htmlspecialchars($car['brand']); ?></span>
                        </div>
                        <div class="detail">
                            <i class="fas fa-users"></i>
                            <span><?php echo htmlspecialchars($car['passengers']); ?> Person</span>
                        </div>
                        <div class="detail">
                            <i class="fas fa-cog"></i>
                            <span><?php echo htmlspecialchars($car['transmission']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <script>
    document.querySelector('.search-input').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cars = document.querySelectorAll('.car-card');
        
        cars.forEach(car => {
            const carName = car.querySelector('h3').textContent.toLowerCase();
            const carBrand = car.querySelector('.fas.fa-car').nextElementSibling.textContent.toLowerCase();
            const carYear = car.querySelector('.far.fa-calendar').nextElementSibling.textContent.toLowerCase();
            const carTransmission = car.querySelector('.fas.fa-cog').nextElementSibling.textContent.toLowerCase();
            
            if (carName.includes(searchTerm) || 
                carBrand.includes(searchTerm) || 
                carYear.includes(searchTerm) || 
                carTransmission.includes(searchTerm)) {
                car.style.display = '';
            } else {
                car.style.display = 'none';
            }
        });
    });
</script>
</body>

</body>
</html>