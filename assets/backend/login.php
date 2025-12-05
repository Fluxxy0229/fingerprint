<?php
session_start();
include '../../dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $msg = 'Please provide both username and password.';
        header('Location: /fingerprint/index.html?error=' . urlencode($msg));
        exit();
    }
    $query = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $stored = $user['password'];

            $authenticated = is_string($stored) && password_verify($password, $stored);

            if ($authenticated) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $role = $user['role'] ?? '';
                if ($role === 'Admin') {
                    header('Location: /fingerprint/admin_dashboard.php');
                } elseif ($role === 'Staff') {
                    header('Location: /fingerprint/staff_dashboard.php');
                } else {
                    header('Location: /fingerprint/admin_dashboard.php');
                }
                exit();
            }

            $msg = 'Invalid username or password.';
            header('Location: /fingerprint/index.html?error=' . urlencode($msg));
            exit();
        } else {
                $msg = 'Invalid username or password.';
                header('Location: /fingerprint/index.html?error=' . urlencode($msg));
            exit();
        }
    } 
}

header('Location: /fingerprint/index.html');
exit();
?>