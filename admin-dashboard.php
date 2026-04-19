<?php 
session_start();

// 1. SECURITY: Session Check & Prepared Statements (Week 16)
if (!isset($_SESSION['staff_logged_in'])) {
    header("Location: staff-login.php");
    exit();
}

include 'db.php';

// 2. SECURITY: CRUD Operations using Prepared Statements (Prevents SQL Injection)
if (isset($_GET['resolve_id'])) {
    $stmt = $conn->prepare("UPDATE inquiries SET status = 'Resolved' WHERE id = ?");
    $stmt->bind_param("i", $_GET['resolve_id']);
    $stmt->execute();
    header("Location: admin-dashboard.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM inquiries WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: admin-dashboard.php");
    exit();
}

// 3. DYNAMIC COLUMN DETECTION (Self-Healing Schema)
$columns_result = mysqli_query($conn, "SHOW COLUMNS FROM inquiries");
$cols = [];
while($c = mysqli_fetch_assoc($columns_result)) { $cols[] = $c['Field']; }

$name_col  = in_array('name', $cols) ? 'name' : (in_array('user_name', $cols) ? 'user_name' : 'id');
$subj_col  = in_array('subject', $cols) ? 'subject' : (in_array('breed_interest', $cols) ? 'breed_interest' : "'General'");

// 4. AGGREGATE STATS (Week 8: SQL Functions)
$stats_query = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved FROM inquiries";
$stats_result = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));
$total = $stats_result['total'] ?? 0;
$res = $stats_result['resolved'] ?? 0;
$percent = ($total > 0) ? round(($res / $total) * 100) : 0;

// 5. SECURITY: Filtered Search with Prepared Statements
$search = $_GET['search'] ?? '';
$search_param = "%$search%";
$query = "SELECT *, 
    CASE 
        WHEN message LIKE '%urgent%' OR message LIKE '%emergency%' THEN 'Urgent'
        WHEN $subj_col = 'Adoption' THEN 'Priority'
        ELSE 'General' 
    END AS priority_level
    FROM inquiries 
    WHERE ($name_col LIKE ? OR message LIKE ? OR $subj_col LIKE ?)
    ORDER BY status ASC, id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Elite Staff Dashboard | TailTalks</title>
    <style>
        body { background: radial-gradient(circle at center, #1e293b, #000000); color: white; font-family: 'Segoe UI', sans-serif; padding: 40px; }
        .dashboard-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border-radius: 30px; padding: 40px; max-width: 1200px; margin: auto; border: 1px solid rgba(255,255,255,0.1); }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: rgba(255,255,255,0.05); padding: 20px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); }
        .progress-bar { height: 8px; background: rgba(255,255,255,0.1); border-radius: 10px; margin-top: 15px; overflow: hidden; }
        .search-container { margin-bottom: 30px; display: flex; gap: 10px; }
        .search-input { flex: 1; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 12px; color: white; }
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        td { padding: 20px; background: rgba(255,255,255,0.04); transition: 0.3s; }
        .btn { padding: 8px 16px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 0.8rem; border: none; cursor: pointer; }
        .btn-blue { background: #5dade2; color: white; }
        .btn-red { background: rgba(231, 76, 60, 0.2); color: #f87171; border: 1px solid rgba(231, 76, 60, 0.2); }
        .resolved-row td { opacity: 0.4; }
    </style>
</head>
<body>

<div class="dashboard-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <div>
            <h1 style="margin:0; color:#5dade2;">Staff Portal</h1>
            <small style="opacity:0.5;">Secure Management System (Week 16 Protected)</small>
        </div>
        <a href="logout.php" class="btn btn-red" style="padding:12px 25px;">Secure Logout</a>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <small>Database Integrity</small>
            <h2><?php echo $total; ?> Total Entries</h2>
            <div class="progress-bar"><div style="width:100%; height:100%; background:#5dade2;"></div></div>
        </div>
        <div class="stat-box">
            <small>Resolution Rate</small>
            <h2 style="color:#10b981;"><?php echo $percent; ?>% Completed</h2>
            <div class="progress-bar"><div style="width:<?php echo $percent; ?>%; height:100%; background:#10b981;"></div></div>
        </div>
    </div>

    <form method="GET" class="search-container">
        <input type="text" name="search" class="search-input" placeholder="Search secure records..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-blue">Apply Filter</button>
        <button type="button" class="btn btn-blue" onclick="exportTableToCSV('tailtalks-data.csv')">Export to Excel</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Priority</th>
                <th>User Info</th>
                <th>Message Snippet</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): 
                $isRes = ($row['status'] === 'Resolved');
            ?>
            <tr class="<?php echo $isRes ? 'resolved-row' : ''; ?>">
                <td><span style="color:<?php echo $isRes ? '#10b981':'#f59e0b'; ?>; font-weight:bold;"><?php echo $row['status'] ?? 'New'; ?></span></td>
                <td><small><?php echo $row['priority_level']; ?></small></td>
                <td><strong><?php echo htmlspecialchars($row[$name_col]); ?></strong></td>
                <td><small><?php echo htmlspecialchars(substr($row['message'], 0, 50)); ?>...</small></td>
                <td>
                    <?php if(!$isRes): ?>
                        <a href="?resolve_id=<?php echo $row['id']; ?>" class="btn btn-blue">Resolve</a>
                    <?php else: ?>
                        <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-red" onclick="return confirm('Delete permanently?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
// Simple Export Function
function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("table tr");
    for (var i = 0; i < rows.size; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.size; j++) row.push(cols[j].innerText);
        csv.push(row.join(","));
    }
    var csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

</body>
</html>
