<?php
session_start();
include('db.php');
 
// 1. Handle Cookies for auto-login
if (!isset($_SESSION['username']) && isset($_COOKIE['user_login'])) {
    $_SESSION['username'] = $_COOKIE['user_login'];
    
    // Also restore user_id from cookie so My Inquiries link shows
    $cookie_user = mysqli_real_escape_string($conn, $_COOKIE['user_login']);
    $user_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM users WHERE username='$cookie_user' LIMIT 1"));
    if ($user_row) {
        $_SESSION['user_id'] = $user_row['id'];
    }
}
 
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest Member";
 
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
 
// 2. Optimized Database Queries
if (!empty($search)) {
    $search_query = "SELECT * FROM breeds WHERE breed_name LIKE '%$search%' OR animal_type LIKE '%$search%'";
    $search_results = mysqli_query($conn, $search_query);
} else {
    $dogs     = mysqli_query($conn, "SELECT * FROM breeds WHERE animal_type = 'Dog'");
    $cats     = mysqli_query($conn, "SELECT * FROM breeds WHERE animal_type = 'Cat'");
    $birds    = mysqli_query($conn, "SELECT * FROM breeds WHERE animal_type = 'Bird'");
    $hamsters = mysqli_query($conn, "SELECT * FROM breeds WHERE animal_type = 'Hamster'");
}
 
// 3. Load the Design
include('index-design.php');
?>
 
