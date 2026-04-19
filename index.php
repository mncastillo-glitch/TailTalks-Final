<?php
session_start();
include('db.php');

// 1. Handle Cookies for auto-login
if (!isset($_SESSION['username']) && isset($_COOKIE['user_login'])) {
    $_SESSION['username'] = $_COOKIE['user_login'];

    // Restore user_id from cookie
    $cookie_user = mysqli_real_escape_string($conn, $_COOKIE['user_login']);
    $user_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, email FROM users WHERE username='$cookie_user' LIMIT 1"));
    if ($user_row) {
        $_SESSION['user_id']    = $user_row['id'];
        $_SESSION['user_email'] = $user_row['email'];
    }
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest Member";

// 2. Get pending inquiry count for notification badge
$pending_count = 0;
if (isset($_SESSION['user_email'])) {
    $pc = $conn->prepare("SELECT COUNT(*) as cnt FROM inquiries WHERE email = ? AND status = 'New'");
    $pc->bind_param("s", $_SESSION['user_email']);
    $pc->execute();
    $pending_count = $pc->get_result()->fetch_assoc()['cnt'] ?? 0;
}

// 3. Search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// 4. Database Queries
if (!empty($search)) {
    $search_query   = "SELECT * FROM breeds WHERE breed_name LIKE '%$search%' OR animal_type LIKE '%$search%'";
    $search_results = mysqli_query($conn, $search_query);
} else {
    $dogs     = mysqli_query($conn, "SELECT * FROM breeds WHERE animal_type = 'Dog'");
    $cats     = mysqli_query($conn, "SELECT * FROM breeds WHERE animal_type = 'Cat'");
    $birds    = mysqli_query($conn, "SELECT * FROM breeds WHERE animal_type = 'Bird'");
    $hamsters = mysqli_query($conn, "SELECT * FROM breeds WHERE animal_type = 'Hamster'");
}

// 5. Load Design
include('index-design.php');
?>
