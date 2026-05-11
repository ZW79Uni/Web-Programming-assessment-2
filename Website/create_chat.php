<?php
session_start();
header('Content-Type: application/json');

error_reporting(0); 

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'client') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$conn = new mysqli("localhost", "h6zp02h_WebAccess", "SparrowHawk26!", "h6zp02h_EMW_Database");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

$clientID = intval($_SESSION['user_id']);
$vendorID = intval($_POST['vendorID'] ?? 0);

if ($vendorID > 0) {
    $stmt = $conn->prepare("INSERT INTO chat (clientID, vendorID) VALUES (?, ?)");
    $stmt->bind_param("ii", $clientID, $vendorID);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'DB error or duplicate chat']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Vendor ID']);
}
$conn->close();
?>