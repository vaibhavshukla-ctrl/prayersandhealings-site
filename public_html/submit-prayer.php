<?php
// submit-prayer.php
// Handles the "Request a Prayer" form. Saves the prayer as pending (not public yet).

require_once 'db-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$allowedCategories = ['health', 'family', 'peace', 'work', 'grief', 'relationships', 'strength', 'gratitude'];

$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$intention = isset($_POST['intention_text']) ? trim($_POST['intention_text']) : '';
$email = isset($_POST['contact_email']) ? trim($_POST['contact_email']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$showName = isset($_POST['show_name']) && $_POST['show_name'] === 'on' ? 1 : 0;

if (!in_array($category, $allowedCategories, true)) {
    echo json_encode(['success' => false, 'message' => 'Please choose a valid category.']);
    exit;
}

if (mb_strlen($intention) < 5 || mb_strlen($intention) > 2000) {
    echo json_encode(['success' => false, 'message' => 'Please share a bit more about your prayer intention (5 to 2000 characters).']);
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'That email address doesn\'t look right.']);
    exit;
}

if (mb_strlen($name) > 100) {
    echo json_encode(['success' => false, 'message' => 'That name is too long.']);
    exit;
}

if ($name === '') {
    $showName = 0;
}

$conn = getDbConnection();

$stmt = $conn->prepare(
    "INSERT INTO prayers (category, intention_text, contact_email, name, show_name, is_approved, prayer_count) VALUES (?, ?, ?, ?, ?, 0, 0)"
);
$stmt->bind_param('ssssi', $category, $intention, $email, $name, $showName);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Your prayer request has been received. Thank you for sharing.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again shortly.']);
}

$stmt->close();
$conn->close();
