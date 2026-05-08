<?php
session_start();

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);

// For demo purposes, fallback to a sample admin if not logged in
$adminID = $_SESSION['adminID'] ?? 1;

// Handle Actions (Ban Client / Verify Vendor)
// Added inline comments for technical documentation
// We perform updates to 'client' or 'vendor' and log these to 'auditLog'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'ban_client' && isset($_POST['clientID'])) {
        $clientID = intval($_POST['clientID']);
        // Ban logic
        $stmt = $conn->prepare("UPDATE `client` SET `isBanned` = 1, `bannedBy` = ?, `bannedAt` = CURRENT_TIMESTAMP WHERE `clientID` = ?");
        $stmt->bind_param("ii", $adminID, $clientID);
        $stmt->execute();
        
        // Log to auditLog
        $conn->query("INSERT INTO `auditLog` (`adminID`, `actionType`, `targetType`, `targetID`, `actionTimestamp`) VALUES ($adminID, 'ban_client', 'client', $clientID, CURRENT_TIMESTAMP)");
        $stmt->close();
    } elseif ($action === 'verify_vendor' && isset($_POST['vendorID'])) {
        $vendorID = intval($_POST['vendorID']);
        // Verify logic
        $stmt = $conn->prepare("UPDATE `vendor` SET `isVerified` = 1, `verifiedBy` = ?, `verifiedAt` = CURRENT_TIMESTAMP WHERE `vendorID` = ?");
        $stmt->bind_param("ii", $adminID, $vendorID);
        $stmt->execute();

        // Log to auditLog
        $conn->query("INSERT INTO `auditLog` (`adminID`, `actionType`, `targetType`, `targetID`, `actionTimestamp`) VALUES ($adminID, 'verify_vendor', 'vendor', $vendorID, CURRENT_TIMESTAMP)");
        $stmt->close();
    }
}

// Data Fetching
// 1. Clients Management (For Banning)
$clientsResult = $conn->query("SELECT `clientID`, `firstName`, `lastName`, `username`, `isBanned` FROM `client` LIMIT 10");

// 2. Unverified Vendors
$unverifiedVendorsResult = $conn->query("SELECT `vendorID`, `firstName`, `lastName`, `vendorOrginisationName` FROM `vendor` WHERE `isVerified` = 0 LIMIT 10");

// 3. Platform Moderation - Recent Messages (Monitoring Chats)
$messagesResult = $conn->query("SELECT `messageID`, `chatID`, `senderType`, `messageContent` FROM `message` ORDER BY `messageID` DESC LIMIT 5");

// 4. Platform Moderation - Recent Blogs (Monitoring Blogs)
$blogsResult = $conn->query("SELECT `blogID`, `posterType`, `posterID`, LEFT(CAST(`blogContent` AS CHAR), 50) AS `content_preview` FROM `blog` ORDER BY `blogID` DESC LIMIT 5");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - EMW</title>
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
            flex-direction: column;
            gap: 20px;
            margin-bottom: 20px;
        }
        .panel {
            border: 5px solid black;
            box-sizing: border-box;
            padding: 10px;
            min-height: 150px;
        }
        .footer {
            width: 100%;
            height: 80px;
            border: 5px solid black;
            box-sizing: border-box;
            text-align: center;
            padding-top: 20px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #ccc; padding: 5px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'global_header.php'; ?>
        
        <div class="content">
            <div class="panel">
                <h2>Pending Vendor Verifications</h2>
                <table>
                    <tr><th>Vendor ID</th><th>Name</th><th>Organisation</th><th>Action</th></tr>
                    <?php if ($unverifiedVendorsResult && $unverifiedVendorsResult->num_rows > 0): ?>
                        <?php while ($vendor = $unverifiedVendorsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $vendor['vendorID'] ?></td>
                            <td><?= htmlspecialchars($vendor['firstName'] . ' ' . $vendor['lastName']) ?></td>
                            <td><?= htmlspecialchars($vendor['vendorOrginisationName']) ?></td>
                            <td>
                                <form method="POST" action="admin_dash.php">
                                    <input type="hidden" name="action" value="verify_vendor">
                                    <input type="hidden" name="vendorID" value="<?= $vendor['vendorID'] ?>">
                                    <button type="submit">Verify Vendor</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No pending verifications.</td></tr>
                    <?php endif; ?>
                </table>
            </div>

            <div class="panel">
                <h2>User Management (Ban/Suspend Clients)</h2>
                <table>
                    <tr><th>Client ID</th><th>Name</th><th>Username</th><th>Status</th><th>Action</th></tr>
                    <?php if ($clientsResult): ?>
                        <?php while ($client = $clientsResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= $client['clientID'] ?></td>
                            <td><?= htmlspecialchars($client['firstName'] . ' ' . $client['lastName']) ?></td>
                            <td><?= htmlspecialchars($client['username']) ?></td>
                            <td><?= $client['isBanned'] ? '<strong style="color:red;">Banned</strong>' : 'Active' ?></td>
                            <td>
                                <?php if (!$client['isBanned']): ?>
                                <form method="POST" action="admin_dash.php">
                                    <input type="hidden" name="action" value="ban_client">
                                    <input type="hidden" name="clientID" value="<?= $client['clientID'] ?>">
                                    <button type="submit" style="color:red;">Ban Client</button>
                                </form>
                                <?php else: ?>
                                    <em>Action Unavailable</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </table>
            </div>
            
            <div class="panel">
                <h2>Platform Moderation</h2>
                <div style="display:flex; gap: 20px;">
                    <div style="flex:1;">
                        <h3>Recent Chats</h3>
                        <ul>
                            <?php if ($messagesResult) {
                                while ($msg = $messagesResult->fetch_assoc()) {
                                    echo "<li><strong>[" . $msg['senderType'] . "] Chat " . $msg['chatID'] . ":</strong> " . htmlspecialchars($msg['messageContent']) . "</li>";
                                }
                            } ?>
                        </ul>
                    </div>
                    <div style="flex:1;">
                        <h3>Recent Blogs</h3>
                        <ul>
                            <?php if ($blogsResult) {
                                while ($blg = $blogsResult->fetch_assoc()) {
                                    // Note: blogContent is MEDIUMBLOB, so fetching via CAST AS CHAR in query
                                    echo "<li><strong>[" . $blg['posterType'] . "] ID " . $blg['posterID'] . ":</strong> " . htmlspecialchars($blg['content_preview']) . "...</li>";
                                }
                            } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php include 'global_footer.php'; ?>
    </div>
</body>
</html>