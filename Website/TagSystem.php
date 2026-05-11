<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['role']) && $_SESSION['role'] === 'client' ? 'true' : 'false';
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Matchmaking - EMW</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php include 'global_header.php'; ?>

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
    cursor:pointer;
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

.modal { 
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0; 
    top: 0; 
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.5); 
}

.modal-content { 
    background-color: #ffffff; 
    color: #E15050; 
    border: 3px solid #E15050; 
    margin: 15% auto; 
    padding: 30px; 
    width: 80%; 
    max-width: 500px; 
}

.modal input[type="date"] { 
    width: 100%; 
    padding: 12px; 
    margin-top: 10px; 
    margin-bottom: 20px; 
    box-sizing: border-box; 
    background: #ffffff; 
    color: #E15050; 
    border: 2px solid #E15050; 
    font-size: 16px; 
}

.action-btn { 
    padding: 12px; 
    background-color: #E15050; 
    color: #ffffff; 
    border: 3px solid #E15050; 
    cursor: pointer; 
    font-weight: bold; 
    font-size: 14px; 
    transition: 0.2s; 
    width: 100%; 
    text-align: center; 
    margin-bottom: 10px;
}

.action-btn:hover { 
    background-color: #ffffff; 
    color: #E15050; 
}

.secondary-btn { 
    background-color: #ffffff; 
    color: #E15050; 
}

.book-now-btn { 
    background: #E15050; 
    color: white; 
    border: none; 
    padding: 8px; 
    width: 100%; 
    font-weight: bold; 
    cursor: pointer; 
    margin-top: 10px; 
}

.book-now-btn:hover { 
    background: #c04040; 
}

@media screen and (max-width:900px){
    label.price, label.location, select.price, select.location{ position:static; }
    div.container{ width:95%; }
    #box{ margin-top:20px; justify-content:center; }
}
</style>

<div class="ex2">
    <div class="arrow" onclick="window.history.back()">
        <h1>&#8592;</h1>
    </div>
    <div class= "eventName">
        <h2><?php echo isset($_GET['event']) ? htmlspecialchars($_GET['event']) : 'CHOSEN EVENT'; ?></h2>
    </div>
    <div class="container">
        <div class="filters">
            <label class="price">Price:</label>
            <select class="price" id="price" onchange="Change()">
                <option value="Ascending">Ascending</option>
                <option value="Descending">Descending</option>
            </select>

            <label class="location">Location:</label>
            <select class="location" id="location" onchange="Change()">
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

        $ChosenEventName = isset($_GET['event']) ? trim($_GET['event']) : null;
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;

        $sql = "SELECT s.serviceID, v.vendorID, s.serviceName, v.vendorOrginisationName, s.servicePrice, a.county, et.eventTypeName
                FROM service s
                JOIN vendorServiceAllocation vsa ON s.serviceID = vsa.serviceID
                JOIN vendor v ON vsa.vendorID = v.vendorID
                JOIN eventTypeAllocation eta ON s.serviceID = eta.serviceID
                JOIN address a ON a.addressID = v.addressID
                JOIN eventType et on eta.eventTypeID = et.eventTypeID
                WHERE 1=1";

        $params = []; $types = "";
        if ($ChosenEventName) { $sql .= " AND et.eventTypeName = ?"; $params[] = $ChosenEventName; $types .= "s"; }
        if ($searchTerm) { 
            $sql .= " AND (s.serviceName LIKE ? OR v.vendorOrginisationName LIKE ?)"; 
            $likeTerm = "%$searchTerm%"; $params[] = $likeTerm; $params[] = $likeTerm; $types .= "ss"; 
        }

        $stmt = $conn->prepare($sql);
        if ($params) { $stmt->bind_param($types, ...$params); }
        $stmt->execute();
        $result = $stmt->get_result();
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = [
                'serviceID' => $row['serviceID'],
                'orgID' => $row['vendorID'],
                'serviceName' => $row['serviceName'],
                'orgName' => $row['vendorOrginisationName'],
                'orgPrice' => $row['servicePrice'],
                'orgLocation' => $row['county'],
                'serEventType' => $row['eventTypeName']
            ];
        }
        $conn->close();
        echo "<script>var services = " . json_encode($services) . ";</script>";
        ?>
        </div>
    </div>
</div>

<div id="bookingModal" class="modal">
    <div class="modal-content">
        <h3 id="modalVendorName" style="border-bottom: 2px solid #E15050; padding-bottom: 10px;">Book Vendor</h3>
        <p style="font-weight: bold;">Service:</p>
        <input type="text" id="serviceDisplayName" disabled style="width:100%; padding:10px; border:1px solid #ccc; background:#eee; margin-bottom:15px;">
        <input type="hidden" id="serviceID">
        
        <p style="font-weight: bold;">Select Event Date (yyyy-mm-dd):</p>
        <input type="date" id="eventDate" min="<?php echo $today; ?>" required>
        
        <button class="action-btn" onclick="submitBooking()">Confirm Booking</button>
        <button class="action-btn secondary-btn" onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
const isLoggedIn = <?php echo $isLoggedIn; ?>;
let currentVendorID = null;

function Change() {
    document.getElementById("box").innerHTML = "";
    const sortOrder = document.getElementById("price").value;
    let sortedServices = [...services];
    sortedServices.sort((a, b) => (sortOrder === "Ascending" ? a.orgPrice - b.orgPrice : b.orgPrice - a.orgPrice));
    suffering(sortedServices);
}

function suffering(arr) {
    const boxContainer = document.getElementById('box');
    const ChosenLocation = document.getElementById("location").value;
    
    arr.forEach(item => {
        if (item.orgLocation == ChosenLocation || ChosenLocation == "all") {
            const div = document.createElement('div');
            div.style = "width:220px; min-height:220px; background:white; border:2px solid #E15050; padding:10px; flex-shrink:0;";
            const safeName = item.orgName.replace(/'/g, "\\'");

            div.innerHTML = `
                Service: ${item.serviceName}<br>
                By: <a href="vendorDetails.php?id=${item.orgID}">${item.orgName}</a><br>
                Cost: £${item.orgPrice}<br>
                Located: ${item.orgLocation}<br>
                <button type='button' class="book-now-btn" onclick="handleBookingClick(${item.orgID}, '${safeName}', ${item.serviceID}, '${item.serviceName}')">Book Now!</button>
            `;
            boxContainer.appendChild(div);
        }
    });
}

suffering(services);
</script>
<?php include 'global_footer.php'; ?>
</body>
</html>
