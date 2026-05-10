<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Vendor Profile - EMW</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .container { width: 100%; max-width: 1200px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        .vendor-header { border: 5px solid #E15050; padding: 30px; background: #ffffff; text-align: center; margin-bottom: 30px; }
        .vendor-header h1 { color: #E15050; margin: 0 0 10px 0; font-size: 36px; }
        .vendor-header p { color: #666; font-size: 18px; margin: 0; }
        
        .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        
        .about-section { border: 3px solid #E15050; padding: 25px; background: #ffffff; }
        .about-section h3 { color: #E15050; border-bottom: 2px solid #E15050; padding-bottom: 10px; margin-top: 0; }
        .about-section p { color: #333; line-height: 1.6; }

        .services-section { border: 3px solid #E15050; padding: 25px; background: #ffffff; }
        .services-section h3 { color: #E15050; border-bottom: 2px solid #E15050; padding-bottom: 10px; margin-top: 0; }
        .service-item { border-bottom: 1px dashed #E15050; padding: 15px 0; }
        .service-item:last-child { border-bottom: none; }
        .service-item h4 { margin: 0 0 5px 0; color: #333; }
        .service-price { color: #E15050; font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>

<?php include 'global_header.php'; ?>

<div class="container">
<?php
if (isset($_GET['id'])) {
    $vendorID = (int)$_GET['id'];
    
    $servername = "localhost";
    $username = "h6zp02h_WebAccess";
    $password = "SparrowHawk26!";
    $dbname = "h6zp02h_EMW_Database";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Fetch Vendor Info
    $stmt = $conn->prepare("SELECT v.*, a.city, a.county FROM vendor v JOIN address a ON v.addressID = a.addressID WHERE vendorID = ?");
    $stmt->bind_param("i", $vendorID);
    $stmt->execute();
    $vendor = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($vendor) {
        // Fetch Services
        $svcStmt = $conn->prepare("SELECT s.serviceName, s.servicePrice, s.serviceDecription FROM service s JOIN vendorServiceAllocation vsa ON s.serviceID = vsa.serviceID WHERE vsa.vendorID = ? AND s.isAvailable = 1");
        $svcStmt->bind_param("i", $vendorID);
        $svcStmt->execute();
        $services = $svcStmt->get_result();
        $svcStmt->close();
        ?>

        <div class="vendor-header">
            <h1><?php echo htmlspecialchars($vendor['vendorOrginisationName']); ?></h1>
            <p>📍 <?php echo htmlspecialchars($vendor['city'] . ', ' . $vendor['county']); ?></p>
        </div>

        <div class="content-grid">
            <div class="about-section">
                <h3>About this Vendor</h3>
                <p><?php echo nl2br(htmlspecialchars($vendor['vendorDescription'])); ?></p>
            </div>

            <div class="services-section">
                <h3>Available Services</h3>
                <?php if ($services->num_rows > 0): ?>
                    <?php while($svc = $services->fetch_assoc()): ?>
                        <div class="service-item">
                            <h4><?php echo htmlspecialchars($svc['serviceName']); ?></h4>
                            <div class="service-price">£<?php echo number_format($svc['servicePrice'], 2); ?></div>
                        </div>
                    <?php endwhile; ?>
                    <a href="events.php?search=<?php echo urlencode($vendor['vendorOrginisationName']); ?>"><button style="width:100%; padding:10px; margin-top:20px; background:#E15050; color:white; border:none; cursor:pointer; font-weight:bold;">Book via Matchmaker</button></a>
                <?php else: ?>
                    <p>No active services listed.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php
    } else {
        echo "<h2 style='color:#E15050; text-align:center;'>Vendor not found.</h2>";
    }
    $conn->close();
} else {
    echo "<h2 style='color:#E15050; text-align:center;'>No vendor selected.</h2>";
}
?>
</div>

<?php include 'global_footer.php'; ?>
</body>
</html>