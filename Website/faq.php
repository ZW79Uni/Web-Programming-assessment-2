<?php
// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FAQ Page</title>
<link rel="stylesheet" href="style.css">
<style>
        /* Main Content */
        .container {
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .faq-box {
                   width: 70%;
                   border: 1px solid black;
                   padding: 20px;
                   height: 400px;        /* controls box height */
                   overflow-y: auto;     /* enables vertical scrolling */
       }
        .faq-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .faq-item {
            border: 1px solid black;
            margin-bottom: 20px;
        }

        .question {
            padding: 10px;
            border-bottom: 1px solid black;
        }

        .answer {
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
        <div class="faq-box">
            <h2 class="faq-title">Frequently Asked Questions</h2>

            <div class="faq-item">
                <div class="question">1. Example Question </div>
                <div class="answer">Exmple Answer</div>
            </div>

            <div class="faq-item">
                <div class="question">2.Example Question</div>
                <div class="answer">Example Answer</div>
            </div>
           <div class="faq-item">
    <div class="question">3. Example Question</div>
    <div class="answer">Example Answer</div>
</div>

<div class="faq-item">
    <div class="question">4. Example Question</div>
    <div class="answer">Example Answer</div>
</div>

<div class="faq-item">
    <div class="question">5. Example Question</div>
    <div class="answer">Example Answer</div>
</div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'global_footer.php'; ?>

</body>
</html>
