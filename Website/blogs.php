<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs - Under Construction</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .construction-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
        }
        .construction-container h1 {
            font-size: 48px;
            color: #E15050;
        }
        .construction-container p {
            font-size: 20px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include "global_header.php"; ?>

    <div class="container construction-container">
        <h1>🚧 Under Construction 🚧</h1>
        <p>Our Blogs section is currently being built. Check back soon!</p>
    </div>

    <?php include "global_footer.php"; ?>
</body>
</html>