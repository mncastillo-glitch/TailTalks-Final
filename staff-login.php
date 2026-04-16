<?php
session_start();
include('db.php');
$error = "";

if (isset($_POST['staff_login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    // For your presentation: Admin credentials
    // You can also check this against a 'staff' table if you create one
    if ($user === 'admin' && $pass === 'admin123') { 
        $_SESSION['staff_logged_in'] = true;
        header("Location: admin-dashboard.php");
        exit();
    } else {
        $error = "Invalid Staff Credentials.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Portal | TailTalks</title>
    <style>
        body { 
            background: #0f172a; color: white; font-family: 'Segoe UI', sans-serif; 
            display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;
        }
        .login-card { 
            background: rgba(255,255,255,0.05); padding: 40px; border-radius: 20px; 
            border: 1px solid rgba(255,255,255,0.1); width: 100%; max-width: 350px; text-align: center; 
        }
        h2 { color: #5dade2; margin-bottom: 20px; }
        input { 
            width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: none; 
            background: rgba(255,255,255,0.1); color: white; box-sizing: border-box;
        }
        button { 
            width: 100%; padding: 12px; background: #5dade2; border: none; color: white; 
            font-weight: bold; border-radius: 8px; cursor: pointer; margin-top: 10px;
        }
        .error { color: #e74c3c; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Staff Access</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Staff Username" required>
            <input type="password" name="password" placeholder="Staff Password" required>
            <button type="submit" name="staff_login">Enter Dashboard</button>
        </form>
        <p style="margin-top:20px; font-size:12px;"><a href="index.php" style="color:#aaa; text-decoration:none;">Return to Site</a></p>
    </div>
</body>
</html>