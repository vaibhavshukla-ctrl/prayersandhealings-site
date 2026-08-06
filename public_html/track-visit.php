<?php
// track-visit.php
// Called quietly from every page to log a visit. Doesn't slow down page loading
// since the browser calls it in the background after the page has already shown.

require_once 'db-config.php';

header('Content-Type: application/json');

$page = isset($_POST['page']) ? substr(trim($_POST['page']), 0, 255) : 'unknown';

$conn = getDbConnection();
$stmt = $conn->prepare("INSERT INTO site_visits (page) VALUES (?)");
$stmt->bind_param('s', $page);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => true]);
