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
<style>
/* Ensure the parent container is the anchor for the absolute positioning */
.search-container {
    position: relative;
    width: 90%;
}

.results-box{
    position: absolute;
    /* Changed from top to bottom to make it appear above */
    bottom: 100%; 
    left: 0;
    width: 100%;
    background: white;
    /* Added margin-bottom to create gap above the search box */
    margin-bottom: 5px; 
    border-radius: 15px;
    overflow: hidden;
    display: none;
    /* Reversed shadow to go upwards */
    box-shadow: 0px -4px 8px rgba(0,0,0,0.2);
    z-index: 1000;
}

.result-item{
    padding: 15px 20px;
    cursor: pointer;
    font-size: 18px;
}

.result-item:hover{
    background: #f1f1f1;
}

a:link {
  color: white;
}

.aboutusHeader {
    border: 3px solid black;
    text-align: center;
    width: 500px;
    height: 150px;
    float: left;
}
.eventHeader {
    border: 3px solid black;
    text-align: center;
    width: 500px;
    height: 150px;
    float: right;
}

button {
    background-color: #ffffff;
    border: 2PX solid #B2B2B2;
    color: #E15050;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    cursor: pointer;
}

body {
    font-family: "Segoe UI", Roboto, sans-serif;
    margin: 0;
    background: #ffffff;
    color: #E15050;
    text-align: left;
}

.container {
    width: 100%;
    min-height: 100vh;
}

.clearfix::after {
    content: "";
    clear: both;
    display: table;
}

.eventHolder {
    margin-top: 10px;
    border: 3px solid black;
    width: 450px;
    height: 350px;
    margin-left: 960px;
    text-align: center;
}

.searchEvent {
    margin-top: 10px;
    border: 3px solid black;
    width: 450px;
    height: 350px;
    margin-left: 960px;
    text-align: center;
}

.search-box{
    width: 100%;
    background: white;
    border-radius: 30px;
    display: flex;
    align-items: center;
    padding: 12px 20px;
    box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
    box-sizing: border-box;
}

.search-box input{
    flex: 1;
    border: none;
    outline: none;
    font-size: 18px;
}

.search-icon{
    font-size: 22px;
    cursor: pointer;
}
    
.button-event:active {
	background-color: black;
        color: white;
}

footer {
    background: linear-gradient(120deg, #E15050, #E15050);
    color: #ffffff;
    padding: 60px 20px;
    text-align: left;
}
</style>
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

    		    <div class="search-box">

                    <input
            		type="text"
            		id="searchInput"
            		placeholder="Search..."
            		onkeyup="searchItems()"
            	    >

            	    <span class="search-icon">⌕</span>

    		    </div>

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

/* SEARCH DATA */

const events = [

    "Wedding",
    "Wedding Dresses",
    "Wedding Planner",
    "Birthday Party",
    "Corporate Event",
    "Music Festival",
    "DJ Services",
    "Catering",
    "Photographer",
    "Conference",
    "Decorator",
    "Food Vendor",
    "kent",
    "Sussex",
    "Surrey"

];

/* SEARCH FUNCTION */

function searchItems(){

    const input = document
        .getElementById("searchInput")
        .value
        .toLowerCase();

    const resultsBox = document.getElementById("resultsBox");

    /* HIDE RESULTS IF EMPTY */

    if(input === ""){

        resultsBox.style.display = "none";
        resultsBox.innerHTML = "";
        return;

    }

    /* FILTER RESULTS */

    const filtered = events.filter(function(item){

        return item.toLowerCase().includes(input);

    });

    /* SHOW RESULTS */

    if(filtered.length > 0){

        resultsBox.style.display = "block";

        resultsBox.innerHTML = filtered.map(function(item){

            return `
                <div class="result-item">
                    ${item}
                </div>
            `;

        }).join("");

    }
    else{

        resultsBox.style.display = "block";

        resultsBox.innerHTML = `
            <div class="result-item">
                No results found
            </div>
        `;

    }

}

</script>
</body>
</html>