<?php
session_start();
include('db.php');
$msg = "";

// Security check: If they didn't come from the forgot-password flow, kick them out
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

if (isset($_POST['update_password'])) {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        // Use Prepared Statements for security
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
        $stmt->bind_param("ss", $hashed, $email);
        
        if ($stmt->execute()) {
            unset($_SESSION['reset_email']); 
            $msg = "Success! <a href='login.php' style='color:#5dade2; text-decoration:none;'>Login here</a>";
        } else {
            $msg = "Error updating database.";
        }
    } else {
        $msg = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | TailTalks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Locking the screen like the About page */
        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: radial-gradient(circle at center, #2c3e50 0%, #000000 100%);
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 25px;
            padding: 50px 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 25px;
            left: 25px;
            color: white;
            opacity: 0.6;
            text-decoration: none;
        }

        h2 { color: white; margin-bottom: 10px; font-weight: 500; }
        
        .message { 
            color: #5dade2; 
            font-size: 0.9rem; 
            margin-bottom: 25px;
            min-height: 20px;
        }

        input {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            box-sizing: border-box;
            outline: none;
        }

        input::placeholder { color: rgba(255,255,255,0.4); }

        .update-btn {
            width: 100%;
            padding: 15px;
            background: #5dade2;
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .update-btn:hover { background: #3498db; }
    </style>
</head>
<body>

    <div class="glass-card">
        <a href="login.php" class="back-btn"><i class="fa fa-arrow-left"></i></a>
        
        <h2>New Password</h2>
        <div class="message"><?php echo $msg; ?></div>

        <form method="POST">
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="update_password" class="update-btn">Update Password</button>
        </form>
    </div>

</body>
</html>