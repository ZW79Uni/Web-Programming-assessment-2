<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Redirect already logged-in users to dashboards
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'vendor' && isset($_SESSION['vendorID'])) {
        header("Location: vendor_profile.php");
        exit;
    } elseif ($_SESSION['role'] === 'admin' && isset($_SESSION['adminID'])) {
        header("Location: admin_dash.php");
        exit;
    } elseif ($_SESSION['role'] === 'client' && isset($_SESSION['clientID'])) {
        header("Location: clientDashBoard.php");
        exit;
    }
}

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $passwordInput = $_POST['password'] ?? '';

    if ($role && $email && $passwordInput) {
        if ($role === 'vendor') {
            $table = 'vendor';
            $idField = 'vendorID';
        } elseif ($role === 'client') {
            $table = 'client';
            $idField = 'clientID';
        } elseif ($role === 'admin') {
            $table = 'admin';
            $idField = 'adminID';
        } else {
            echo "<script>alert('Invalid role selected.');</script>";
            exit;
        }

        $stmt = $conn->prepare("SELECT $idField, password FROM $table WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $dbPassword);
            $stmt->fetch();
            if ($passwordInput === $dbPassword) {
                $_SESSION[$idField] = $id;
                $_SESSION['role'] = $role;
                $_SESSION['loggedin'] = true;
                if ($role === 'client') {
                    $stmt->close();
                    $conn->close();
                    header("Location: clientDashBoard.php");
                    exit;
                }
                if ($role === 'vendor') {
                    $stmt->close();
                    $conn->close();
                    header("Location: vendor_profile.php");
                    exit;
                }
                if ($role === 'admin') {
                    $stmt->close();
                    $conn->close();
                    header("Location: admin_dash.php");
                    exit;
                }
            } else {
                echo "<script>alert('Incorrect password.');</script>";
            }
        } else {
            echo "<script>alert('User not found.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login Page</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid black;
        }

        .title {
            font-size: 24px;
        }

        .login-btn {
            padding: 5px 10px;
            border: 1px solid black;
            text-decoration: none;
            color: black;
            display: inline-block;
        }

        .nav {
            display: flex;
            border-bottom: 1px solid black;
        }

        .nav a {
            flex: 1;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            border-right: 1px solid black;
            color: black;
        }

        .nav a:last-child {
            border-right: none;
        }

        .container {
            padding: 60px 20px;
            display: flex;
            justify-content: center;
        }

        .login-box {
            width: 70%;
            border: 1px solid black;
        }

        .login-tabs {
            display: flex;
            border-bottom: 1px solid black;
        }

        .login-tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-right: 1px solid black;
            font-size: 20px;
            cursor: pointer;
        }

        .login-tab:last-child {
            border-right: none;
        }
        
        .login-tab:hover {
            transform: scale(1.1);
        }
    
        .login-tab:active {
            background-color: black;
            color: white;
        }

        .login-content {
            padding: 30px 20px;
            text-align: center;
        }

        .input-box {
            width: 70%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid black;
            box-sizing: border-box;
        }

        .small-text {
            margin: 8px 0;
        }

        .sign-in-btn {
            padding: 8px 20px;
            border: 1px solid black;
            background: none;
            cursor: pointer;
        }

        .footer {
            border-top: 1px solid black;
            padding: 20px;
            margin-top: 80px;
        }
    </style>
</head>
<body>

    <?php include 'global_header.php'; ?>

    <div class="container">
        <div class="login-box">
            <div class="login-tabs">
                <div class="login-tab" data-role="client">Sign in as Client?</div>
                <div class="login-tab" data-role="vendor">Sign in as Vendor?</div>
                <div class="login-tab" data-role="admin">Sign in as Admin?</div>
            </div>

            <form method="POST" class="login-content">
                <input type="text" name="email" id="email" class="input-box" placeholder="Email or Phone number">
                <br>
                <input type="password" name="password" id="password" class="input-box" placeholder="Password">
                <br>
                <input type="hidden" name="role" id="role" value="client">
                <button type="submit" class="sign-in-btn">Sign in</button>
                <div class="small-text">
                    Can’t remember your password? 
                    <a href="forgot-password.html">Click here</a>
                </div>
            </form>
        </div>
    </div>

<?php include 'global_footer.php'; ?>

<script>
    let selectedRole = 'client';
    const tabs = document.querySelectorAll('.login-tab');
    const roleInput = document.getElementById('role');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            selectedRole = tab.getAttribute('data-role');
            roleInput.value = selectedRole;
            tabs.forEach(t => t.style.backgroundColor = '');
            tabs.forEach(t => t.style.color = '');
            tab.style.backgroundColor = 'black';
            tab.style.color = 'white';
        });
    });
</script>
</body>
</html>