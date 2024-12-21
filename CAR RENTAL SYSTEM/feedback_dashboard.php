<?php
session_start(); // Start the session

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

// Fetch feedback details from the database
$sql = "SELECT f.id, f.first_name, f.last_name, f.email, f.phone, f.message, f.created_at, u.name AS user_name 
        FROM feedback f 
        LEFT JOIN users u ON f.user_id = u.id 
        ORDER BY f.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50; /* Green background for header */
            color: white; /* White text for header */
        }

        tr:hover {
            background-color: #f1f1f1; /* Light gray background on hover */
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Zebra striping for even rows */
        }

        tr:nth-child(odd) {
            background-color: #ffffff; /* White background for odd rows */
        }

        td {
            color: #555; /* Darker text color for table data */
        }

        /* Responsive design */
        @media (max-width: 600px) {
            table {
                font-size: 14px; /* Smaller font size for mobile */
            }
        }
        
    </style>
</head>
<body>
    </div>
    <h1>Feedback Dashboard</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Submitted At</th>
                <th>User Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['first_name']}</td>
                            <td>{$row['last_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['message']}</td>
                            <td>{$row['created_at']}</td>
                            <td>{$row['user_name']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No feedback available</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
