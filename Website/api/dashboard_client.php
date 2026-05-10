<?php
header('Content-Type: application/json');
session_start();

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);

// 1. RESTORED: Database connection check
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// 2. ENHANCED: Security role check (Consistency with client_dash.php)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$clientID = $_SESSION['clientID'];

// 3. FIXED: SQL now includes vendorID so the Dashboard links work
$sql = "SELECT b.bookingID, b.eventDate, b.status, b.vendorID, v.vendorOrginisationName, s.serviceName, s.servicePrice 
        FROM booking b
        JOIN vendor v ON b.vendorID = v.vendorID
        JOIN service s ON b.serviceID = s.serviceID
        WHERE b.clientID = ?
        ORDER BY b.eventDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $clientID);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode(["data" => $bookings]);

$stmt->close();
$conn->close();
?>