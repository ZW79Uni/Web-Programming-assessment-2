<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$chatID = intval($_GET['chatID']);

$sql = "
SELECT senderType, messageContent
FROM message
WHERE chatID = $chatID
ORDER BY messageID ASC
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='messages'>";
    while ($row = $result->fetch_assoc()) {
        $sender = htmlspecialchars($row['senderType']);
        $message = htmlspecialchars($row['messageContent']);
        echo "<div class='message $sender'><strong>$sender:</strong> $message</div>";
    }
    echo "</div>";
} else {
    echo "<div>No messages yet.</div>";
}

$conn->close();
?>