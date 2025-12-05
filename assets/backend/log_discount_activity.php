<?php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fingerprint_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$user_id = isset($_SESSION['staff_id']) ? intval($_SESSION['staff_id']) : null;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Staff';

$senior_name = isset($_POST['senior_name']) ? trim($_POST['senior_name']) : '';
$discount_amount = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0.0;
$verification_type = isset($_POST['verification_type']) ? trim($_POST['verification_type']) : '';

if (empty($senior_name) || $discount_amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit;
}

$action = 'applied';
$details = "a discount of PHP " . number_format($discount_amount, 2) . " to " . htmlspecialchars($senior_name) . " via " . htmlspecialchars($verification_type);

$discount_verification_type = ($verification_type === 'Guardian Verification') ? 'Guardian' : $verification_type;

$stmt = $conn->prepare("INSERT INTO user_activities (user_id, username, action, details) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $username, $action, $details);

$success = $stmt->execute();
$stmt->close();

if ($success) {
    $stmt2 = $conn->prepare("INSERT INTO applied_discounts (senior_name, discount_amount, verification_type, staff_id, staff_username) VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("sdsss", $senior_name, $discount_amount, $discount_verification_type, $user_id, $username);

    if ($stmt2->execute()) {
        echo json_encode(['success' => true, 'message' => 'Activity logged successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to log discount']);
    }

    $stmt2->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to log activity']);
}

$conn->close();
?>
