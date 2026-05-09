<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}
?>
<!DOCTYPE html>
<html>

<title>Client Home</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    font-family: "Segoe UI", Roboto, sans-serif;
    margin: 0;
    background: #ffffff;
    text-align: left;
}

header {
    background: #E15050;
    color: #ffffff;
    padding: 60px 20px;
    width: 100%;
}

footer {
    background: linear-gradient(120deg, #E15050, #E15050);
    color: #ffffff;
    padding: 60px 20px;
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

.navigationHeader {
    background: linear-gradient(120deg, #E15050, #E15050);
    display: flex;
    width: 100%;
}

.navChild {
    flex: 1;
    border: solid black;
    text-align: center;
    height: 38px;
    width: 25%;
    colour: white;
    font-weight: bold;
}

</style>

<body>
<header>
    <h1>Events Meets World</h1>
    <button><a href="login.php">Log In!</a></button>
</header>

<div class="navigationHeader">
    <div class="navChild"><a href="about.html">ABOUT US</a></div>
<div class="navChild"><a href="#">EVENT</a></div>
    <div class="navChild"><a href="faq.html">FAQ</a></div>
    <div class="navChild"><a href="#">EVENTS MEETS WORLD</a></div>
 </div>

<div class="container">
    <div class="contentContainer">
        <div class="featureRow left">
            <div class="itemFeature">
            	<a href="HTMLPage1.php"><h1>Search Events!</h1></a>   
            </div>
        </div>

        <div class="featureRow right">
            <div class="itemFeature">
                <a href="#"><h1>Find Blogs!</h1></a> 
            </div>
        </div>

        <div class="featureRow left">
            <div class="itemFeature">
                <a href="faq.html"><h1>See frequently asked questions!</h1></a>
            </div>
        </div>

        <div class="featureRow">
            <img src="chatIcon.png">
            <div class="itemFeature">
    		<?php if (isLoggedIn()): ?>
        		<a href="chat.PHP"><h1>Chat with people!</h1></a>
    		<?php else: ?>
        		<a href="login.php"><h1>Chat with people!</h1></a>
    		<?php endif; ?>
	</div>
        </div>
    </div>
</div>

<footer>
    <h1>About us, Accolades, etc...</h1>
</footer>
</body>
</html>