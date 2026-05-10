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
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$adminID = $_SESSION['adminID'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $chatID = intval($_POST['chatID']);
    $messageContent = trim($_POST['messageContent']);
    
    if (!empty($messageContent)) {
        $stmt = $conn->prepare("INSERT INTO `message` (`chatID`, `senderID`, `senderType`, `messageContent`) VALUES (?, ?, 'admin', ?)");
        $stmt->bind_param("iis", $chatID, $adminID, $messageContent);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_chat.php?chatID=" . $chatID);
    exit;
}

$chatsResult = $conn->query("
    SELECT c.chatID, cl.firstName AS clientFirst, cl.lastName AS clientLast, v.vendorOrginisationName
    FROM chat c
    JOIN client cl ON c.clientID = cl.clientID
    JOIN vendor v ON c.vendorID = v.vendorID
");

$selectedChatID = isset($_GET['chatID']) ? intval($_GET['chatID']) : null;
$messagesResult = null;

if ($selectedChatID) {
    $stmt = $conn->prepare("SELECT senderID, senderType, messageContent, messageID FROM message WHERE chatID = ? ORDER BY messageID ASC");
    $stmt->bind_param("i", $selectedChatID);
    $stmt->execute();
    $messagesResult = $stmt->get_result();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Chat Oversight - EMW</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-chat-container {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 30px;
            height: 70vh;
        }
        
        .chat-list {
            flex: 1;
            border: 3px solid #E15050;
            background-color: white;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .chat-list h3 {
            margin: 0;
            padding: 15px;
            background: #E15050;
            color: #ffffff;
            text-align: center;
        }
        
        .chat-box {
            flex: 2;
            border: 3px solid #E15050;
            background-color: white;
            display: flex;
            flex-direction: column;
        }
        
        .chat-item {
            padding: 15px;
            border-bottom: 1px solid #fcebeb;
            cursor: pointer;
            display: block;
            text-decoration: none;
            color: #E15050;
            transition: background 0.2s;
        }
        
        .chat-item:hover { background-color: #fcebeb; }
        .chat-item.active { background-color: #E15050; color: white; }
        
        .messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .message-row { display: flex; flex-direction: column; }
        .message-sender { font-weight: bold; font-size: 0.85em; margin-bottom: 3px; color: #E15050; }
        .admin-sender { color: black; } 
        .message-content { 
            padding: 10px 15px; 
            border: 1px solid #E15050; 
            background-color: #fcebeb; 
            border-radius: 4px; 
            display: inline-block; 
            max-width: 80%; 
            color: #E15050;
        }
        .msg-admin-body { background-color: #000; color: #fff; border-color: #000; }
        
        .chat-input {
            padding: 15px;
            display: flex;
            gap: 10px;
            border-top: 3px solid #E15050;
        }
        
        .chat-input input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 2px solid #E15050;
            color: #E15050;
        }
        
        .chat-input button {
            padding: 10px 25px;
            background-color: #000000;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        .chat-input button:hover { background-color: #333; }
    </style>
</head>
<body>

<?php include 'global_header.php'; ?>

<div class="admin-chat-container">
    <div class="chat-list">
        <h3>Active System Chats</h3>
        <?php if ($chatsResult && $chatsResult->num_rows > 0): ?>
            <?php while ($chat = $chatsResult->fetch_assoc()): ?>
                <a href="admin_chat.php?chatID=<?= $chat['chatID'] ?>" class="chat-item <?= ($selectedChatID == $chat['chatID']) ? 'active' : '' ?>">
                    <div><strong>Chat #<?= $chat['chatID'] ?></strong></div>
                    <div style="font-size:0.9em; margin-top:5px;">
                        <?= htmlspecialchars($chat['clientFirst'] . ' ' . $chat['clientLast']) ?> 
                        <br>↔<br> 
                        <?= htmlspecialchars($chat['vendorOrginisationName']) ?>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="padding: 20px; text-align: center; color: #E15050;">No active chats found.</p>
        <?php endif; ?>
    </div>
    
    <div class="chat-box">
        <?php if ($selectedChatID && $messagesResult): ?>
            <div class="messages">
                <?php while ($msg = $messagesResult->fetch_assoc()): ?>
                    <div class="message-row" style="align-items: <?= $msg['senderType'] === 'admin' ? 'center' : 'flex-start' ?>;">
                        <div class="message-sender <?= $msg['senderType'] === 'admin' ? 'admin-sender' : '' ?>">
                            <?= strtoupper($msg['senderType']) ?> (ID: <?= $msg['senderID'] ?>)
                        </div>
                        <div class="message-content <?= $msg['senderType'] === 'admin' ? 'msg-admin-body' : '' ?>">
                            <?= htmlspecialchars($msg['messageContent']) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <form class="chat-input" method="POST" action="admin_chat.php">
                <input type="hidden" name="action" value="send_message">
                <input type="hidden" name="chatID" value="<?= $selectedChatID ?>">
                <input type="text" name="messageContent" placeholder="Type an authoritative admin oversight message..." required>
                <button type="submit">Override</button>
            </form>
        <?php else: ?>
            <div style="display:flex; justify-content:center; align-items:center; height:100%; color:#E15050; font-weight: bold;">
                Select a chat from the left to monitor and intervene.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'global_footer.php'; ?>

</body>
</html>