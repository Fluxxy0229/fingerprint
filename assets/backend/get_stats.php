<?php
session_start();

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fingerprint_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$total_senior = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM senior_list");
if ($result && $row = $result->fetch_assoc()) {
    $total_senior = $row['count'] ?? 0;
}

$today_discount = 0.00;
$result = $conn->query("SELECT SUM(discount_amount) as total FROM applied_discounts WHERE DATE(applied_at) = CURDATE()");
if ($result && $row = $result->fetch_assoc()) {
    $today_discount = $row['total'] ?? 0.00;
}

$total_discount = 0.00;
$result = $conn->query("SELECT SUM(discount_amount) as total FROM applied_discounts");
if ($result && $row = $result->fetch_assoc()) {
    $total_discount = $row['total'] ?? 0.00;
}

$total_transactions = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM applied_discounts");
if ($result && $row = $result->fetch_assoc()) {
    $total_transactions = $row['count'] ?? 0;
}

echo json_encode([
    'success' => true,
    'stats' => [
        'total_senior' => $total_senior,
        'today_discount' => number_format($today_discount, 2),
        'total_discount' => number_format($total_discount, 2),
        'total_transactions' => $total_transactions
    ]
]);

$conn->close();
?>
