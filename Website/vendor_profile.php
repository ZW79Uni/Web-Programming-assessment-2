<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);

$vendorID = $_SESSION['user_id'];
$systemMessage = ""; // Diagnostic feedback banner

// Handle Add Service Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_service') {
    $eventTypeID = intval($_POST['eventTypeID'] ?? 0);
    $svcName = $conn->real_escape_string($_POST['serviceName'] ?? '');
    $svcPrice = floatval($_POST['servicePrice'] ?? 0);
    $svcDesc = $conn->real_escape_string($_POST['serviceDescription'] ?? '');
    
    if ($svcName && $svcPrice > 0 && $eventTypeID > 0) {
        $conn->begin_transaction();
        try {
            $insertSvc = "INSERT INTO `service` (`serviceName`, `serviceDecription`, `servicePrice`) VALUES ('$svcName', '$svcDesc', $svcPrice)";
            if (!$conn->query($insertSvc)) throw new Exception("Failed to create Service: " . $conn->error);
            $newSvcID = $conn->insert_id;
            
            $insertAlloc = "INSERT INTO `vendorServiceAllocation` (`vendorID`, `serviceID`) VALUES ($vendorID, $newSvcID)";
            if (!$conn->query($insertAlloc)) throw new Exception("Failed to link Vendor to Service: " . $conn->error);

            $insertEventAlloc = "INSERT INTO `eventTypeAllocation` (`eventTypeID`, `serviceID`) VALUES ($eventTypeID, $newSvcID)";
            if (!$conn->query($insertEventAlloc)) throw new Exception("Failed to link Event Type: " . $conn->error);
            
            $conn->commit();
            $systemMessage = "<div style='background:#5cb85c; color:white; padding:15px; margin-bottom:20px; font-weight:bold; border:2px solid #4cae4c;'>✅ Success: '$svcName' has been added to your portfolio!</div>";
        } catch (Exception $e) {
            $conn->rollback();
            $systemMessage = "<div style='background:#d9534f; color:white; padding:15px; margin-bottom:20px; font-weight:bold; border:2px solid #c9302c;'>❌ Database Error: " . $e->getMessage() . "</div>";
        }
    } else {
        $systemMessage = "<div style='background:#f0ad4e; color:white; padding:15px; margin-bottom:20px; font-weight:bold; border:2px solid #eea236;'>⚠️ Please fill out all fields with valid data.</div>";
    }
}

// Handle Booking Status Update & Chat Injection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_booking') {
    $bookingID = intval($_POST['bookingID'] ?? 0);
    $status = $conn->real_escape_string($_POST['status'] ?? '');
    
    if ($bookingID > 0 && in_array($status, ['confirmed', 'cancelled', 'completed'])) {
        $conn->begin_transaction();
        try {
            $conn->query("UPDATE `booking` SET `status` = '$status' WHERE `bookingID` = $bookingID AND `vendorID` = $vendorID");
            
            $res = $conn->query("SELECT clientID FROM booking WHERE bookingID = $bookingID");
            if ($res->num_rows > 0) {
                $clientID = $res->fetch_assoc()['clientID'];
                $chatRes = $conn->query("SELECT chatID FROM chat WHERE clientID = $clientID AND vendorID = $vendorID");
                if ($chatRes->num_rows > 0) {
                    $chatID = $chatRes->fetch_assoc()['chatID'];
                    
                    $msgContent = "";
                    if ($status === 'confirmed') $msgContent = "✅ BOOKING ACCEPTED!\nThe vendor has confirmed your request for Booking #$bookingID.";
                    if ($status === 'cancelled') $msgContent = "❌ BOOKING DECLINED!\nThe vendor cannot fulfill Booking #$bookingID.";
                    if ($status === 'completed') $msgContent = "🎉 EVENT COMPLETED!\nBooking #$bookingID has concluded. Client, please check your dashboard to leave a review!";
                    
                    if ($msgContent) {
                        $stmtMsg = $conn->prepare("INSERT INTO message (chatID, senderID, senderType, messageContent) VALUES (?, ?, 'vendor', ?)");
                        $stmtMsg->bind_param("iis", $chatID, $vendorID, $msgContent);
                        $stmtMsg->execute();
                    }
                }
            }
            $conn->commit();
            $systemMessage = "<div style='background:#5cb85c; color:white; padding:15px; margin-bottom:20px; font-weight:bold;'>✅ Booking #$bookingID status updated to '$status'.</div>";
        } catch (Exception $e) {
            $conn->rollback();
        }
    }
}

