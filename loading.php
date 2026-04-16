<?php
session_start();
// Get the provider from the URL (google, facebook, or github)
$provider = isset($_GET['provider']) ? ucfirst($_GET['provider']) : "Social";
$_SESSION['username'] = $provider . "_Guest";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Connecting to <?php echo $provider; ?>...</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; flex-direction: column; }
        .loader { border: 8px solid #f3f3f3; border-top: 8px solid #5dade2; border-radius: 50%; width: 60px; height: 60px; animation: spin 1s linear infinite; margin-bottom: 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        h2 { color: #555; }
    </style>
    <meta http-equiv="refresh" content="2;url=index.php?login=success">
</head>
<body>
    <div class="loader"></div>
    <h2>Connecting to <?php echo $provider; ?>...</h2>
    <p style="color: #888;">Please do not refresh the page.</p>
</body>
</html>