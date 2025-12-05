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

$recentActivities = [];
$sql = "
  SELECT
    ua.id,
    COALESCE(u.role, 'Staff') AS role,
    ua.username,
    COALESCE(ua.details,
      CONCAT(COALESCE(ua.username, 'Staff'), ' ', ua.action)
    ) AS activity,
    ua.created_at AS timestamp
  FROM user_activities ua
  LEFT JOIN users u ON ua.user_id = u.id
  ORDER BY ua.created_at DESC
  LIMIT 5
";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ( $row = $result->fetch_assoc()) {
        $recentActivities[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'logs' => $recentActivities
]);

$conn->close();
?>
