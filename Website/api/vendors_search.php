<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "h6zp02h_WebAccess";
$password = "SparrowHawk26!";
$dbname = "h6zp02h_EMW_Database";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$eventType = $_GET['eventType'] ?? '';
$location = $_GET['location'] ?? '';
$maxPrice = isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '' ? floatval($_GET['maxPrice']) : 999999;
$sort = $_GET['sort'] ?? ''; 

$sql = "SELECT 
            v.vendorID, v.vendorOrginisationName, v.isVerified, v.vendorDescription, v.automationScore,
            a.city, a.county,
            s.serviceID, s.serviceName, s.servicePrice, s.isAvailable,
            (SELECT COALESCE(ROUND(AVG(rating), 1), 0) FROM review r WHERE r.vendorID = v.vendorID) as avgRating
        FROM `vendor` v
        JOIN `address` a ON v.addressID = a.addressID
        JOIN `vendorServiceAllocation` vsa ON v.vendorID = vsa.vendorID
        JOIN `service` s ON vsa.serviceID = s.serviceID
        LEFT JOIN `eventTypeAllocation` eta ON s.serviceID = eta.serviceID
        LEFT JOIN `eventType` et ON eta.eventTypeID = et.eventTypeID
        WHERE s.isAvailable = 1 AND s.servicePrice <= ?";

$params = [$maxPrice];
$types = "d";

if (!empty($eventType)) {
    $sql .= " AND et.eventTypeName = ?";
    $params[] = $eventType;
    $types .= "s";
}

if (!empty($location)) {
    // ENHANCED SEARCH: Now scans serviceName as well!
    $sql .= " AND (a.city LIKE ? OR a.county LIKE ? OR v.vendorOrginisationName LIKE ? OR s.serviceName LIKE ?)";
    $searchTerm = "%" . $location . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm; 
    $types .= "ssss";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$vendors = [];
while ($row = $result->fetch_assoc()) {
    $vid = $row['vendorID'];
    if (!isset($vendors[$vid])) {
        $vendors[$vid] = [
            "vendorID" => $vid,
            "vendorOrginisationName" => $row['vendorOrginisationName'],
            "vendorDescription" => $row['vendorDescription'] ?? 'Professional event services provider.',
            "city" => $row['city'],
            "county" => $row['county'],
            "isVerified" => (bool)$row['isVerified'],
            "avgRating" => (float)$row['avgRating'],
            "automationScore" => (int)$row['automationScore'],
            "services" => []
        ];
    }
    
    $serviceExists = array_filter($vendors[$vid]["services"], fn($s) => $s['serviceID'] == $row['serviceID']);
    if (empty($serviceExists)) {
        $vendors[$vid]["services"][] = [
            "serviceID" => $row['serviceID'],
            "serviceName" => $row['serviceName'],
            "servicePrice" => (float)$row['servicePrice']
        ];
    }
}

$vendors = array_values($vendors);
foreach ($vendors as &$vendor) {
    $prices = array_column($vendor['services'], 'servicePrice');
    $vendor['basePrice'] = !empty($prices) ? min($prices) : 0;
}

if ($sort === 'price_asc') {
    usort($vendors, fn($a, $b) => $a['basePrice'] <=> $b['basePrice']);
} elseif ($sort === 'price_desc') {
    usort($vendors, fn($a, $b) => $b['basePrice'] <=> $a['basePrice']);
} elseif ($sort === 'rating') {
    usort($vendors, fn($a, $b) => $b['avgRating'] <=> $a['avgRating']);
}

echo json_encode(["data" => $vendors]);
$stmt->close();
$conn->close();
?>