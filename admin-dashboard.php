<?php 
session_start();

// 1. Security Check
if (!isset($_SESSION['staff_logged_in'])) {
    header("Location: staff-login.php");
    exit();
}

include 'db.php';

// 2. Handle "Resolve" Action (The 'Update' part of CRUD)
if (isset($_GET['resolve_id'])) {
    $id = intval($_GET['resolve_id']);
    $update_query = "UPDATE inquiries SET status = 'Resolved' WHERE id = $id";
    mysqli_query($conn, $update_query);
    header("Location: admin-dashboard.php"); // Refresh to show updated status
    exit();
}

// 3. Fetch Data from Aiven
// We use COALESCE or checks in the loop to handle different column names
$query = "SELECT * FROM inquiries ORDER BY id DESC"; 
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | TailTalks</title>
    <style>
        /* Modern Dark Theme - Glassmorphism */
        body { 
            background: radial-gradient(circle at center, #1e293b, #000000); 
            color: white; 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
            margin: 0;
            padding: 40px;
            min-height: 100vh;
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            max-width: 1200px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        h1 { color: #5dade2; margin: 0; font-weight: 800; font-size: 2rem; }

        table { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0 10px; 
        }

        th { 
            text-align: left; 
            padding: 15px; 
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1.5px;
        }

        td { 
            padding: 20px 15px; 
            background: rgba(255,255,255,0.05);
            border-top: 1px solid rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        td:first-child { border-radius: 15px 0 0 15px; border-left: 1px solid rgba(255,255,255,0.05); }
        td:last-child { border-radius: 0 15px 15px 0; border-right: 1px solid rgba(255,255,255,0.05); }

        /* Status & Badge Styles */
        .status-badge {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-new { background: #f59e0b; color: #000; }
        .status-resolved { background: #10b981; color: white; opacity: 0.7; }

        .breed-badge {
            background: rgba(93, 173, 226, 0.2);
            color: #5dade2;
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.85rem;
        }

        /* Action Buttons */
        .btn-resolve {
            background: #5dade2;
            color: white;
            padding: 8px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: bold;
            transition: 0.3s;
            display: inline-block;
        }
        .btn-resolve:hover {
            background: white;
            color: #1e293b;
            transform: translateY(-2px);
        }

        .resolved-row td {
            opacity: 0.4;
            filter: grayscale(0.5);
        }

        .logout { 
            background: rgba(231, 76, 60, 0.15);
            color: #f87171; 
            text-decoration: none; 
            font-weight: bold; 
            padding: 10px 25px;
            border-radius: 12px;
            border: 1px solid rgba(248, 113, 113, 0.2);
            transition: 0.3s;
        }
        .logout:hover { background: #f87171; color: white; }

        small { color: rgba(255,255,255,0.4); }
    </style>
</head>
<body>

<div class="dashboard-card">
    <div class="header">
        <div>
            <h1>Staff Dashboard</h1>
            <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.5);">Management Portal for TailTalks</p>
        </div>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Sender Info</th>
                <th>Interest</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): 
                // Determine if row is resolved
                $status = $row['status'] ?? 'New';
                $isResolved = ($status === 'Resolved');
                
                // Fallback for date columns
                $displayDate = $row['submitted_at'] ?? $row['date'] ?? $row['created_at'] ?? 'Recently';
            ?>
            <tr class="<?php echo $isResolved ? 'resolved-row' : ''; ?>">
                <td>
                    <small><?php echo (strtotime($displayDate)) ? date('M d, Y', strtotime($displayDate)) : $displayDate; ?></small>
                </td>
                
                <td>
                    <strong><?php echo htmlspecialchars($row['name'] ?? $row['user_name'] ?? 'Guest'); ?></strong><br>
                    <small><?php echo htmlspecialchars($row['email'] ?? $row['user_email'] ?? 'No Email'); ?></small>
                </td>

                <td>
                    <span class="breed-badge">
                        <?php echo htmlspecialchars($row['subject'] ?? $row['breed_interest'] ?? 'General'); ?>
                    </span>
                </td>
                
                <td style="font-size: 0.9rem; line-height: 1.5; max-width: 300px;">
                    <?php echo htmlspecialchars($row['message']); ?>
                </td>

                <td>
                    <span class="status-badge <?php echo $isResolved ? 'status-resolved' : 'status-new'; ?>">
                        <?php echo $status; ?>
                    </span>
                </td>

                <td>
                    <?php if (!$isResolved): ?>
                        <a href="admin-dashboard.php?resolve_id=<?php echo $row['id']; ?>" class="btn-resolve" onclick="return confirm('Mark this inquiry as resolved?')">Resolve</a>
                    <?php else: ?>
                        <span style="color: #10b981; font-size: 0.8rem;">Completed ✅</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

give me the whole code!
