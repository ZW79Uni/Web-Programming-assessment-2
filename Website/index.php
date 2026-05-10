<?php
session_start();

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);

// Fetch Top Rated Vendors (Calculating actual averages from the review table)
$topVendorsQuery = "
    SELECT v.vendorID, v.vendorOrginisationName, v.vendorDescription, 
           COALESCE(ROUND(AVG(r.rating), 1), 0) AS avgRating, 
           COUNT(r.reviewID) AS reviewCount
    FROM vendor v
    LEFT JOIN review r ON v.vendorID = r.vendorID
    WHERE v.isVerified = 1
    GROUP BY v.vendorID
    ORDER BY avgRating DESC, reviewCount DESC
    LIMIT 4
";
$topVendors = $conn->query($topVendorsQuery);

// Fetch Popular Services/Events for the carousel
$popularEventsQuery = "
    SELECT s.serviceID, s.serviceName, s.servicePrice, v.vendorOrginisationName
    FROM service s
    JOIN vendorServiceAllocation vsa ON s.serviceID = vsa.serviceID
    JOIN vendor v ON vsa.vendorID = v.vendorID
    WHERE s.isAvailable = 1
    LIMIT 8
";
$popularEvents = $conn->query($popularEventsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Events Meets World</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .hero-section {
            background-color: #fcebeb;
            border-bottom: 3px solid #E15050;
            padding: 60px 20px;
            text-align: center;
        }
        .hero-section h1 {
            color: #E15050;
            font-size: 3em;
            margin: 0 0 10px 0;
            font-style: italic;
        }
        .hero-section p {
            color: #E15050;
            font-size: 1.2em;
            max-width: 600px;
            margin: 0 auto 30px auto;
        }
        .cta-btn {
            background-color: #E15050;
            color: #ffffff;
            border: 3px solid #E15050;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .cta-btn:hover {
            background-color: #ffffff;
            color: #E15050;
        }

        .section-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .section-title {
            color: #E15050;
            border-bottom: 3px solid #E15050;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        /* Carousel Implementation */
        .carousel-container {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 20px;
            scroll-snap-type: x mandatory;
        }
        .carousel-item {
            flex: 0 0 300px;
            scroll-snap-align: start;
            border: 3px solid #E15050;
            background: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .carousel-item h3 { margin-top: 0; color: #E15050; }
        
        /* Grid Implementation */
        .vendor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .vendor-card {
            border: 3px solid #E15050;
            padding: 20px;
            background: #ffffff;
            text-align: center;
        }
        .vendor-card h3 { color: #E15050; margin-top: 0; }
        .stars { color: #f39c12; font-size: 1.2em; letter-spacing: 2px; }
        
        /* Hide scrollbar for cleaner look */
        .carousel-container::-webkit-scrollbar { height: 8px; }
        .carousel-container::-webkit-scrollbar-track { background: #fcebeb; }
        .carousel-container::-webkit-scrollbar-thumb { background: #E15050; }
    </style>
</head>
<body>

    <?php include 'global_header.php'; ?>

    <div class="hero-section">
        <h1>EVENTS MEETS WORLD</h1>
        <p>Connecting you with top-rated vendors, premium equipment hire, and seamless event planning all in one place.</p>
        <a href="eventSearchPage.php" class="cta-btn">Start Matchmaking</a>
    </div>

    <div class="section-container">
        <h2 class="section-title">Popular Events & Services</h2>
        <div class="carousel-container">
            <?php if ($popularEvents && $popularEvents->num_rows > 0): ?>
                <?php while ($event = $popularEvents->fetch_assoc()): ?>
                    <div class="carousel-item">
                        <div style="height: 150px; background-color: #fcebeb; border: 1px solid #E15050; margin-bottom: 15px; display:flex; align-items:center; justify-content:center; color:#E15050; font-weight:bold;">
                            [Image Placeholder]
                        </div>
                        <h3><?= htmlspecialchars($event['serviceName']) ?></h3>
                        <p style="color: #666; font-size: 14px; flex-grow: 1;">Provided by: <?= htmlspecialchars($event['vendorOrginisationName']) ?></p>
                        <p style="color: #E15050; font-weight: bold; font-size: 18px; margin: 10px 0 0 0;">£<?= htmlspecialchars($event['servicePrice']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No services currently listed.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="section-container">
        <h2 class="section-title">Top Rated Event Hosters</h2>
        <div class="vendor-grid">
            <?php if ($topVendors && $topVendors->num_rows > 0): ?>
                <?php while ($vendor = $topVendors->fetch_assoc()): ?>
                    <div class="vendor-card">
                        <h3><?= htmlspecialchars($vendor['vendorOrginisationName']) ?></h3>
                        <div class="stars">
                            <?= str_repeat('★', round($vendor['avgRating'])) ?><?= str_repeat('☆', 5 - round($vendor['avgRating'])) ?>
                        </div>
                        <p style="color: #666; font-size: 14px; margin-bottom: 5px;">
                            <?= number_format($vendor['avgRating'], 1) ?>/5.0 (<?= $vendor['reviewCount'] ?> Reviews)
                        </p>
                        <p style="font-size: 14px; color: #333; margin-top: 15px;">
                            <?= htmlspecialchars(substr($vendor['vendorDescription'] ?? 'Top tier professional event vendor dedicated to making your day perfect.', 0, 100)) ?>...
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No verified vendors with ratings found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'global_footer.php'; ?>
    <?php $conn->close(); ?>
</body>
</html>