<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fingerprint_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("UPDATE user_activities SET details = REPLACE(details, 'Applied discount', 'a discount') WHERE details LIKE 'Applied discount%'");
$conn->query("UPDATE user_activities SET action = 'applied' WHERE action = 'applied discount'");

echo "Logs updated successfully.";
$conn->close();
?>
