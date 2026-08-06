<?php
// site-visits-count.php
// Returns the total number of logged visits as JSON, used by site.js to show a live counter.

require_once 'db-config.php';

header('Content-Type: application/json');

$conn = getDbConnection();
$result = $conn->query("SELECT COUNT(*) as total FROM site_visits");
$row = $result->fetch_assoc();

echo json_encode(['success' => true, 'total' => (int) $row['total']]);

$conn->close();
