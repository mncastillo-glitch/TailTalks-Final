<?php
session_start();
include('db.php');
$error_msg = "";

// Social login shortcut
if (isset($_GET['social'])) {
    $_SESSION['username'] = "Guest Member";
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $user_input = trim($_POST['user_input']);
    $password   = $_POST['password'];

    // Prepared statement — safe from SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id']  = $row['id'];

            // Remember Me
            if (isset($_POST['remember'])) {
                setcookie("user_login", $row['username'], time() + (86400 * 30), "/");
            } else {
                setcookie("user_login", "", time() - 3600, "/");
            }

            header("Location: index.php");
            exit();
        } else {
            $error_msg = "Invalid password.";
        }
    } else {
        $error_msg = "User not found.";
    }
}

$html = file_get_contents("login.html");
echo str_replace("{{MESSAGE}}", $error_msg, $html);
?>
