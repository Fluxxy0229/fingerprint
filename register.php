<?php 
$conn = new mysqli("localhost", "root", "", "fingerprint_db");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $checkQuery = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $checkResult = $conn->prepare($checkQuery);

    if($checkResult->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        $insertQuery = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $username, password_hash($password, PASSWORD_BCRYPT), $role);

        if($stmt->execute()) {
            header("Location: register.php");
            exit();
        } else {
            $error = "Error registering user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <form method="post">
        username: <input type="text" name="username" required><br>
        password: <input type="password" name="password" required><br>
        role: 
        <select name="role" required>
            <option value="Admin">Admin</option>
            <option value="Staff">Staff</option>
        </select><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>