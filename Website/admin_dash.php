<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - EMW</title>
    <!-- Using inline styles heavily for the boxy wireframe look -->
    <style>
        .container {
            width: 1000px;
            margin: 0 auto;
            border: 5px solid black;
            box-sizing: border-box;
            background-color: white;
            padding: 10px;
        }
        .header {
            width: 100%;
            height: 100px;
            border: 5px solid black;
            box-sizing: border-box;
            margin-bottom: 20px;
            text-align: center;
        }
        .nav {
            width: 100%;
            height: 40px;
            border: 5px solid black;
            box-sizing: border-box;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        .content {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 20px;
        }
        .panel {
            border: 5px solid black;
            box-sizing: border-box;
            padding: 10px;
            min-height: 150px;
        }
        .footer {
            width: 100%;
            height: 80px;
            border: 5px solid black;
            box-sizing: border-box;
            text-align: center;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>EMW Logo / Title Block</h1>
        </div>
        <div class="nav">
            <span>About</span> <span>Event</span> <span>FAQ</span> <span>Admin Dashboard</span>
        </div>
        
        <div class="content">
            <div class="panel">
                <h2>User Management (Ban/Suspend)</h2>
                <p>Placeholder for User/Vendor banning interface.</p>
            </div>
            
            <div class="panel">
                <h2>Platform Moderation</h2>
                <p>Placeholder for Blog and Chat monitoring views.</p>
            </div>
            
            <div class="panel">
                <h2>Pending Vendor Verifications</h2>
                <p>Placeholder for unverified vendors list.</p>
            </div>
        </div>

        <div class="footer">
            <p>Copyright / Mailing Block</p>
        </div>
    </div>
</body>
</html>