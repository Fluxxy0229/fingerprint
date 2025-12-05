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
$contact_number = trim($_POST['contact_number'] ?? '');
$birthdate = trim($_POST['birthdate'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$civil_status = trim($_POST['civil_status'] ?? '');

$contact_person = trim($_POST['contact_person'] ?? null);
$contact_person_number = trim($_POST['contact_person_number'] ?? null);
$contact_person_relation = trim($_POST['contact_person_relation'] ?? '');
$senior_biometric_data = isset($_POST['senior_biometric_data']) ? (int) $_POST['senior_biometric_data'] : 0;

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
if ($contact_number === '') {
	$errors[] = 'Contact number is required.';
}
if ($birthdate === '') {
	$errors[] = 'Birth date is required.';
}
if ($gender === '') {
	$errors[] = 'Gender is required.';
}
if ($civil_status === '') {
	$errors[] = 'Civil status is required.';
}
if ($contact_person_relation === '') {
	$errors[] = 'Emergency contact relation is required.';
}

if (!empty($errors)) {
	$msg = urlencode(implode(' ', $errors));
	header("Location: ../../admin_dashboard.php?error={$msg}");
	exit();
}

$checkStmt = $conn->prepare("SELECT id FROM senior_list WHERE full_name = ? LIMIT 1");
$checkStmt->bind_param("s", $full_name);
$checkStmt->execute();
$checkStmt->store_result();
if ($checkStmt->num_rows > 0) {
	$errors[] = 'A senior with this full name already exists.';
}
$checkStmt->close();

if (!empty($errors)) {
	$msg = urlencode(implode(' ', $errors));
	header("Location: ../../admin_dashboard.php?error={$msg}");
	exit();
}

$columns = [
	'full_name',
	'age',
	'address',
	'contact_number',
	'birthdate',
	'gender',
	'civil_status',
	'contact_person',
	'contact_person_number',
	'contact_person_relation',
	'senior_biometric_data'
];

$values = [
	$full_name,
	$age,
	$address,
	$contact_number,
	$birthdate === '' ? null : $birthdate,
	$gender,
	$civil_status,
	$contact_person === '' ? null : $contact_person,
	$contact_person_number === '' ? null : $contact_person_number,
	$contact_person_relation,
	$senior_biometric_data
];

$placeholders = implode(', ', array_fill(0, count($columns), '?'));
$sql = "INSERT INTO senior_list (" . implode(', ', $columns) . ") VALUES (" . $placeholders . ")";

if ($stmt = $conn->prepare($sql)) {
	$types = '';
	foreach ($columns as $col) {
		if ($col === 'age' || $col === 'senior_biometric_data') {
			$types .= 'i';
		} else {
			$types .= 's';
		}
	}

	$bindParams = [&$types];
	for ($i = 0; $i < count($values); $i++) {
		$bindParams[] = &$values[$i];
	}

	call_user_func_array([$stmt, 'bind_param'], $bindParams);
	
	if ($stmt->execute()) {
		$newSeniorId = $conn->insert_id;
		$stmt->close();

		if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) {
			$logUserId = $_SESSION['user_id'] ?? null;
			$logUsername = $_SESSION['username'] ?? null;
			$logAction = 'add_senior';
			$logDetails = 'senior citizen: ' . $full_name;
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
		$err = urlencode('Insert failed: ' . $stmt->error);
		$stmt->close();
		$conn->close();
		header("Location: ../../admin_dashboard.php?error={$err}");
		exit();
	}
} else {
	$err = urlencode('Prepare failed: ' . $conn->error);
	$conn->close();
	header("Location: ../../admin_dashboard.php?error={$err}");
	exit();
}
?>