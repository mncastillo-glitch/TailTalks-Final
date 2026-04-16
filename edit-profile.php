<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username'];
$message = "";

// 1. Fetch current data
$query = "SELECT * FROM users WHERE username = '$user'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);

// Fallback for new columns
if (!isset($userData['favorite_category'])) {
    $userData['favorite_category'] = "Dogs & Puppies";
}

// 2. Handle Update
$user = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_fav = mysqli_real_escape_string($conn, $_POST['favorite_category']);
    $new_bio = mysqli_real_escape_string($conn, $_POST['bio']); // New line

    $update_query = "UPDATE users SET 
                     email = '$new_email', 
                     favorite_category = '$new_fav', 
                     bio = '$new_bio' 
                     WHERE username = '$user'";
    
    if (mysqli_query($conn, $update_query)) {
        $message = "Success! Profile updated.";
        $userData['email'] = $new_email;
        $userData['favorite_category'] = $new_fav;
        $userData['bio'] = $new_bio; // Update local variable
    }
}
include('edit-profile-design.php');
?>