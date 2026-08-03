<?php
// pray-for.php
// Called when someone clicks "I Prayed" on the public Prayer Wall. Increments the counter.

require_once 'db-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false]);
    exit;
}

$conn = getDbConnection();

$stmt = $conn->prepare("UPDATE prayers SET prayer_count = prayer_count + 1 WHERE id = ? AND is_approved = 1");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    // fetch the new count to send back
    $result = $conn->query("SELECT prayer_count FROM prayers WHERE id = " . (int)$id);
    $row = $result ? $result->fetch_assoc() : null;
    echo json_encode(['success' => true, 'prayer_count' => $row ? (int)$row['prayer_count'] : null]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
