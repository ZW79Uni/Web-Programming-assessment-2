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
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    float: left;
}

.chatSelected {
    background-image: url(chatBG.png);
    height: 40px;
    width: 430px;
    background-size: cover; 
    background-position: center;         
    box-sizing: border-box;
    transition: transform 0.3s ease;
    
}

.chatSelected img {
    margin-right: 10px;  
}

.chatSelected:hover {
    transform: scale(1.1);
}

.chatSelected:active {
    animation: shake 0.2s ease-in-out;
}

@keyframes shake {
    0% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-10px);
    }
    50% {
        transform: translateX(10px);
    }
    75% {
        transform: translateX(-10px);
    }
    100% {
        transform: translateX(0);
    }
}

.chat {
    border: 5px solid black;
    width: 2030px;  
    height: 992px;
    box-sizing: border-box;  
    overflow-y: auto;
    background-color: #fff;
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
	<?php
	$servername = "localhost";
	$username = "h6zp02h_WebAccess";
	$password = "SparrowHawk26!";
	$dbname = "h6zp02h_EMW_Database";

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

	$clientID = 1;

	$sql = "
	SELECT chat.chatID, chat.vendorID, vendor.firstName, vendor.lastName, vendor.vendorOrginisationName
	FROM chat
	JOIN vendor ON chat.vendorID = vendor.vendorID
	WHERE chat.clientID = $clientID
	";

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
    	while($row = $result->fetch_assoc()) {
        	$chatID = $row['chatID'];
        	$vendorName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
        	$vendorOrg = htmlspecialchars($row['vendorOrginisationName']);
        echo "
        <div class='chatSelected' data-chatid='$chatID'>
            <div style='display: flex; align-items: center;'>
                <img src='blank pfp.png' width='40' height='40'>
                <div>
                    Chat with: $vendorName <br>from: $vendorOrg
                </div>
            </div>
        </div>";
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
<script>
document.addEventListener('DOMContentLoaded', function() {
	const chats = document.querySelectorAll('.chatSelected');
	const chatBox = document.querySelector('.chat');
	
	chats.forEach(chat => {
		chat.addEventListener('click', function() {
			const chatID = this.getAttribute('data-chatid');
			
			fetch(`get_messages.php?chatID=${chatID}`)
    				.then(response => response.text())
    				.then(data => {
        			chatBox.innerHTML = data; // insert messages
    				})
    			.catch(err => console.error(err));
        });
    });
});
</script>
</body>
</html>