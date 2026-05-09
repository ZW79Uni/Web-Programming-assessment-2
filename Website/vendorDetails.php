<!DOCTYPE html>

<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
header {
    background: #E15050;
    color: #ffffff;
    padding: 20px 2vw;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    box-sizing: border-box;
}

.container {
    width: 100%;
    height: 70vh;
    border:5px solid black;
    box-sizing: border-box;  
    display: flex;
    flex-wrap: nowrap;
    flex-direction: column;
}

.vendorTitle {
    width: 100%;
    height: 10%;
    box-sizing: border-box;  
    display: flex;
    flex-wrap: nowrap;
    border:5px solid black;
}

.vendorDescTitle {
    width: 100%;
    height: 10%;
    box-sizing: border-box;  
    display: flex;
    flex-wrap: nowrap;
    border:5px solid black;
}

.vendorDesc {
    width: 100%;
    height: 80%;
    box-sizing: border-box;  
    display: flex;
    flex-wrap: nowrap;
    border:5px solid black;
}

.clearfix::after {
    content: "";
    clear: both;
    display: table;
}

footer {
    background: linear-gradient(120deg, #E15050, #E15050);
    color: #ffffff;
    padding: 10vh 2vw;
    text-align: left;
}
</style>
<body>
<header>
<h1>Events Meets World</h1>
<button>Return Home</button>
</header>
<div class="container clearfix">
<?php
if (isset($_GET['id'])) {
    $vendorID = (int)$_GET['id'];
    
    $servername = "localhost";
    $username = "h6zp02h_WebAccess";
    $password = "SparrowHawk26!";
    $dbname = "h6zp02h_EMW_Database";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("SELECT * FROM vendor WHERE vendorID = ?");
    $stmt->bind_param("i", $vendorID); // "i" means the variable is an integer
    $stmt->execute();

    $result = $stmt->get_result();
    $vendor = $result->fetch_assoc();

    if ($vendor) {
        $name = $vendor['vendorOrginisationName'];
        $desc = $vendor['vendorDescription'];
    } else {
        echo "Vendor not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No vendor selected.";
}
?>
<div class = "vendorTitle">
	<h1><?php echo htmlspecialchars($vendor['vendorOrginisationName']); ?></h1>
</div>

<div class = "vendorDescTitle">
	<h3>About this Vendor</h3>
</div>

<div class = "vendorDesc">
	<p><?php echo htmlspecialchars($vendor['vendorDescription']); ?></p>
</div>

</div>
<footer>
<h1>About us, Accolades, etc...</h1>
</footer>
</body>
</html>