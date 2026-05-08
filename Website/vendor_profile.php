<?php
session_start();

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);

// For demo purposes, fallback to a sample vendor if not logged in
$vendorID = $_SESSION['vendorID'] ?? 1;

// Handle Add Service Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_service') {
    $svcName = $conn->real_escape_string($_POST['serviceName'] ?? '');
    $svcPrice = floatval($_POST['servicePrice'] ?? 0);
    $svcDesc = $conn->real_escape_string($_POST['serviceDescription'] ?? '');
    
    if ($svcName && $svcPrice > 0) {
        $insertSvc = "INSERT INTO `service` (`serviceName`, `serviceDecription`, `servicePrice`) VALUES ('$svcName', '$svcDesc', $svcPrice)";
        if ($conn->query($insertSvc)) {
            $newSvcID = $conn->insert_id;
            $insertAlloc = "INSERT INTO `vendorServiceAllocation` (`vendorID`, `serviceID`) VALUES ($vendorID, $newSvcID)";
            $conn->query($insertAlloc);
        }
    }
}

// Handle Booking Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_booking') {
    $bookingID = intval($_POST['bookingID'] ?? 0);
    $status = $conn->real_escape_string($_POST['status'] ?? 'pending');
    if ($bookingID > 0 && in_array($status, ['confirmed', 'cancelled', 'completed'])) {
        $conn->query("UPDATE `booking` SET `status` = '$status' WHERE `bookingID` = $bookingID AND `vendorID` = $vendorID");
    }
}

// Fetch Vendor Profile Data
$vendorQuery = "SELECT `firstName`, `lastName`, `vendorOrginisationName`, `isVerified`, `automationScore` FROM `vendor` WHERE `vendorID` = $vendorID";
$vendorResult = $conn->query($vendorQuery);
$vendorData = $vendorResult ? $vendorResult->fetch_assoc() : [];

// Calculate Badges (Demo logic for 'badges / star ratings' request)
// Technical Note: Currently evaluates the statically fetched boolean `isVerified`
// and integer `automationScore`. In a deployed real-world environment, this would
// aggregate `rating` from the `review` table and calculate live percentage.
$badges = [];
if (!empty($vendorData['isVerified'])) {
    $badges[] = "🌟 Verified Vendor";
}
if (!empty($vendorData['automationScore']) && $vendorData['automationScore'] > 80) {
    $badges[] = "🏆 Top Rated (Score: " . $vendorData['automationScore'] . ")";
}

// Fetch Existing Services
$servicesResult = $conn->query("
    SELECT s.`serviceID`, s.`serviceName`, s.`servicePrice`, s.`serviceDecription`
    FROM `service` s
    JOIN `vendorServiceAllocation` vsa ON s.`serviceID` = vsa.`serviceID`
    WHERE vsa.`vendorID` = $vendorID
");

// Fetch Bookings
$bookingsResult = $conn->query("
    SELECT b.`bookingID`, b.`eventDate`, b.`status`, s.`serviceName`, c.`firstName`, c.`lastName`
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
    <title>Vendor Profile - EMW</title>
    <!-- Using inline styles heavily for the boxy wireframe look -->
    <style>
        .container {
            width: 1000px;
            margin: 0 auto;
            border: 5px solid black;
            box-sizing: border-box;
            background-color: white;
            padding: 10px;
        }
        .header {
            width: 100%;
            height: 100px;
            border: 5px solid black;
            box-sizing: border-box;
            margin-bottom: 20px;
            text-align: center;
        }
        .nav {
            width: 100%;
            height: 40px;
            border: 5px solid black;
            box-sizing: border-box;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            font-weight: bold;
        }
        .nav a { text-decoration: none; color: black; }
        .content {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .sidebar {
            width: 30%;
            border: 5px solid black;
            box-sizing: border-box;
            padding: 10px;
            min-height: 400px;
        }
        .main-panel {
            width: 70%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .panel {
            border: 5px solid black;
            box-sizing: border-box;
            padding: 10px;
        }
        .footer {
            width: 100%;
            height: 80px;
            border: 5px solid black;
            box-sizing: border-box;
            text-align: center;
            padding-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            margin: 5px;
            border: 2px solid black;
            background-color: #EEE;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'global_header.php'; ?>
        
        <!-- Badges / Rating Row -->
        <div style="margin-bottom: 20px; text-align: center;">
            <?php foreach ($badges as $badge) { echo "<span class='badge'>$badge</span>"; } ?>
        </div>

        <div class="content">
            <div class="sidebar">
                <h2>New Service</h2>
                <form method="POST" action="vendor_profile.php">
                    <input type="hidden" name="action" value="add_service">
                    <p><label>Name:</label><br> <input type="text" name="serviceName" style="width:90%" required></p>
                    <p><label>Price (£):</label><br> <input type="number" step="0.01" name="servicePrice" style="width:90%" required></p>
                    <p><label>Description:</label><br> <textarea name="serviceDescription" style="width:90%; height:80px;" required></textarea></p>
                    <button type="submit">Submit Service</button>
                </form>
            </div>
            
            <div class="main-panel">
                <div class="panel">
                    <h2>Existing Services</h2>
                    <ul>
                        <?php if ($servicesResult): ?>
                            <?php while ($svc = $servicesResult->fetch_assoc()): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($svc['serviceName']); ?></strong> - £<?php echo htmlspecialchars($svc['servicePrice']); ?>
                                    <br><small><?php echo htmlspecialchars($svc['serviceDecription']); ?></small>
                                </li>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="panel">
                    <h2>Active Bookings</h2>
                    <table style="width:100%; border-collapse: collapse; text-align:left;">
                        <tr>
                            <th style="border-bottom: 2px solid black;">Client</th>
                            <th style="border-bottom: 2px solid black;">Service</th>
                            <th style="border-bottom: 2px solid black;">Date</th>
                            <th style="border-bottom: 2px solid black;">Status</th>
                            <th style="border-bottom: 2px solid black;">Actions</th>
                        </tr>
                        <?php if ($bookingsResult): ?>
                            <?php while ($bk = $bookingsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($bk['firstName'] . ' ' . $bk['lastName']); ?></td>
                                    <td><?php echo htmlspecialchars($bk['serviceName']); ?></td>
                                    <td><?php echo htmlspecialchars($bk['eventDate']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($bk['status']); ?></strong></td>
                                    <td>
                                        <?php if ($bk['status'] === 'pending'): ?>
                                        <form method="POST" action="vendor_profile.php" style="display:inline;">
                                            <input type="hidden" name="action" value="update_booking">
                                            <input type="hidden" name="bookingID" value="<?php echo $bk['bookingID']; ?>">
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit">Confirm</button>
                                        </form>
                                        <form method="POST" action="vendor_profile.php" style="display:inline;">
                                            <input type="hidden" name="action" value="update_booking">
                                            <input type="hidden" name="bookingID" value="<?php echo $bk['bookingID']; ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit">Cancel</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="panel">
                    <h2>Blogs & Chat</h2>
                    <button>Post Blog (Demo)</button>
                    <button onclick="window.location.href='chatPicker.HTML'">Chat with Clients</button>
                </div>
            </div>
        </div>

        <?php include 'global_footer.php'; ?>
    </div>
</body>
</html>