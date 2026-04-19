<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $user  = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $p1    = $_POST['password'];
    $p2    = $_POST['confirm_password'];

    // Check password match
    if ($p1 !== $p2) {
        header("Location: signup.html?error=password_mismatch");
        exit();
    }

    // Check if username or email already exists (prepared statement)
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $user, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: signup.html?error=already_exists");
        exit();
    }

    // Hash password and insert (prepared statement)
    $hashed = password_hash($p1, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user, $email, $phone, $hashed);

    if ($stmt->execute()) {
        header("Location: signup.html?status=success");
        exit();
    } else {
        header("Location: signup.html?error=db_error");
        exit();
    }
}
?>
