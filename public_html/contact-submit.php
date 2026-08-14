<?php
// contact-submit.php
// Sends contact form submissions to your email using PHP's built-in mail() function.

header('Content-Type: application/json');

// Change this to the email address you want to receive messages at.
define('CONTACT_RECEIVE_EMAIL', 'support@prayersandhealings.com');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($name === '' || mb_strlen($name) > 200) {
    echo json_encode(['success' => false, 'message' => 'Please enter your name.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}
if (mb_strlen($message) < 5 || mb_strlen($message) > 3000) {
    echo json_encode(['success' => false, 'message' => 'Please write a message between 5 and 3000 characters.']);
    exit;
}

$subject = 'New contact form message from ' . $name;
$body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
$headers = 'From: no-reply@prayersandhealings.com' . "\r\n" .
           'Reply-To: ' . $email . "\r\n";

if (mail(CONTACT_RECEIVE_EMAIL, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Thanks for reaching out. We will get back to you soon.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong sending your message. Please try again shortly.']);
}
