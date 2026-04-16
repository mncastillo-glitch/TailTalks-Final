<?php 
session_start();
if (!isset($_SESSION['staff_logged_in'])) {
    header("Location: staff-login.php");
    exit();
}
include 'db.php';
$result = mysqli_query($conn, $result_query = "SELECT * FROM inquiries ORDER BY submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | TailTalks</title>
    <style>
        /* 1. Glassmorphism Background */
        body { 
            background: radial-gradient(circle at center, #1e293b, #000000); 
            color: white; 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0;
            padding: 40px;
            min-height: 100vh;
        }

        /* 2. Main Container Card */
        .dashboard-card {
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            max-width: 1100px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        h1 { color: #5dade2; margin: 0; font-weight: 800; font-size: 2rem; }

        /* 3. Styled Table */
        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0 10px; /* Adds space between rows */
        }

        th { 
            text-align: left; 
            padding: 15px; 
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        td { 
            padding: 20px 15px; 
            background: rgba(255,255,255,0.05);
            border-top: 1px solid rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        /* Round the corners of the rows */
        td:first-child { border-radius: 15px 0 0 15px; border-left: 1px solid rgba(255,255,255,0.05); }
        td:last-child { border-radius: 0 15px 15px 0; border-right: 1px solid rgba(255,255,255,0.05); }

        tr:hover td {
            background: rgba(255,255,255,0.1);
            border-color: rgba(93, 173, 226, 0.3);
            transition: 0.3s;
        }

        .breed-badge {
            background: rgba(93, 173, 226, 0.2);
            color: #5dade2;
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .logout { 
            background: rgba(231, 76, 60, 0.15);
            color: #f87171; 
            text-decoration: none; 
            font-weight: bold; 
            padding: 10px 20px;
            border-radius: 12px;
            border: 1px solid rgba(248, 113, 113, 0.2);
            transition: 0.3s;
        }

        .logout:hover {
            background: #f87171;
            color: white;
        }

        small { color: rgba(255,255,255,0.4); }
    </style>
</head>
<body>

<div class="dashboard-card">
    <div class="header">
        <h1>Staff Dashboard</h1>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <h3>Incoming Inquiries</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Sender</th>
                <th>Interested In</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><small><?php echo date('M d, Y', strtotime($row['submitted_at'] ?? $row['date'])); ?></small></td>
        
        <td>
            <strong><?php echo htmlspecialchars($row['name'] ?? $row['user_name'] ?? 'Guest'); ?></strong><br>
            <small><?php echo htmlspecialchars($row['email'] ?? $row['user_email'] ?? ''); ?></small>
        </td>

        <td><span class="breed-badge"><?php echo htmlspecialchars($row['subject'] ?? $row['breed_interest'] ?? 'General'); ?></span></td>
        
        <td style="font-size: 0.95rem; line-height: 1.4;"><?php echo htmlspecialchars($row['message']); ?></td>
    </tr>
    <?php endwhile; ?>
</tbody>
    </table>
</div>

</body>
</html>