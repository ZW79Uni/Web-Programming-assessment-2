<?php
// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8" />
    <title> Events Search Page</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php include 'global_header.php'; ?>
    <div class="container clearfix">
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
        				echo '<button class="button-event" data-event="'. $eventName .'">' . $eventName . '</button>';
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
                    <div class="search-container">

    		    <!-- SEARCH BAR -->

	            <div class="search-box">

                    <input
            		type="text"
            		id="searchInput"
            		placeholder="Search..."
            		onkeyup="searchItems()"
        	    >

        	    <span class="search-icon">⌕</span>

    		    </div>

    		    <!-- DROPDOWN RESULTS -->

    		   <div class="results-box" id="resultsBox"></div>
	           </div>
                   <button id="sendBtn">Search</button>
                  </div>
            </section>
</div>
<?php include 'global_footer.php'; ?>
<script>
let selectedEvent = null;

document.querySelectorAll('.button-event').forEach(button => {
    button.addEventListener('click', () => {

        if(selectedEvent) {
            selectedEvent.style.border = '2px solid #B2B2B2';
            selectedEvent.style.backgroundColor = '#ffffff';
            selectedEvent.style.color = '#E15050';
        }
        

        button.style.border = '2px solid black';
        button.style.backgroundColor = 'black';
        button.style.color = 'white';

        selectedEvent = button;
    });
});


document.getElementById('sendBtn').addEventListener('click', () => {
    const searchValue = document.getElementById('searchInput').value;
    const eventValue = selectedEvent ? selectedEvent.getAttribute('data-event') : '';
    

    const url = `TagSystem.php?event=${encodeURIComponent(eventValue)}&search=${encodeURIComponent(searchValue)}`;
    

    window.location.href = url;
});
</script>
</body>
</html>