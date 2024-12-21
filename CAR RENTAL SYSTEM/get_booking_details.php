<?php
include 'db.php';

// Query to get all booking details
$query = "SELECT 
    b.id,
    b.name,
    c.name as car_name,
    b.pickup_date,
    b.return_date,
    CASE 
        WHEN b.return_date < CURDATE() THEN 'Completed'
        ELSE 'Active'
    END as status
    FROM bookings b
    JOIN cars c ON b.car_id = c.id
    ORDER BY b.pickup_date DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$bookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $bookings[] = [
        'id' => $row['id'],
        'name' => htmlspecialchars($row['name']),
        'car_name' => htmlspecialchars($row['car_name']),
        'pickup_date' => date('M d, Y', strtotime($row['pickup_date'])),
        'return_date' => date('M d, Y', strtotime($row['return_date'])),
        'status' => $row['status']
    ];
}

header('Content-Type: application/json');
echo json_encode($bookings);
?> 