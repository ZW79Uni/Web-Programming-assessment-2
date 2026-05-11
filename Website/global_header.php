<?php
// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="main-header">
    <div class="header-logo">
        <a href="index.php" style="color: inherit; text-decoration: none;">
            <img src="EMW Logo 2.png"width="200" height="100">
        </a>
    </div>
    
    <div class="header-right">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'vendor'): ?>
            <span class="welcome-text">Vendor Portal</span>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <span class="welcome-text">System Moderation</span>
        <?php endif; ?>

        <?php if (!isset($_SESSION['role'])): ?>
            <a href="login.php"><button class="header-login-btn">Log In</button></a>
        <?php else: ?>
            <a href="login.php?logout=1"><button class="header-login-btn">Log Out</button></a>
        <?php endif; ?>
    </div>
</header>
<div class="navigationHeader">
    <div class="navChild"><a href="about.php">About Us</a></div>
    <div class="navChild"><a href="eventSearchPage.php">Events</a></div>
    <div class="navChild"><a href="faq.php">FAQs</a></div>
    <div class="navChild"><a href="https://www.eventsmeetsworld.co.uk/hire" target="_blank">Equipment Hire</a></div>
    
    <?php if (isset($_SESSION['role'])): ?>
        <?php if ($_SESSION['role'] === 'client'): ?>
            <div class="navChild"><a href="chat.php">Chat</a></div>
            <div class="navChild"><a href="clientDashBoard.php">Dashboard</a></div>
        <?php elseif ($_SESSION['role'] === 'vendor'): ?>
            <div class="navChild"><a href="vendor_profile.php">Dashboard</a></div>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
            <div class="navChild"><a href="admin_dash.php">Admin</a></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="navChild"><a href="blogs.php">Blogs</a></div>
    <?php endif; ?>
</div>