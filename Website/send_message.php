<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$chatID = intval($_POST['chatID']);
$message = $conn->real_escape_string($_POST['message']);

$senderType = "client";

$sql = "
INSERT INTO message (chatID, senderType, messageContent)
VALUES ($chatID, '$senderType', '$message')
";

if ($conn->query($sql) === TRUE) {
    echo "Message sent";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>