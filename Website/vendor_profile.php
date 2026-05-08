<!DOCTYPE html>
<html>
<head>
    <title>Vendor Profile - EMW</title>
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
            gap: 20px;
            margin-bottom: 20px;
        }
        .sidebar {
            width: 30%;
            border: 5px solid black;
            box-sizing: border-box;
            padding: 10px;
            min-height: 400px;
        }
        .main-panel {
            width: 70%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .panel {
            border: 5px solid black;
            box-sizing: border-box;
            padding: 10px;
            min-height: 190px;
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
            <span>About</span> <span>Event</span> <span>FAQ</span> <span>Vendor Dashboard</span>
        </div>
        
        <div class="content">
            <div class="sidebar">
                <h2>New Service</h2>
                <form>
                    <p><label>Name:</label><br> <input type="text" style="width:90%"></p>
                    <p><label>Price (£):</label><br> <input type="text" style="width:90%"></p>
                    <p><label>Description:</label><br> <textarea style="width:90%; height:80px;"></textarea></p>
                    <button type="button">Submit Service</button>
                </form>
            </div>
            
            <div class="main-panel">
                <div class="panel">
                    <h2>Existing Services & Bookings</h2>
                    <p>Placeholder for services list and active bookings.</p>
                </div>
                <div class="panel">
                    <h2>Blogs & Chat</h2>
                    <button>Post Blog</button>
                    <button>Chat with Clients</button>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Copyright / Mailing Block</p>
        </div>
    </div>
</body>
</html>