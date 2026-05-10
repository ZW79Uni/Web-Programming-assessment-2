<html>
<body>
<style>
div.ex2 {
  max-width: 960px;
  height: 1000px;
  margin: auto;
  border: 3px solid #2B2B28;
}    

div.container {
    padding: 10px;
    background-color: #fff;
    border: 3px solid #2B2B28;
    width: 864px;
    max-width:864px;
    height: 900px;
    max-height: 900px;
    margin:0 auto;
    overflow:auto;
}

div.box {
    padding: 10px;
    background-color: #fff;
    border: 3px solid #2B2B28;
    width: 216px;
    max-width: 216px;
    margin:0 auto;
    overflow:auto;
}

section.boxSide {
    padding: 10px;
    background-color: #fff;
    border: 3px solid #2B2B28;
    width: 216px;
    max-width: 216px;
    margin:0 auto;
    overflow:auto;
    display: inline-block;
}
label.price {
    position: absolute;
    top: 20px;
    right: 700px;
    font-size: 20px
}
label.location{
    position: absolute;
    top: 20px;
    right: 500px;
    font-size: 20px
}
select.price{
    position: absolute;
    top: 24px;
    right: 600px;
}
select.location{
    position: absolute;
    top: 24px;
    right: 430px;
}

</style>

<div class="ex2">
    <div class="container">
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
       <button type="button">Checkout!</button>
       <div id="box">
            
        <?php
        // change this back to the original database variables
	$servername = "localhost";
	$username = "h6zp02h_WebAccess";
	$password = "SparrowHawk26!";
	$dbname = "h6zp02h_EMW_Database";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

        // change this to whatever was the selected on the previous page.
        $ChosenEvent= 1;

        $sql= "
        SELECT s.serviceID, v.vendorID, s.serviceName, v.vendorOrginisationName, s.servicePrice, a.county, et.eventTypeName
        FROM service s
        JOIN vendorServiceAllocation vsa ON s.serviceID = vsa.serviceID
        JOIN vendor v ON vsa.vendorID = v.vendorID
        JOIN eventTypeAllocation eta ON s.serviceID = eta.serviceID
        JOIN address a ON a.addressID = v.addressID
        JOIN eventType et on eta.eventTypeID = et.eventTypeID
        WHERE eta.eventTypeID = $ChosenEvent
        ";

        $result = $conn->query($sql);

        $services = [];
 
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
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
        let ActualLocation = arr[x].orgLocation;
        
        if (ActualLocation == ChosenLocation || ChosenLocation == "all") {
            const div = document.createElement('div');
            div.style.width = '200px';
            div.style.height = '200px';
            div.style.marginTop = '50px';
            div.style.backgroundColor = 'white';
            div.style.borderColor = "black";
            div.style.border = "solid";
            div.style.display = "inline-block";
            div.style.marginRight = "20px";
            div.style.marginLeft = "50px";

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

</body>
</html>

