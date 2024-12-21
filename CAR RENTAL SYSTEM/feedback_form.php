<?php
// Database connection
$servername = "localhost"; // Usually correct
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP
$dbname = "car_rental_db"; // Ensure this is the correct database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start(); // Ensure session is started

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user_id is set in the session
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('You must be logged in to submit feedback.'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id']; // Get user_id from session
    $firstName = htmlspecialchars($_POST['firstName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);

    // SQL insert statement for feedback
    $sql = "INSERT INTO feedback (user_id, first_name, last_name, email, phone, message) 
            VALUES ('$user_id', '$firstName', '$lastName', '$email', '$phone', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Feedback submitted successfully.'); window.location.href='form2.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// New HTML form layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Customer Support</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <style>
        /* Reset some default styles */
body, h1, h2, form {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body styles */
body {
    font-family: Arial, sans-serif;
    background-image: url('car2.webp');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Container styles */
.container {
    max-width: 900px; /* Ensure the container is wide enough */
    margin: auto; /* Center the container */
    padding: 40px; /* Increased padding for better spacing */
    background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent background */
    border-radius: 10px; /* Rounded corners for the container */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

/* Heading styles */
h1 {
    text-align: center;
    color: red; /* YourCompany color */
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333; /* Darker text for contrast */
}

/* Input styles */
input[type="text"],
input[type="email"],
input[type="tel"],
select,
textarea {
    width: 100%; /* Full width for inputs */
    padding: 12px; /* Padding for inputs */
    border: 1px solid #ccc; /* Border style */
    border-radius: 4px; /* Rounded corners */
    font-size: 16px; /* Font size */
    box-shadow: none; /* Remove any default shadow */
}

/* Placeholder styles */
input::placeholder,
textarea::placeholder {
    color: #aaa; /* Placeholder color */
}

/* Button styles */
button {
    background-color: red; /* YourCompany color */
    color: white;
    padding: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    font-size: 16px; /* Larger font size */
}

button:hover {
    background-color: darkred; /* Darker shade on hover */
}

/* Select styles */
select {
    appearance: none; /* Remove default arrow */
    background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>') no-repeat right 10px center;
    background-size: 12px;
    padding-right: 30px; /* Space for the arrow */
}

/* Additional styles for better presentation */
.container {
    max-width: 900px; /* Increased max width for the container */
    margin: auto; /* Center the container */
    padding: 40px; /* Increased padding for better spacing */
    background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent background */
    border-radius: 10px; /* Rounded corners for the container */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

h1 {
    font-size: 2em; /* Larger font size for the heading */
    margin-bottom: 20px; /* Space below the heading */
    text-align: center; /* Center the heading */
}

h2 {
    font-size: 1.5em; /* Larger font size for the subheading */
    margin-bottom: 30px; /* Space below the subheading */
    text-align: center; /* Center the subheading */
}

/* Form layout */
form {
    display: flex; /* Use flexbox for layout */
    flex-direction: column; /* Stack elements vertically */
    gap: 20px; /* Space between form elements */
    width: 100%; /* Ensure the form takes full width of the container */
}

/* Additional styles for form groups */
.form-group {
    display: flex;
    flex-direction: column; /* Stack label and input vertically */
    margin-bottom: 20px; /* Add margin below each form group */
}

/* Textarea styles */
textarea {
    height: 100px; /* Set a fixed height for the textarea */
    resize: vertical; /* Allow vertical resizing only */
}

/* Input styles */
input[type="text"],
input[type="email"],
input[type="tel"],
select,
textarea {
    width: 100%; /* Full width */
    padding: 12px; /* Padding for inputs */
    border: 1px solid #ccc; /* Border style */
    border-radius: 4px; /* Rounded corners */
    font-size: 16px; /* Font size */
    box-shadow: none; /* Remove any default shadow */
}

/* Button styles */
button {
    background-color: red; /* Button color */
    color: white; /* Text color */
    padding: 12px; /* Padding for button */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor on hover */
    font-size: 16px; /* Font size */
    font-weight: bold; /* Bold text */
}

button:hover {
    background-color: darkred; /* Darker shade on hover */
}

/* Add a hover effect for inputs */
input[type="text"]:hover,
input[type="email"]:hover,
input[type="tel"]:hover,
select:hover,
textarea:hover {
    border-color: #4CAF50; /* Change border color on hover */
}

/* Responsive styles */
@media (max-width: 600px) {
    .container {
        padding: 20px; /* Reduced padding for smaller screens */
        width: 90%; /* Full width on smaller screens */
    }

    h1 {
        font-size: 1.8em; /* Adjusted font size for smaller screens */
    }

    h2 {
        font-size: 1.3em; /* Adjusted font size for smaller screens */
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    select,
    textarea {
        font-size: 14px; /* Smaller font size for inputs */
    }

    button {
        font-size: 14px; /* Smaller font size for button */
    }
}
    </style>
    <div class="container">
        <h1>MTT CAR RENTAL</h1>
        <h2>Contact Customer Support</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
            </div>
           
            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" placeholder="Your message" required></textarea>
            </div>
            <button type="submit">Contact Us</button>
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>