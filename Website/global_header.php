<?php
// Ensure session is started if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="header">
    <h1>Events Meets Worlds (EMW)</h1>
    <?php if (isset($vendorData['firstName'])): ?>
        <h3>Welcome, <?php echo htmlspecialchars($vendorData['firstName']); ?> (<?php echo htmlspecialchars($vendorData['vendorOrginisationName'] ?? 'No Org'); ?>)</h3>
    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <h3>System Moderation & Verifications</h3>
    <?php endif; ?>
</div>
<div class="nav">
    <a href="about.html">About Us</a> 
    <!-- Assuming Search Events is HTMLPage1.php or just placeholder Event.php -->
    <a href="HTMLPage1.php">Events</a> 
    <a href="faq.html">FAQ</a> 

    <?php if (isset($_SESSION['role'])): ?>
        <!-- Role Specific Dashboards -->
        <?php if ($_SESSION['role'] === 'client'): ?>
            <a href="#">Client Dashboard</a>
        <?php elseif ($_SESSION['role'] === 'vendor'): ?>
            <a href="vendor_profile.php">Vendor Dashboard</a>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
            <a href="admin_dash.php">Admin Dashboard</a>
        <?php endif; ?>
        
        <!-- Shared Logged-in Links -->
        <a href="chatPicker.HTML">Chat</a>
        <a href="login.php">Logout</a> <!-- In a real app this would go to a logout.php to destroy session -->
    <?php else: ?>
        <a href="login.php">Log In</a>
    <?php endif; ?>
</div>