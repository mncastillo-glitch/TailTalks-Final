<?php
session_start();

// 1. Get the provider name from the URL (e.g., ?provider=github)
$provider = isset($_GET['provider']) ? $_GET['provider'] : "Social";

// 2. Format it nicely (e.g., "Github")
$clean_name = ucfirst($provider);

// 3. Create a "Fake" session for this user
// In a real app, we'd get their real name from Google/FB API.
$_SESSION['username'] = $clean_name . "_Guest";
$_SESSION['login_type'] = $provider;

// 4. Redirect to your homepage
header("Location: index.php?status=social_success");
exit();
?>