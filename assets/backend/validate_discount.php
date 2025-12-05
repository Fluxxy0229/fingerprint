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

$senior_name = isset($_POST['senior_name']) ? trim($_POST['senior_name']) : '';
$guardian_name = isset($_POST['guardian_name']) ? trim($_POST['guardian_name']) : '';
$verification_type = isset($_POST['verification_type']) ? trim($_POST['verification_type']) : '';
$discount_amount = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0;

if (empty($senior_name)) {
    echo json_encode(['success' => false, 'message' => 'Senior name is required']);
    exit;
}

if ($discount_amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid discount amount']);
    exit;
}

$weekly_cap = 500.00;
$current_week_total = 0.00;

$stmt = $conn->prepare("
    SELECT SUM(discount_amount) as weekly_total
    FROM applied_discounts
    WHERE senior_name = ?
    AND YEARWEEK(applied_at, 1) = YEARWEEK(CURDATE(), 1)
");
$stmt->bind_param("s", $senior_name);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $row = $result->fetch_assoc()) {
    $current_week_total = $row['weekly_total'] ?? 0.00;
}
$stmt->close();

if (($current_week_total + $discount_amount) > $weekly_cap) {
    $remaining = $weekly_cap - $current_week_total;
    echo json_encode([
        'success' => false,
        'message' => "Weekly discount cap exceeded. Senior can only receive PHP " . number_format($remaining, 2) . " more this week."
    ]);
    exit;
}

if ($verification_type === 'Fingerprint') {
    $stmt = $conn->prepare("SELECT id, full_name FROM senior_list WHERE full_name = ?");
    $stmt->bind_param("s", $senior_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'message' => 'Senior verified', 'senior_id' => $row['id']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Senior Citizen not found in database']);
    }

    $stmt->close();
} elseif ($verification_type === 'Guardian') {
    if (empty($guardian_name)) {
        echo json_encode(['success' => false, 'message' => 'Guardian name is required']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, full_name, contact_person FROM senior_list WHERE full_name = ? AND contact_person = ?");
    $stmt->bind_param("ss", $senior_name, $guardian_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'message' => 'Senior and guardian verified', 'senior_id' => $row['id']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Senior Citizen and Contact Person combination not found in database']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid verification type']);
}

$conn->close();
