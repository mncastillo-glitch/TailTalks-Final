<?php
session_start();
include('db.php');

// Keep the user logged in if they have a cookie
if (!isset($_SESSION['username']) && isset($_COOKIE['user_login'])) {
    $_SESSION['username'] = $_COOKIE['user_login'];
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest Member";

include('about-design.php');
?>