<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../../admin_dashboard.php');
	exit();
}

if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
	header('Location: ../../index.html');
	exit();
}

include '../../dbconn.php';

$full_name = trim($_POST['full_name'] ?? '');
$age = isset($_POST['age']) ? (int) $_POST['age'] : null;
$address = trim($_POST['address'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$dob = trim($_POST['birthdate'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$civil_status = trim($_POST['civil_status'] ?? '');

$errors = [];
if ($full_name === '') {
	$errors[] = 'Full name is required.';
}
if ($age === null || $age <= 0) {
	$errors[] = 'Valid age is required.';
}
if ($address === '') {
	$errors[] = 'Address is required.';
}

if (!empty($errors)) {
	$msg = urlencode(implode(' ', $errors));
	header("Location: ../../admin_dashboard.php?error={$msg}");
	exit();
}

$columns = ['full_name', 'age', 'address', 'contact_number', 'gender', 'civil_status'];
$values = [$full_name, $age, $address, $contact, $gender, $civil_status];

$birthdate = trim($_POST['birthdate'] ?? '');

$possibleDobCols = ['birthdate', 'birtdate', 'dob'];
foreach ($possibleDobCols as $col) {
	$res = $conn->query("SHOW COLUMNS FROM senior_list LIKE '" . $conn->real_escape_string($col) . "'");
	if ($res && $res->num_rows > 0) {
		$columns[] = $col;
		$values[] = ($birthdate === '' ? null : $birthdate);
		break;
	}
}

$placeholders = implode(', ', array_fill(0, count($columns), '?'));
$sql = "INSERT INTO senior_list (" . implode(', ', $columns) . ") VALUES (" . $placeholders . ")";
if ($stmt = $conn->prepare($sql)) {
	$types = '';
	foreach ($columns as $col) {
		$types .= ($col === 'age') ? 'i' : 's';
	}

	$bindParams[] = & $types;
	for ($i = 0; $i < count($values); $i++) {
		$bindParams[] = & $values[$i];
	}

	call_user_func_array([$stmt, 'bind_param'], $bindParams);
	$ok = $stmt->execute();
	if ($ok) {
		$newSeniorId = $conn->insert_id;
		$stmt->close();

		if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
			$logUserId = $_SESSION['user_id'] ?? null;
			$logUsername = $_SESSION['username'] ?? null;
			$logAction = 'add_senior';
			$logDetails = $full_name;
			$logStmt = $conn->prepare('INSERT INTO user_activities (user_id, username, action, details) VALUES (?, ?, ?, ?)');
			if ($logStmt) {
				$logStmt->bind_param('isss', $logUserId, $logUsername, $logAction, $logDetails);
				$logStmt->execute();
				$logStmt->close();
			}
		}

		$conn->close();
		header('Location: ../../admin_dashboard.php?added=1');
		exit();
	} else {
		$err = urlencode('Insert failed');
		$stmt->close();
		$conn->close();
		header("Location: ../../admin_dashboard.php?error={$err}");
		exit();
	}
} else {
	$err = urlencode('Prepare failed');
	if (isset($_GET['debug']) && $_GET['debug']) {
		header('Content-Type: application/json');
		echo json_encode(['error' => 'prepare_failed', 'message' => $conn->error, 'sql' => $sql]);
		exit();
	}
	header("Location: ../../admin_dashboard.php?error={$err}");
	exit();
}

?>

