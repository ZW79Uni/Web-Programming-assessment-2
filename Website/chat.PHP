<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
<style>
.container {
    width: 2485px;  
    height: 1250px;
    border:5px solid black;
    box-sizing: border-box;  
}

.Logo {
    border:5px solid black;
    width: 440px;
    height: 258px;
    float: left;
    box-sizing: border-box;
}

.Nav {
    border:5px solid black;
    width: 2030px;  
    height: 258px;
    float: left;
    box-sizing: border-box;  
}

.chatPicker {
    border:5px solid black;
    width: 440px;
    height: 992px;
    float: left;
    box-sizing: border-box;
    display: flex;
}

.chat {
    border:5px solid black;
    width: 2030px;  
    height: 992px;
    float: left;
    box-sizing: border-box;  
}

.clearfix::after {
    content: "";
    clear: both;
    display: table;
}
</style>
<body>
<div class="container clearfix">
    <div class="Logo">
        <h1>Logo here</h1>
    </div>
    <div class="Nav">
        <h1>Navigation here</h1>
    </div>
    <div class="chatPicker">
    <!-- <img src="chatBG.png" width="430" height="40"> -->
    <img src="blank pfp.png" width="40" height="40">
    <div>
        <?php
$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$clientID = 1;

$sql = "
SELECT chat.vendorID, vendor.firstName, vendor.lastName, vendor.vendorOrginisationName
FROM chat
JOIN vendor ON chat.vendorID = vendor.vendorID
WHERE chat.clientID = $clientID
";

$result = $conn->query($sql);

if ($result === false) {
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $vendorName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
        $vendorOrg  = htmlspecialchars($row['vendorOrginisationName']);
        echo "Chat with: $vendorName <br>from: $vendorOrg<br>";
    }
} else {
    echo "No results found";
}

$conn->close();
?>
    </div>
    </div>
    <div class="chat">
        <h1>chat here</h1>
    </div>
</div>
</body>
</html>