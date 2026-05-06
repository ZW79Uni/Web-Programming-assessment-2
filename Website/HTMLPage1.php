<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8" />
    <title> Events Search Page</title>
    <link rel="stylesheet" href="style.css?v=127">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="container clearfix">
        <header>
            <h1>
                Events Meets World
            </h1>
            <button style="float:right">Log In</button>

        </header>
        <div class="navigationHeader">
            <div class="navChild"><a href="about.html">ABOUT US</a></div>
	    <div class="navChild"><a href="#">EVENT</a></div>
            <div class="navChild"><a href="faq.html">FAQ</a></div>
            <div class="navChild"><a href="#">EVENTS MEETS WORLD</a></div>
        </div>
            <section>
                <div class="eventHolder">
                    <h2>
                        What type of event are you looking to host?
                    </h2>
                    <?php
                        $servername = "localhost";
	                    $username = "h6zp02h_WebAccess";
	                    $password = "SparrowHawk26!";
	                    $dbname = "h6zp02h_EMW_Database";

	                    $conn = new mysqli($servername, $username, $password, $dbname);
                    	if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

	                    $sql = "SELECT * FROM `eventType`";

	                    $result = $conn->query($sql);

	                    if ($result->num_rows > 0) {
    	                    while($row = $result->fetch_assoc()) {
        	                    $eventName = htmlspecialchars($row['eventTypeName']);
                                    echo '<button class="button event">' . $eventName . '</button>';
        	           }
                           } else {
    		           	echo "No results found";
    	             }

	             $conn->close();
	             ?>
                </div>

            </section>

            <section>
                <div class="searchEvent">
                    <h2>
                        Cant find the event and/or looking for a specific vendor?
                    </h2>
                    <input type="text" placeholder="Search..." style="width:80%;">
                    <button id="sendBtn">Search</button>
                </div>
            </section>
</div>
<footer>
	<h1>
            About us, Accolades, etc...
        </h1>
</footer>
</body>
</html>