$vendorQuery = "SELECT `firstName`, `lastName`, `vendorOrginisationName`, `isVerified`, `automationScore`, 
                (SELECT COALESCE(ROUND(AVG(rating), 1), 0) FROM review WHERE vendorID = $vendorID) as avgRating 
                FROM `vendor` WHERE `vendorID` = $vendorID";
$vendorData = $conn->query($vendorQuery)->fetch_assoc();

$badges = [];
if (!empty($vendorData['isVerified'])) $badges[] = "🌟 Verified Vendor";
if (!empty($vendorData['avgRating']) && $vendorData['avgRating'] >= 4.5) $badges[] = "🏆 Top Rated (" . $vendorData['avgRating'] . " Stars)";

$servicesResult = $conn->query("
    SELECT s.`serviceID`, s.`serviceName`, s.`servicePrice`, s.`serviceDecription`
    FROM `service` s
    JOIN `vendorServiceAllocation` vsa ON s.`serviceID` = vsa.`serviceID`
    WHERE vsa.`vendorID` = $vendorID AND s.isAvailable = 1
");

$bookingsResult = $conn->query("
    SELECT b.`bookingID`, b.`eventDate`, b.`status`, b.`lockedPrice`, s.`serviceName`, c.`firstName`, c.`lastName`
    FROM `booking` b
    JOIN `service` s ON b.`serviceID` = s.`serviceID`
    JOIN `client` c ON b.`clientID` = c.`clientID`
    WHERE b.`vendorID` = $vendorID
    ORDER BY b.`eventDate` ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vendor Dashboard - EMW</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .badges-wrapper { text-align: center; margin-bottom: 30px; }
        .badge { display: inline-block; padding: 8px 15px; margin: 5px; border: 2px solid #E15050; background-color: #fcebeb; color: #E15050; font-weight: bold; border-radius: 4px; }
        .content-layout { display: flex; gap: 30px; align-items: flex-start; }
        .sidebar { width: 30%; border: 3px solid #E15050; padding: 20px; box-sizing: border-box; background: #ffffff; }
        .main-panel { width: 70%; display: flex; flex-direction: column; gap: 30px; }
        .panel { border: 3px solid #E15050; padding: 20px; box-sizing: border-box; background: #ffffff; }
        h2 { margin-top: 0; color: #E15050; border-bottom: 3px solid #E15050; padding-bottom: 10px; }
        .input-box { width: 100%; padding: 10px; margin: 8px 0 15px 0; border: 2px solid #E15050; box-sizing: border-box; color: #E15050; font-family: inherit; }
        .action-btn { width: 100%; padding: 10px; border: 3px solid #E15050; background: none; cursor: pointer; color: #E15050; font-size: 16px; font-weight: bold; transition: all 0.2s ease; }
        .action-btn:hover { background: #E15050; color: #ffffff; }
        .inline-btn { width: auto; padding: 6px 12px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { border-bottom: 1px solid #fcebeb; padding: 12px 8px; color: #E15050; }
        th { border-bottom: 2px solid #E15050; }
        ul { list-style-type: none; padding: 0; margin: 0; }
        li { padding: 15px 0; border-bottom: 1px solid #fcebeb; }
        li:last-child { border-bottom: none; }
        .button-group { display: flex; gap: 15px; }
    </style>
</head>
<body>
    <?php include 'global_header.php'; ?>
    
    <div class="profile-container">
        
        <?= $systemMessage ?>

        <?php if (!empty($badges)): ?>
        <div class="badges-wrapper">
            <?php foreach ($badges as $badge) { echo "<span class='badge'>$badge</span>"; } ?>
        </div>
        <?php endif; ?>

        <div class="content-layout">
            <div class="sidebar">
                <h2>New Service</h2>
                <form method="POST" action="vendor_profile.php">
                    <input type="hidden" name="action" value="add_service">
                    <label><strong>Event Category:</strong></label>
                    <select name="eventTypeID" class="input-box" required>
                        <option value="1">Wedding</option>
                        <option value="2">Birthday Party</option>
                        <option value="3">Corporate Function</option>
                        <option value="4">Launch Event</option>
                    </select>
                    <label><strong>Name:</strong></label>
                    <input type="text" name="serviceName" class="input-box" required>
                    <label><strong>Price (£):</strong></label>
                    <input type="number" step="0.01" name="servicePrice" class="input-box" required>
                    <label><strong>Description:</strong></label>
                    <textarea name="serviceDescription" class="input-box" style="height:100px; resize:vertical;" required></textarea>
                    <button type="submit" class="action-btn">Submit Service</button>
                </form>
            </div>
            
            <div class="main-panel">
                <div class="panel">
                    <h2>Manage Bookings</h2>
                    <table>
                        <tr><th>Client</th><th>Service</th><th>Date</th><th>Status</th><th>Actions</th></tr>
                        <?php if ($bookingsResult && $bookingsResult->num_rows > 0): ?>
                            <?php while ($bk = $bookingsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($bk['firstName'] . ' ' . $bk['lastName']); ?></td>
                                    <td><?php echo htmlspecialchars($bk['serviceName']); ?><br><small>£<?= $bk['lockedPrice'] ?? 'N/A' ?></small></td>
                                    <td><?php echo htmlspecialchars($bk['eventDate']); ?></td>
                                    <td><strong><?php echo strtoupper($bk['status']); ?></strong></td>
                                    <td>
                                        <?php if ($bk['status'] === 'pending'): ?>
                                        <div style="display:flex; gap:5px;">
                                            <form method="POST" action="vendor_profile.php" style="margin:0;">
                                                <input type="hidden" name="action" value="update_booking">
                                                <input type="hidden" name="bookingID" value="<?php echo $bk['bookingID']; ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="action-btn inline-btn" style="background:#5cb85c; color:white; border-color:#5cb85c;">Accept</button>
                                            </form>
                                            <form method="POST" action="vendor_profile.php" style="margin:0;">
                                                <input type="hidden" name="action" value="update_booking">
                                                <input type="hidden" name="bookingID" value="<?php echo $bk['bookingID']; ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="action-btn inline-btn" style="border-color:#d9534f; color:#d9534f;">Decline</button>
                                            </form>
                                        </div>
                                        <?php elseif ($bk['status'] === 'confirmed'): ?>
                                            <form method="POST" action="vendor_profile.php" style="margin:0;">
                                                <input type="hidden" name="action" value="update_booking">
                                                <input type="hidden" name="bookingID" value="<?php echo $bk['bookingID']; ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="action-btn inline-btn" style="background:#00ADEF; color:white; border-color:#00ADEF;">Mark Completed</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color:#999;">Finalized</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No active bookings.</td></tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="panel">
                    <h2>Active Services</h2>
                    <ul>
                        <?php if ($servicesResult && $servicesResult->num_rows > 0): ?>
                            <?php while ($svc = $servicesResult->fetch_assoc()): ?>
                                <li>
                                    <strong style="font-size:18px;"><?php echo htmlspecialchars($svc['serviceName']); ?></strong> 
                                    <span style="float:right; font-weight:bold;">£<?php echo htmlspecialchars($svc['servicePrice']); ?></span>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li>No services listed. Add one to the left.</li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="panel">
                    <h2>Blogs & Chat</h2>
                    <div class="button-group">
                        <button class="action-btn" onclick="window.location.href='blogs.php'">Post Blog</button>
                        <button class="action-btn" onclick="window.location.href='chat.php'">Chat with Clients</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include 'global_footer.php'; ?>
</body>
</html>