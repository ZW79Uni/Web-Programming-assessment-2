<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$userID = intval($_SESSION['user_id']);
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html>

<title>Vendor Chat Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="style.css">

<style>

.container {
    width: 100%;
    height: calc(100vh - 40vh);
    border:5px solid black;
    box-sizing: border-box;  
    display: flex;
    flex-wrap: nowrap;
}

.Logo {
    border:5px solid black;
    width: 20%;
    height: auto;
    box-sizing: border-box;
}

.Nav {
    border:5px solid black;
    width: 80%;
    height: auto;
    box-sizing: border-box;  
}

.chatPicker {
    border:5px solid black;
    width: 20%;
    height: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    padding: 10px;
}

.chatSelected {
    color: #ffffff;
    width: 100%;
    display: flex;
    align-items: center;
    padding: 5px 10px;
    margin-bottom: 5px;
    box-sizing: border-box;
    transition: transform 0.3s ease;
    background-color: #E15050;
}

.chatSelected div {
    width: 100%;
}

.chatSelected img {
    flex-shrink: 0;
    margin-right: 10px;  
}

.chatSelected:hover {
    transform: scale(1.1);
}

.chatSelected:active {
    animation: shake 0.2s ease-in-out;
}

@keyframes shake {
    0% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    50% { transform: translateX(10px); }
    75% { transform: translateX(-10px); }
    100% { transform: translateX(0); }
}

.chat {
    border: 5px solid black;
    width: 80%;  
    height: 100%;
    box-sizing: border-box;  
    overflow-y: auto;
    background-color: #fff;
    display: flex;
    flex-direction: column;
}

.clearfix::after {
    content: "";
    clear: both;
    display: table;
}

.message {
    border: 1px solid red;
    padding: 5px;
    margin: 5px;
}

footer {
    background: linear-gradient(120deg, #E15050, #E15050);
    color: #ffffff;
    padding: 10vh 2vw;
    text-align: left;
}

#messageInputArea {
    display: flex;
    border-top: 2px solid black;
    padding: 10px;
}

#messageInput {
    width: 80%;
}

#sendBtn {
    width: 18%;
    margin-left: 2%;
}
</style>

<?php include 'global_header.php'; ?>

<body>
<div class="container clearfix">
    <div class="chatPicker">
        <h3>Select A chat!</h3>
        <?php
        $servername = "localhost";
        $username = "h6zp02h_WebAccess";
        $password = "SparrowHawk26!";
        $dbname = "h6zp02h_EMW_Database";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

        if ($role === 'client') {
            $sql = "
            SELECT chat.chatID, chat.vendorID, vendor.firstName, vendor.lastName, vendor.vendorOrginisationName
            FROM chat
            JOIN vendor ON chat.vendorID = vendor.vendorID
            WHERE chat.clientID = $userID
            ";
        } elseif ($role === 'vendor') {
            $sql = "
            SELECT chat.chatID, chat.clientID, client.firstName, client.lastName
            FROM chat
            JOIN client ON chat.clientID = client.clientID
            WHERE chat.vendorID = $userID
            ";
        } else {
            $sql = "
            SELECT chat.chatID, chat.clientID, chat.vendorID, client.firstName AS clientFirst, client.lastName AS clientLast,
                   vendor.firstName AS vendorFirst, vendor.lastName AS vendorLast
            FROM chat
            JOIN client ON chat.clientID = client.clientID
            JOIN vendor ON chat.vendorID = vendor.vendorID
            ";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $chatID = $row['chatID'];
                if ($role === 'client') {
                    $name = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
                    $org = htmlspecialchars($row['vendorOrginisationName']);
                    $display = "Chat with: $name <br>from: $org";
                } elseif ($role === 'vendor') {
                    $name = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
                    $display = "Chat with: $name";
                } else {
                    $clientName = htmlspecialchars($row['clientFirst'] . ' ' . $row['clientLast']);
                    $vendorName = htmlspecialchars($row['vendorFirst'] . ' ' . $row['vendorLast']);
                    $display = "Chat: $clientName ↔ $vendorName";
                }
                echo "
                <div class='chatSelected' data-chatid='$chatID'>
                    <div style='display: flex; align-items: center;'>
                        <img src='blank pfp.png' width='40' height='40'>
                        <div>$display</div>
                    </div>
                </div>";
            }
        } else {
            echo "No results found";
        }

        $conn->close();
        ?>
    </div>

    <div class="chat">
        <div id="messagesArea">
            <h2>Select a chat and get planning!</h2>
        </div>

        <div id="messageInputArea">
            <input type="text" id="messageInput" placeholder="Type a message...">
            <button id="sendBtn">Send</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chats = document.querySelectorAll('.chatSelected');
    const chatBox = document.querySelector('#messagesArea');
    const sendBtn = document.getElementById('sendBtn');
    const messageInput = document.getElementById('messageInput');

    let currentChatID = null;

    chats.forEach(chat => {
        chat.addEventListener('click', function() {
            currentChatID = this.getAttribute('data-chatid');

            fetch(`get_messages.php?chatID=${currentChatID}`)
                .then(response => response.text())
                .then(data => {
                    chatBox.innerHTML = data;
                })
                .catch(err => console.error(err));
        });
    });

    sendBtn.addEventListener('click', function() {
        const message = messageInput.value.trim();

        if (!message || !currentChatID) return;

        fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `chatID=${currentChatID}&message=${encodeURIComponent(message)}`
        })
        .then(response => response.text())
        .then(() => {
            messageInput.value = '';
            return fetch(`get_messages.php?chatID=${currentChatID}`);
        })
        .then(response => response.text())
        .then(data => {
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(err => console.error(err));
    });
});
</script>

<?php include 'global_footer.php'; ?>

</body>
</html>