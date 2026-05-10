<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);

$vendorID = $_SESSION['user_id'];

// Handle Actions (Ban Client / Verify Vendor)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'ban_client' && isset($_POST['clientID'])) {
        $clientID = intval($_POST['clientID']);
        $stmt = $conn->prepare("UPDATE `client` SET `isBanned` = 1, `bannedBy` = ?, `bannedAt` = CURRENT_TIMESTAMP WHERE `clientID` = ?");
        $stmt->bind_param("ii", $adminID, $clientID);
        $stmt->execute();
        $conn->query("INSERT INTO `auditLog` (`adminID`, `actionType`, `targetType`, `targetID`) VALUES ($adminID, 'ban_client', 'client', $clientID)");
        $stmt->close();
    } elseif ($action === 'verify_vendor' && isset($_POST['vendorID'])) {
        $vendorID = intval($_POST['vendorID']);
        $stmt = $conn->prepare("UPDATE `vendor` SET `isVerified` = 1, `verifiedBy` = ?, `verifiedAt` = CURRENT_TIMESTAMP WHERE `vendorID` = ?");
        $stmt->bind_param("ii", $adminID, $vendorID);
        $stmt->execute();
        $conn->query("INSERT INTO `auditLog` (`adminID`, `actionType`, `targetType`, `targetID`) VALUES ($adminID, 'verify_vendor', 'vendor', $vendorID)");
        $stmt->close();
    }
}

// Data Fetching
$clientsResult = $conn->query("SELECT `clientID`, `firstName`, `lastName`, `username`, `isBanned` FROM `client` LIMIT 10");
$unverifiedVendorsResult = $conn->query("SELECT `vendorID`, `firstName`, `lastName`, `vendorOrginisationName` FROM `vendor` WHERE `isVerified` = 0 LIMIT 10");
$messagesResult = $conn->query("SELECT `messageID`, `chatID`, `senderType`, `messageContent` FROM `message` ORDER BY `messageID` DESC LIMIT 5");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - EMW</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-container { padding: 40px 20px; max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 30px; }
        .panel { border: 3px solid #E15050; padding: 20px; background-color: #ffffff; }
        .panel h2 { margin-top: 0; color: #E15050; border-bottom: 3px solid #E15050; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border-bottom: 1px solid #fcebeb; padding: 12px; text-align: left; color: #E15050; }
        th { font-weight: bold; border-bottom: 2px solid #E15050; }
        .action-btn { padding: 8px 15px; border: 3px solid #E15050; background: none; cursor: pointer; color: #E15050; font-weight: bold; transition: 0.2s; }
        .action-btn:hover { background: #E15050; color: #ffffff; }
        .danger-btn { border-color: #d9534f; color: #d9534f; }
        .danger-btn:hover { background: #d9534f; color: #ffffff; }
    </style>
</head>
<body>
    <?php include 'global_header.php'; ?>
    <div class="dashboard-container">
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
                            <form method="POST" style="margin:0;">
                                <input type="hidden" name="action" value="verify_vendor">
                                <input type="hidden" name="vendorID" value="<?= $vendor['vendorID'] ?>">
                                <button type="submit" class="action-btn">Verify</button>
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
            <h2>User Management</h2>
            <table>
                <tr><th>Client ID</th><th>Username</th><th>Status</th><th>Action</th></tr>
                <?php if ($clientsResult): ?>
                    <?php while ($client = $clientsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= $client['clientID'] ?></td>
                        <td><?= htmlspecialchars($client['username']) ?></td>
                        <td><?= $client['isBanned'] ? '<strong style="color:#d9534f;">Banned</strong>' : 'Active' ?></td>
                        <td>
                            <?php if (!$client['isBanned']): ?>
                            <form method="POST" style="margin:0;">
                                <input type="hidden" name="action" value="ban_client">
                                <input type="hidden" name="clientID" value="<?= $client['clientID'] ?>">
                                <button type="submit" class="action-btn danger-btn">Ban</button>
                            </form>
                            <?php else: ?>
                                <em>Banned</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </table>
        </div>
        
        <div class="panel">
            <h2>Platform Moderation</h2>
            <p style="color:#E15050;">Want to step in and moderate real-time communications?</p>
            <button class="action-btn" onclick="window.location.href='admin_chat.php'">Enter Admin Chat Oversight</button>
        </div>
    </div>
    <?php include 'global_footer.php'; ?>
</body>
</html>