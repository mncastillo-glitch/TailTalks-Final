<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $user  = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $p1    = $_POST['password'];
    $p2    = $_POST['confirm_password'];

    if ($p1 !== $p2) {
        header("Location: signup.html?error=mismatch");
        exit();
    }

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        header("Location: signup.html?error=exists");
        exit();
    }

    $hashed = password_hash($p1, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, phone, password) VALUES ('$user', '$email', '$phone', '$hashed')";

    if (mysqli_query($conn, $sql)) {
        // SUCCESS: Redirect back to HTML with a success flag
        header("Location: signup.html?status=success");
        exit();
    } else {
        header("Location: signup.html?error=db");
        exit();
    }
}
?>