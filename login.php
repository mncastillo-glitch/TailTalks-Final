<?php
session_start();
include('db.php');
$error_msg = "";

// Instant Login for Social Buttons
if (isset($_GET['social'])) {
    $_SESSION['username'] = "Guest Member";
    header("Location: index.php"); 
    exit();
}

if (isset($_POST['login'])) {
    $user_input = mysqli_real_escape_string($conn, $_POST['user_input']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$user_input' OR email='$user_input' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id'] = $row['id'];

            // --- REMEMBER ME LOGIC ---
            if (isset($_POST['remember'])) {
                // Store the username in a cookie for 30 days
                setcookie("user_login", $row['username'], time() + (86400 * 30), "/"); 
            } else {
                // If not checked, delete any existing cookie
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