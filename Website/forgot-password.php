<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - EMW</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            padding: 60px 20px;
            display: flex;
            justify-content: center;
            min-height: 50vh;
        }
        .login-box {
            width: 70%;
            max-width: 600px;
            border: 3px solid #E15050;
            background-color: #ffffff;
        }
        .login-tabs {
            display: flex;
            border-bottom: 3px solid #E15050;
            background-color: #fcebeb;
        }
        .login-tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            font-size: 20px;
            color: #E15050;
            font-weight: bold;
        }
        .login-content {
            padding: 40px 20px;
            text-align: center;
        }
        .input-box {
            width: 80%;
            padding: 12px;
            margin: 15px 0;
            border: 3px solid #E15050;
            box-sizing: border-box;
            color: #E15050;
            text-align: center;
            font-size: 18px;
        }
        .input-box::placeholder {
            color: #f29b9b;
        }
        .small-text {
            margin: 15px 0;
            color: #E15050;
            font-size: 16px;
        }
        .small-text a {
            color: #00ADEF;
            text-decoration: underline;
        }
        .sign-in-btn {
            padding: 10px 30px;
            border: 3px solid #E15050;
            background: none;
            cursor: pointer;
            color: #E15050;
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 15px;
            transition: all 0.2s ease;
        }
        .sign-in-btn:hover {
            background: #E15050;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <?php include 'global_header.php'; ?>

    <div class="container">
        <div class="login-box">
            <div class="login-tabs">
                <div class="login-tab">Password Recovery</div>
            </div>
            <form class="login-content" action="#" method="POST">
                <input type="email" name="email" class="input-box" placeholder="Enter your Email" required>
                <div class="small-text">
                    An email will be sent containing a link to reset your password.
                </div>
                <button type="button" class="sign-in-btn" onclick="alert('Recovery system under construction')">Send Recovery Code</button>
                <div class="small-text">
                    Remember your password? <a href="login.php">Back to Login</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'global_footer.php'; ?>

</body>
</html>