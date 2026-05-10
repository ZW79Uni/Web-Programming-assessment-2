<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Main Content */
        .container {
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .about-box {
            width: 70%;
            border: 1px solid black;
            padding: 20px;
        }

        .section {
            border: 1px solid black;
            margin-bottom: 20px;
        }

        .section-title {
            padding: 10px;
            border-bottom: 1px solid black;
            text-align: center;
            font-size: 20px;
        }

        .section-content {
            padding: 20px;
        }

        /* Footer */
        .footer {
            border-top: 1px solid black;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <?php include 'global_header.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <div class="about-box">

            <div class="section">
                <div class="section-title">About Us</div>
                <div class="section-content">
                    Add your About Us text here.
                </div>
            </div>

            <div class="section">
                <div class="section-title">Our Values</div>
                <div class="section-content">
                    Add your Our Values text here.
                </div>
            </div>

        </div>
    </div>

    <!-- Footer -->
    <?php include 'global_footer.php'; ?>

</body>
</html>