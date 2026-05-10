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
$bookingID = intval($data['bookingID'] ?? 0);
$clientID = intval($_SESSION['clientID']);

if ($bookingID > 0) {
    $conn->begin_transaction();
    try {
        // Verify ownership and ensure the booking isn't already completed/cancelled
        $stmtCheck = $conn->prepare("SELECT vendorID, status FROM booking WHERE bookingID = ? AND clientID = ?");
        $stmtCheck->bind_param("ii", $bookingID, $clientID);
        $stmtCheck->execute();
        $res = $stmtCheck->get_result();
        
        if ($res->num_rows === 0) throw new Exception("Booking not found or unauthorized");
        $booking = $res->fetch_assoc();
        $vendorID = $booking['vendorID'];
        $stmtCheck->close();
        
        if (in_array($booking['status'], ['completed', 'cancelled'])) {
            throw new Exception("Cannot cancel a completed or already cancelled booking.");
        }

        // Update booking status
        $stmtUpdate = $conn->prepare("UPDATE booking SET status = 'cancelled' WHERE bookingID = ?");
        $stmtUpdate->bind_param("i", $bookingID);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Inject Automated Cancellation Message into chat
        $stmtChat = $conn->prepare("SELECT chatID FROM chat WHERE clientID = ? AND vendorID = ?");
        $stmtChat->bind_param("ii", $clientID, $vendorID);
        $stmtChat->execute();
        $chatRes = $stmtChat->get_result();
        
        if ($chatRes->num_rows > 0) {
            $chatID = $chatRes->fetch_assoc()['chatID'];
            $msgContent = "❌ BOOKING CANCELLED\nThe client has withdrawn their request for Booking #$bookingID.";
            
            $stmtMsg = $conn->prepare("INSERT INTO message (chatID, senderID, senderType, messageContent) VALUES (?, ?, 'client', ?)");
            $stmtMsg->bind_param("iis", $chatID, $clientID, $msgContent);
            $stmtMsg->execute();
            $stmtMsg->close();
        }
        $stmtChat->close();

        $conn->commit();
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid booking ID"]);
}
$conn->close();
?>