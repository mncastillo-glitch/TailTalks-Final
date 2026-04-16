<?php
session_start();
include('db.php');

// If not logged in, kick them to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// In profile.php
$user = $_SESSION['username'];
// ALWAYS run the SELECT query every time the page loads to get the freshest data
$query = "SELECT * FROM users WHERE username = '$user'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);

// If user doesn't exist in DB for some reason
if (!$userData) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Load the HTML design
include('profile-design.php');
?>