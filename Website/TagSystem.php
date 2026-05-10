<?php
// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<html>
    
<?php include 'global_header.php'; ?>
<link rel="stylesheet" href="style.css">
    
<body>
<style>
body{
    margin:0;
    padding:20px;
    background:#f2f2f2;
    font-family:serif;
    box-sizing:border-box;
}

*{
    box-sizing:border-box;
}

div.ex2{
    width:100%;
    max-width:1400px;
    min-height:100vh;
    margin:auto;
    border:3px solid #E15050;
    position:relative;
    padding:20px;
}

div.arrow{
    width:100px;
    height:100px;
}

h1{
    font-size:50px;
    margin:0;
}

h2{
    font-size:50px;
    margin:0;
    text-align:center;
}

div.eventName{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    margin-top:-70px;
    margin-bottom:40px;
}

div.container{
    width:70%;
    min-height:900px;
    border:3px solid #E15050;
    margin:0 auto;
    padding:20px;
    background:#f2f2f2;
    position:relative;
}

div.box{
    width:100%;
}

section.boxSide{
    padding:10px;
    background-color:#fff;
    border:3px solid #E15050;
    width:216px;
    overflow:auto;
    display:inline-block;
}

label.price{
    position:absolute;
    top:20px;
    right:260px;
    font-size:20px;
}

label.location{
    position:absolute;
    top:20px;
    right:70px;
    font-size:20px;
}

select.price{
    position:absolute;
    top:24px;
    right:150px;
}

select.location{
    position:absolute;
    top:24px;
    right:0;
}

#box{
    margin-top:70px;
    display:flex;
    flex-wrap:wrap;
    gap:30px;
}

@media screen and (max-width:1200px){

    div.container{
        width:85%;
    }
}

@media screen and (max-width:900px){

    h2{
        font-size:40px;
    }

    div.container{
        width:95%;
    }

    label.price,
    label.location,
    select.price,
    select.location{
        position:static;
    }

    .filters{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        justify-content:center;
        align-items:center;
        margin-bottom:20px;
    }

    #box{
        margin-top:20px;
        justify-content:center;
    }
}

@media screen and (max-width:600px){

    h1{
        font-size:40px;
    }

    h2{
        font-size:30px;
    }

    div.eventName{
        margin-top:-50px;
    }
}

</style>

<div class="ex2">
    <div class="arrow" onclick="window.history.back()">
        <h1>&#8592;</h1>
    </div>
    <div class= "eventName">
        <!-- PLEASE MAKE THIS DYNAMIC WITH THE PREVIOUS SELECTED RESPONSE" -->
        <h2> CHOSEN EVENT </h2>
    </div>
    <div class="container">
    <div class="filters">

        <label class="price">Price:</label>
            <select class = "price" name="price" id="price" onchange= "Change()">
            <option value="Ascending">Ascending</option>
            <option value="Descending">Descending</option>
            </select>

        <label class="location">Location:</label>
            <select class = "location" name="location" id="location" onchange= "Change()">
                <option value="all">All</option>
                <option value="Kent">Kent</option>
                <option value="London">London</option>
                <option value="Essex">Essex</option>
            </select>

    </div>
       <div id="box">
            
    <?php

	$servername = "localhost";
	$username = "h6zp02h_WebAccess";
	$password = "SparrowHawk26!";
	$dbname = "h6zp02h_EMW_Database";

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


		$ChosenEventName = isset($_GET['event']) ? trim($_GET['event']) : null; // now using name
		$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;


	$sql = "
	SELECT s.serviceID, v.vendorID, s.serviceName, v.vendorOrginisationName, s.servicePrice, a.county, et.eventTypeName
	FROM service s
	JOIN vendorServiceAllocation vsa ON s.serviceID = vsa.serviceID
	JOIN vendor v ON vsa.vendorID = v.vendorID
	JOIN eventTypeAllocation eta ON s.serviceID = eta.serviceID
	JOIN address a ON a.addressID = v.addressID
	JOIN eventType et on eta.eventTypeID = et.eventTypeID
	WHERE 1=1
	";

	$params = [];
	$types = "";

	if ($ChosenEventName) {
   	 $sql .= " AND et.eventTypeName = ?";
    	$params[] = $ChosenEventName;
    	$types .= "s"; // string
	}

	if ($searchTerm) {
	    $sql .= " AND (s.serviceName LIKE ? OR v.vendorOrginisationName LIKE ?)";
    	    $likeTerm = "%$searchTerm%";
    	    $params[] = $likeTerm;
    	    $params[] = $likeTerm;
            $types .= "ss"; // two strings
	}

	$stmt = $conn->prepare($sql);
	if ($params) {
    		$stmt->bind_param($types, ...$params);
	}

	$stmt->execute();
	$result = $stmt->get_result();

	$services = [];
	if ($result && $result->num_rows > 0) {
    		while ($row = $result->fetch_assoc()) {
        		$services[] = [
            		'orgID' => $row['vendorID'],
            		'serviceName' => $row['serviceName'],
            		'orgName' => $row['vendorOrginisationName'],
            		'orgPrice' => $row['servicePrice'],
            		'orgLocation' => $row['county'],
            		'serEventType' => $row['eventTypeName']
        		];
    		}
	}

	$conn->close();
	echo "<script>var services = " . json_encode($services) . ";</script>";
	?>
        
       </div>
    </div>
</div>


<script>

function Change() {
    const oldElement = document.getElementById("box");
    oldElement.remove();
    
    const newElement = document.createElement("div");
    newElement.id = "box";
    document.querySelector(".container").appendChild(newElement);

    const sortOrder = document.getElementById("price").value;

    let sortedServices = [...services];
    sortedServices.sort((a, b) => {
        if(sortOrder === "Ascending") {
            return a.orgPrice - b.orgPrice;
        } else {
            return b.orgPrice - a.orgPrice;
        }
    });

    suffering(sortedServices);
}

function suffering(arr) {
    const boxContainer = document.getElementById('box');
    const ChosenLocation = document.getElementById("location").value;
    for (var x = 0; x < arr.length; x++) {
        let ActualLocation = arr[x].orgLocation
        if (ActualLocation == ChosenLocation || ChosenLocation == "all"){
            const div = document.createElement('div');
            div.style.width = '220px';
            div.style.minHeight = '200px';
            div.style.backgroundColor = 'white';
            div.style.border = "2px solid #E15050";
            div.style.padding = "10px";
            div.style.flexShrink = "0";

            div.innerHTML = `
                Service: ${arr[x].serviceName}<br>
                By: <a href="vendorDetails.php?id=${arr[x].orgID}">${arr[x].orgName}</a><br>
                Cost: ${arr[x].orgPrice}<br>
                Located: ${arr[x].orgLocation}<br>
                Type: ${arr[x].serEventType}<br>
                <button type='button'>Add to cart!</button>
`;

            boxContainer.appendChild(div);
        }
    }
}

suffering(services);
</script>
<?php include 'global_footer.php'; ?>
</body>
</html>

