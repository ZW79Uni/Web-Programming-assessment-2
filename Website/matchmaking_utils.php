<?php
/**
 * Vendor Discovery Hub Matchmaking Utilities
 * 
 * Target: "Deliveroo-style" matchmaking for Becky
 * Focus: Vendor Discovery based purely on basic availability or tags (simulation)
 * No interference with chosen Events sorting/filtering logic (Nathan/Toby's logic).
 */

/**
 * Returns the automation score for a specific vendor from the database.
 * 
 * TODO: Real-world calculation logic
 * While currently stored as a static value in the sample data, the intended 
 * "real-world" calculation logic involves:
 * 1. Booking Success Rate: Tracking the ratio between "confirmed" and "cancelled" 
 *    bookings in the booking table.
 * 2. Trust Metrics: Aggregating average star ratings from the review table.
 * 3. Operational Performance: Considering a vendor's platform longevity and response times.
 * 
 * For this demo phase, it relies strictly on the database static integer.
 * 
 * @param mysqli $conn The database connection.
 * @param int $vendorID The Vendor ID to query.
 * @return int The calculated automation score (currently static).
 */
function getAutomationScore($conn, $vendorID) {
    $vendorID = intval($vendorID);
    $query = "SELECT `automationScore` FROM `vendor` WHERE `vendorID` = $vendorID";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return (int) $row['automationScore'];
    }
    
    return 0; // Default fallback score
}

/**
 * Gets a sorted list of vendors for the Discovery Hub.
 * Represents the 'Deliveroo-style' feed of all vendors ranked by the Automation Score and Verification.
 * 
 * @param mysqli $conn The database connection.
 * @return array Array of associative arrays representing vendors.
 */
function getDiscoveryHubVendors($conn) {
    $query = "
        SELECT `vendorID`, `firstName`, `lastName`, `vendorOrginisationName`, `isVerified`, `automationScore`
        FROM `vendor`
        ORDER BY `isVerified` DESC, `automationScore` DESC, `vendorID` ASC
        LIMIT 20
    ";
    
    $result = $conn->query($query);
    $vendors = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $vendors[] = $row;
        }
    }
    
    return $vendors;
}
?>