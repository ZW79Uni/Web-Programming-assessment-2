<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);

$data = json_decode(file_get_contents("php://input"), true);
$vendorID = intval($data['vendorID'] ?? 0);
$clientID = intval($_SESSION['clientID']);

if ($vendorID > 0) {
    // Check if chat thread already exists
    $stmt = $conn->prepare("SELECT chatID FROM chat WHERE clientID = ? AND vendorID = ?");
    $stmt->bind_param("ii", $clientID, $vendorID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(["success" => true, "chatID" => $row['chatID']]);
    } else {
        // Create new chat thread
        $insert = $conn->prepare("INSERT INTO chat (clientID, vendorID) VALUES (?, ?)");
        $insert->bind_param("ii", $clientID, $vendorID);
        $insert->execute();
        echo json_encode(["success" => true, "chatID" => $insert->insert_id]);
    }
} else {
    echo json_encode(["error" => "Invalid Vendor ID"]);
}
$conn->close();
?>