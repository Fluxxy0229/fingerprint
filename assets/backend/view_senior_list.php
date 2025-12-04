<?php
session_start();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['error' => 'unauthorized']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fingerprint_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT id, full_name AS fullname, age, address FROM senior_list";
if (!empty($search)) {
    $sql .= " WHERE full_name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
$seniors = [];
if ($result === false) {
    if (isset($_GET['debug']) && $_GET['debug']) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'sql_error', 'message' => $conn->error, 'sql' => $sql]);
        $conn->close();
        exit();
    }
} else {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $seniors[] = $row;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($seniors);

$conn->close();
?>
