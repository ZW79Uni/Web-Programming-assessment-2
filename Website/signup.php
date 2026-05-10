<?php
session_start();

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";

$conn = new mysqli($servername, $username, $password, $dbname);

$errorMsg = "";
$successMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'client';
    
    // Core User Details
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $user = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phoneNumber'] ?? '');
    $pass = $_POST['password'] ?? '';
    
    // Address Details
    $houseNum = trim($_POST['houseNumber'] ?? '');
    $street = trim($_POST['streetName'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $county = trim($_POST['county'] ?? '');
    $postcode = trim($_POST['postcode'] ?? '');
    
    // Vendor Specific Details
    $orgName = trim($_POST['vendorOrginisationName'] ?? '');
    $orgRole = trim($_POST['vendorOrginisationRole'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($user) || empty($email) || empty($pass) || empty($city) || empty($postcode)) {
        $errorMsg = "Please fill in all required fields.";
    } else {
        // Hash password securely (Matches $2y$10$ hashes in sample data)
        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

        // Begin transaction to ensure both address and user are created together
        $conn->begin_transaction();
        
        try {
            // 1. Insert Address Data
            $stmtAddr = $conn->prepare("INSERT INTO `address` (`houseNumber`, `streetName`, `city`, `county`, `postcode`) VALUES (?, ?, ?, ?, ?)");
            $stmtAddr->bind_param("sssss", $houseNum, $street, $city, $county, $postcode);
            $stmtAddr->execute();
            $addressID = $stmtAddr->insert_id;
            $stmtAddr->close();

            // 2. Insert Role Data
            if ($role === 'vendor') {
                $stmtVen = $conn->prepare("INSERT INTO `vendor` (`addressID`, `firstName`, `lastName`, `username`, `password`, `email`, `phoneNumber`, `vendorOrginisationRole`, `vendorOrginisationName`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtVen->bind_param("issssssss", $addressID, $firstName, $lastName, $user, $hashedPassword, $email, $phone, $orgRole, $orgName);
                $stmtVen->execute();
                $stmtVen->close();
            } else {
                $stmtCli = $conn->prepare("INSERT INTO `client` (`addressID`, `firstName`, `lastName`, `username`, `password`, `email`, `phoneNumber`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmtCli->bind_param("issssss", $addressID, $firstName, $lastName, $user, $hashedPassword, $email, $phone);
                $stmtCli->execute();
                $stmtCli->close();
            }
            
            $conn->commit();
            $successMsg = "Account created successfully! You can now <a href='login.php' style='color:#00ADEF;'>Log In</a>.";
            
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            if ($e->getCode() == 1062) {
                $errorMsg = "Registration failed: That Username or Email is already in use.";
            } else {
                $errorMsg = "A database error occurred. Please try again.";
            }
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - EMW</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .signup-container {
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            min-height: 70vh;
        }
        .signup-box {
            width: 100%;
            max-width: 800px;
            border: 3px solid #E15050;
            background-color: #ffffff;
        }
        .login-tabs {
            display: flex;
            border-bottom: 3px solid #E15050;
        }
        .login-tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-right: 3px solid #E15050;
            font-size: 20px;
            cursor: pointer;
            color: #E15050;
            transition: all 0.2s;
        }
        .login-tab:last-child { border-right: none; }
        .login-tab:hover { background-color: #fcebeb; }
        .login-tab.active { color: #E15050; font-weight: bold; background-color: #fcebeb; }
        
        .signup-content { padding: 30px 40px; }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .full-width { grid-column: 1 / -1; }
        
        .input-group label {
            display: block;
            color: #E15050;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .input-box {
            width: 100%;
            padding: 12px;
            border: 2px solid #E15050;
            box-sizing: border-box;
            color: #E15050;
            font-size: 16px;
        }
        
        .section-title {
            color: #E15050;
            border-bottom: 2px solid #E15050;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .action-btn {
            padding: 12px 30px;
            border: 3px solid #E15050;
            background: #E15050;
            cursor: pointer;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            width: 100%;
            margin-top: 20px;
            transition: all 0.2s;
        }
        .action-btn:hover { background: #ffffff; color: #E15050; }
        
        .alert { padding: 15px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 2px solid; }
        .alert-error { border-color: #d9534f; color: #d9534f; background-color: #fdf7f7; }
        .alert-success { border-color: #5cb85c; color: #5cb85c; background-color: #f4fdf4; }
    </style>
</head>
<body>

    <?php include 'global_header.php'; ?>

    <div class="signup-container">
        <div class="signup-box">
            <div class="login-tabs">
                <div class="login-tab active" data-role="client">Register as Customer</div>
                <div class="login-tab" data-role="vendor">Register as Vendor</div>
            </div>

            <form class="signup-content" method="POST" action="signup.php">
                
                <?php if ($errorMsg): ?><div class="alert alert-error"><?= $errorMsg ?></div><?php endif; ?>
                <?php if ($successMsg): ?><div class="alert alert-success"><?= $successMsg ?></div><?php endif; ?>

                <input type="hidden" name="role" id="roleInput" value="client">

                <div class="section-title">Personal Details</div>
                <div class="form-grid">
                    <div class="input-group">
                        <label>First Name *</label>
                        <input type="text" name="firstName" class="input-box" required>
                    </div>
                    <div class="input-group">
                        <label>Last Name *</label>
                        <input type="text" name="lastName" class="input-box" required>
                    </div>
                    <div class="input-group">
                        <label>Username *</label>
                        <input type="text" name="username" class="input-box" required>
                    </div>
                    <div class="input-group">
                        <label>Password *</label>
                        <input type="password" name="password" class="input-box" required>
                    </div>
                    <div class="input-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" class="input-box" required>
                    </div>
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phoneNumber" class="input-box">
                    </div>
                </div>

                <div id="vendorSection" style="display: none;">
                    <div class="section-title">Business Information</div>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Organization Name *</label>
                            <input type="text" name="vendorOrginisationName" id="orgNameInput" class="input-box">
                        </div>
                        <div class="input-group">
                            <label>Your Role (e.g., Owner, Manager) *</label>
                            <input type="text" name="vendorOrginisationRole" id="orgRoleInput" class="input-box">
                        </div>
                    </div>
                </div>

                <div class="section-title">Physical Address</div>
                <div class="form-grid">
                    <div class="input-group">
                        <label>House Number / Name *</label>
                        <input type="text" name="houseNumber" class="input-box" required>
                    </div>
                    <div class="input-group">
                        <label>Street Name *</label>
                        <input type="text" name="streetName" class="input-box" required>
                    </div>
                    <div class="input-group">
                        <label>City *</label>
                        <input type="text" name="city" class="input-box" required>
                    </div>
                    <div class="input-group">
                        <label>County *</label>
                        <input type="text" name="county" class="input-box" required>
                    </div>
                    <div class="input-group full-width">
                        <label>Postcode *</label>
                        <input type="text" name="postcode" class="input-box" required style="width: 50%;">
                    </div>
                </div>

                <button type="submit" class="action-btn">Create Account</button>
                <div style="text-align: center; margin-top: 15px; color: #E15050; font-weight: bold;">
                    Already have an account? <a href="login.php" style="color: #00ADEF;">Log In Here</a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'global_footer.php'; ?>

    <script>
        const tabs = document.querySelectorAll('.login-tab');
        const roleInput = document.getElementById('roleInput');
        const vendorSection = document.getElementById('vendorSection');
        const orgNameInput = document.getElementById('orgNameInput');
        const orgRoleInput = document.getElementById('orgRoleInput');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const selectedRole = tab.getAttribute('data-role');
                roleInput.value = selectedRole;
                
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // Toggle required fields for Vendor data
                if (selectedRole === 'vendor') {
                    vendorSection.style.display = 'block';
                    orgNameInput.required = true;
                    orgRoleInput.required = true;
                } else {
                    vendorSection.style.display = 'none';
                    orgNameInput.required = false;
                    orgRoleInput.required = false;
                }
            });
        });
    </script>
</body>
</html>