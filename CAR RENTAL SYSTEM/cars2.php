<?php
// Database connection
include "db.php";

$query = "SELECT id, name, price_per_day, model_year, brand, passengers, transmission, image_url, category 
         FROM cars 
         ORDER BY price_per_day ASC";
$result = mysqli_query($conn, $query);
$cars = mysqli_fetch_all($result, MYSQLI_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rentals</title>
    <link rel="stylesheet" href="style/cars.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    body{
        padding-top: 80px;
    }
    .search-container {
    display: flex;
    align-items: center;
    position: relative;
}

.car-image img {
    width: 100%;
    height: 100%;
    border-radius: 10px;
    object-fit: fill;
}

.car-card a:hover img {
    opacity: 0.9;
    cursor: pointer;
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
.car-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Car Card Styling */
.car-card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.5s ease;
}

.car-card:hover {
    transform: translateY(-5px);
}

.car-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    object-position: center;
    transition: object-position 0.3s ease;
}

.car-info {
    padding: 1.5rem;
}
</style>
<body>
<nav class="navbar">
    <div class="logo">
        <span>MTT</span><span>CarRental</span>
    </div>
    
    <div class="nav-links">
        <a href="form2.php">Home</a>
        <a href="cars.php" class="active">Cars</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="#">Contact us</a>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search cars...">
            <i class="search-icon fas fa-search"></i>
        </div>
        <a href="booking.php" type="submit" class="book-btn">Book Now</a>
    </div>
</nav>
    <div class="collection-header">
        <br>
        <span class="collection-label"><i class="fas fa-car"></i> OUR COLLECTIONS</span>
        <h1>Explore Our Top-Rated Vehicles</h1>
    </div>

    <div class="car-grid">
    <?php foreach($cars as $car): ?>
        <div class="car-card">
            <a href="car_details.php?car_id=<?php echo htmlspecialchars($car['id']); ?>">
                <img src="<?php echo htmlspecialchars($car['image_url']); ?>"
                 alt="<?php echo htmlspecialchars($car['name']); ?>"
                 onerror="this.src='images/default-car.jpg'"
                 class="car-image">
            </a>
            <div class="car-info">
                <div class="price-book">
                    <span class="price">₱<?php echo htmlspecialchars($car['price_per_day']); ?>/day</span>
                    <a href="booking.php?car_id=<?php echo htmlspecialchars($car['id']); ?>" class="book-btn">Book</a>
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
</html>