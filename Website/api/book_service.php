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
$serviceID = intval($data['serviceID'] ?? 0);
$eventDate = $conn->real_escape_string($data['eventDate'] ?? '');
$clientID = intval($_SESSION['clientID']);

if ($vendorID > 0 && $serviceID > 0 && !empty($eventDate)) {
    $conn->begin_transaction();
    try {
        // 1. Get Service details to lock in the price
        $stmtSvc = $conn->prepare("SELECT serviceName, servicePrice FROM service WHERE serviceID = ?");
        $stmtSvc->bind_param("i", $serviceID);
        $stmtSvc->execute();
        $svcResult = $stmtSvc->get_result();
        if ($svcResult->num_rows === 0) throw new Exception("Service not found");
        $svc = $svcResult->fetch_assoc();
        $stmtSvc->close();

        // 2. Insert Booking with locked price
        $stmtBook = $conn->prepare("INSERT INTO booking (clientID, vendorID, serviceID, eventDate, status, lockedPrice) VALUES (?, ?, ?, ?, 'pending', ?)");
        $stmtBook->bind_param("iiisd", $clientID, $vendorID, $serviceID, $eventDate, $svc['servicePrice']);
        $stmtBook->execute();
        $bookingID = $stmtBook->insert_id;
        $stmtBook->close();

        // 3. Ensure Chat Thread Exists
        $chatID = 0;
        $stmtChat = $conn->prepare("SELECT chatID FROM chat WHERE clientID = ? AND vendorID = ?");
        $stmtChat->bind_param("ii", $clientID, $vendorID);
        $stmtChat->execute();
        $chatRes = $stmtChat->get_result();
        if ($chatRes->num_rows > 0) {
            $chatID = $chatRes->fetch_assoc()['chatID'];
        } else {
            $insertChat = $conn->prepare("INSERT INTO chat (clientID, vendorID) VALUES (?, ?)");
            $insertChat->bind_param("ii", $clientID, $vendorID);
            $insertChat->execute();
            $chatID = $insertChat->insert_id;
            $insertChat->close();
        }
        $stmtChat->close();

        // 4. Inject Automated System Message
        $msgContent = "🗓️ NEW BOOKING REQUEST (ID: #$bookingID)\nService: " . $svc['serviceName'] . "\nDate: $eventDate\nPrice: £" . $svc['servicePrice'] . "\n\nVendor, please Accept or Decline this request via your dashboard.";
        $stmtMsg = $conn->prepare("INSERT INTO message (chatID, senderID, senderType, messageContent) VALUES (?, ?, 'client', ?)");
        $stmtMsg->bind_param("iis", $chatID, $clientID, $msgContent);
        $stmtMsg->execute();
        $stmtMsg->close();

        $conn->commit();
        echo json_encode(["success" => true, "bookingID" => $bookingID]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Invalid inputs"]);
}
$conn->close();
?>