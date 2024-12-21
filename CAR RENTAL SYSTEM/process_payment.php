<?php
session_start();
include "db.php"; // Database connection

$receipt = null; // Initialize receipt variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Assuming user is logged in
    $amount = $_POST['amount'];
    $paymentMethod = $_POST['paymentMethod'];

    // Insert payment into the database
    $stmt = $conn->prepare("INSERT INTO payments (user_id, amount, payment_method) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $user_id, $amount, $paymentMethod);
    
    if ($stmt->execute()) {
        // Prepare receipt details
        $receipt = [
            'amount' => $amount,
            'paymentMethod' => $paymentMethod,
            'date' => date('Y-m-d H:i:s')
        ];
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/style/style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .payment-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }

        p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #666;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            text-align: left;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            border-color: #ff0000;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #ff0000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #cc0000;
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

        .search-container {
            display: flex;
            align-items: center;
        }

        .search-input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-icon {
            color: white;
            cursor: pointer;
        }

        .receipt {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            text-align: left;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <span>MTT</span><span>CarRental</span>
        </div>
        <div class="nav-links">
            <a href="#" class="active">Home</a>
            <a href="cars2.php">Cars</a>
            <a href="booking.php">Booking</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="contact_us.php">Contact us</a>
            <div class="search-container">
                <input type="text" id="searchInput" class="search-input" placeholder="Search cars...">
                <i class="search-icon fas fa-search"></i>
            </div>
            <a href="form.php" type="submit" class="book-btn">Logout</a>  
        </div>
    </nav>
    <div class="payment-container">
        <h1>Welcome to MTT CarRental</h1>
        <p>Please make a payment</p>
        <form id="paymentForm" method="POST" action="process_payment.php">
            <label for="paymentMethod">Payment Method:</label>
            <select id="paymentMethod" name="paymentMethod" required>
                <option value="gcash">GCash</option>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="bitcoin">Bitcoin</option>
                <option value="stripe">Stripe</option>
                <option value="apple_pay">Apple Pay</option>
                <option value="google_pay">Google Pay</option>
            </select>
            <label for="amount">Amount (₱):</label>
            <input type="number" id="amount" name="amount" required>
            <button type="submit">Pay</button>
        </form>

        <?php if ($receipt): ?>
            <div class="receipt">
                <h2>Receipt</h2>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($receipt['paymentMethod']); ?></p>
                <p><strong>Amount:</strong> ₱<?php echo htmlspecialchars($receipt['amount']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($receipt['date']); ?></p>
                <p>Thank you for your payment!</p>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(event) {
            const amount = document.getElementById('amount').value;
            if (amount <= 0) {
                alert('Please enter a valid amount.');
                event.preventDefault();
            }
        });
    </script>
</body>
</html>