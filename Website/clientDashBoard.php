<?php
// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>

<title>Client Home</title>
<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    font-family: "Segoe UI", Roboto, sans-serif;
    margin: 0;
    background: #ffffff;
    text-align: left;
}

.container {
    width: 100%;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.contentContainer {
    width: 80%;
    min-height: 90vh;
    border: 3px solid black;
    background: rgb(136, 135, 135);
    margin: 10% 0;
    display: flex;
    flex-direction: column;
    gap: 3%;
    padding: 2%;
}

.featureRow {
    width: 100%;
    display: flex;
    align-items: center;
}

.featureRow.left {
    justify-content: flex-start;
}

.featureRow.right {
    justify-content: flex-end;
}

.itemFeature {
    background: #E15050;
    color: #ffffff;
    width: 20%;
    min-height: 20vh;
    border: solid black;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    transition: transform 0.3s;
    margin: 2% 2%; 
}

.itemFeature:hover {
    transform: scale(1.1);
}

.featureRow img {
    width: 20%;
    height: 10%;
    margin-right: auto;
}

</style>

<body>

<?php include 'global_header.php'; ?>

<div class="container">
    <div class="contentContainer">
        <div class="featureRow left">
            <div class="itemFeature">
            	<a href="eventSearchPage.php"><h1>Search Events!</h1></a>   
            </div>
        </div>

        <div class="featureRow right">
            <div class="itemFeature">
                <a href="blogs.php"><h1>Find Blogs!</h1></a> 
            </div>
        </div>

        <div class="featureRow left">
            <div class="itemFeature">
                <a href="faq.php"><h1>See frequently asked questions!</h1></a>
            </div>
        </div>

        <div class="featureRow">
            <img src="chatIcon.png">
            <div class="itemFeature">
    		<?php if (isLoggedIn()): ?>
        		<a href="chat.php"><h1>Chat with people!</h1></a>
    		<?php else: ?>
        		<a href="login.php"><h1>Chat with people!</h1></a>
    		<?php endif; ?>
	</div>
        </div>
    </div>
</div>

<?php include 'global_footer.php'; ?>

</body>
</html>