<?php 
session_start();

// 1. Security Check (CITE005 - Access Control)
if (!isset($_SESSION['staff_logged_in'])) {
    header("Location: staff-login.php");
    exit();
}

include 'db.php';

// 2. Handle CRUD Update (Week 7: DML)
if (isset($_GET['resolve_id'])) {
    $id = intval($_GET['resolve_id']);
    mysqli_query($conn, "UPDATE inquiries SET status = 'Resolved' WHERE id = $id");
    header("Location: admin-dashboard.php");
    exit();
}

// 3. Aggregate Functions (Week 8: SQL Functions)
$stats_query = "SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN status = 'New' OR status IS NULL THEN 1 ELSE 0 END) as pending
    FROM inquiries";
$stats_result = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));

// 4. Handle Search and Advanced Logic (Week 7 & 11: DQL & CASE)
$search = $_GET['search'] ?? '';
$query = "SELECT *, 
    CASE 
        WHEN message LIKE '%urgent%' OR message LIKE '%emergency%' THEN 'Urgent'
        WHEN subject = 'Adoption' THEN 'Priority'
        ELSE 'General'
    END AS priority_level
    FROM inquiries 
    WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR message LIKE '%$search%'
    ORDER BY status ASC, id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | TailTalks</title>
    <style>
        body { 
            background: radial-gradient(circle at center, #1e293b, #000000); 
            color: white; 
            font-family: 'Segoe UI', sans-serif; 
            margin: 0; padding: 40px; min-height: 100vh;
        }
        .dashboard-card {
            background: rgba(255, 255, 255, 0.03); 
            backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 30px; padding: 40px; max-width: 1200px; margin: auto;
        }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        h1 { color: #5dade2; margin: 0; font-weight: 800; }
        
        /* Stats Styling (Week 8) */
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: rgba(255,255,255,0.05); padding: 20px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); }
        
        /* Search Styling (Week 7) */
        .search-container { margin-bottom: 30px; display: flex; gap: 10px; }
        .search-input { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 12px; color: white; }

        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { text-align: left; padding: 15px; color: rgba(255,255,255,0.5); text-transform: uppercase; font-size: 0.75rem; }
        td { padding: 20px 15px; background: rgba(255,255,255,0.05); }
        td:first-child { border-radius: 15px 0 0 15px; }
        td:last-child { border-radius: 0 15px 15px 0; }

        .status-badge { padding: 5px 12px; border-radius: 6px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; }
        .status-new { background: #f59e0b; color: #000; }
        .status-resolved { background: #10b981; color: white; opacity: 0.7; }
        
        .priority-urgent { color: #f87171; font-weight: bold; }
        .priority-normal { color: #5dade2; }

        .btn-resolve { background: #5dade2; color: white; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 0.8rem; font-weight: bold; transition: 0.3s; border: none; cursor: pointer;}
        .btn-resolve:hover { background: white; color: #1e293b; }
        .resolved-row td { opacity: 0.4; filter: grayscale(0.5); }
        .logout { background: rgba(231, 76, 60, 0.15); color: #f87171; text-decoration: none; padding: 10px 25px; border-radius: 12px; border: 1px solid rgba(248, 113, 113, 0.2); }
    </style>
</head>
<body>

<div class="dashboard-card">
    <div class="header">
        <div>
            <h1>Staff Dashboard</h1>
            <p style="color: rgba(255,255,255,0.5);">Pet Inquiry Management System</p>
        </div>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <small>Total Database Records</small>
            <h2 style="margin: 5px 0;"><?php echo $stats_result['total']; ?></h2>
        </div>
        <div class="stat-box" style="border-color: #f59e0b;">
            <small>Pending Tasks</small>
            <h2 style="margin: 5px 0; color: #f59e0b;"><?php echo $stats_result['pending']; ?></h2>
        </div>
    </div>

    <form method="GET" class="search-container">
        <input type="text" name="search" class="search-input" placeholder="Search inquiries..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-resolve">Filter Results</button>
        <?php if($search): ?> <a href="admin-dashboard.php" style="color: grey; padding: 10px;">Clear</a> <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Level (CASE)</th>
                <th>Sender</th>
                <th>Message Snippet</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $isResolved = ($row['status'] === 'Resolved');
                $priority = $row['priority_level'];
            ?>
            <tr class="<?php echo $isResolved ? 'resolved-row' : ''; ?>">
                <td>
                    <span class="status-badge <?php echo $isResolved ? 'status-resolved' : 'status-new'; ?>">
                        <?php echo $row['status'] ?? 'New'; ?>
                    </span>
                </td>
                <td class="<?php echo $priority === 'Urgent' ? 'priority-urgent' : 'priority-normal'; ?>">
                    <?php echo $priority; ?>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($row['name'] ?? 'Guest'); ?></strong><br>
                    <small><?php echo htmlspecialchars($row['email'] ?? ''); ?></small>
                </td>
                <td style="max-width: 250px; font-size: 0.85rem;"><?php echo htmlspecialchars($row['message']); ?></td>
                <td>
                    <?php if (!$isResolved): ?>
                        <a href="admin-dashboard.php?resolve_id=<?php echo $row['id']; ?>" class="btn-resolve" onclick="return confirm('Update status to Resolved?')">Resolve</a>
                    <?php else: ?>
                        <span style="color: #10b981;">Archived ✅</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
