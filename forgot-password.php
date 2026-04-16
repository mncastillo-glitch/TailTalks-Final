<?php
session_start();
include('db.php');
$message = "";

if (isset($_POST['reset_request'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists in your 'users' table
    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // In a real app, you'd send an email. 
        // For your project, we will "simulate" it by saving the email in a session 
        // and going straight to the reset page.
        $_SESSION['reset_email'] = $email;
        header("Location: reset-now.php"); 
        exit();
    } else {
        $message = "No account found with that email address.";
    }
}

// Load HTML and swap placeholder
$template = file_get_contents("forgot-password.html");
echo str_replace("{{MESSAGE}}", $message, $template);
?>