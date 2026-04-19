<?php 
session_start();

// 1. SECURITY: Session Check
if (!isset($_SESSION['staff_logged_in'])) {
    header("Location: staff-login.php");
    exit();
}

include 'db.php';

// 2. SECURITY: Prepared Statements for Actions (Week 16 Hardening)
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

// 3. ANALYTICS: Database Statistics
$stats_query = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved FROM inquiries";
$stats_result = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));
$total = $stats_result['total'] ?? 0;
$res = $stats_result['resolved'] ?? 0;
$percent = ($total > 0) ? round(($res / $total) * 100) : 0;

// 4. DATA FETCHING: Using Actual Column Names
$search = $_GET['search'] ?? '';
$search_param = "%$search%";
$query = "SELECT *, 
    CASE 
        WHEN message LIKE '%urgent%' OR breed = 'Golden retriever' THEN 'Priority'
        ELSE 'General' 
    END AS priority_level
    FROM inquiries 
    WHERE (fullname LIKE ? OR email LIKE ? OR breed LIKE ?)
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
    <title>TailTalks | Elite Admin</title>
    <style>
        body { background: #0f172a; color: #f8fafc; font-family: 'Inter', sans-serif; padding: 40px; }
        .dashboard-card { background: #1e293b; border-radius: 20px; padding: 30px; border: 1px solid #334155; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #334155; padding: 20px; border-radius: 12px; border-left: 4px solid #38bdf8; }
        .progress-bar { height: 8px; background: #0f172a; border-radius: 10px; margin-top: 10px; overflow: hidden; }
        .search-container { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-input { flex: 1; background: #0f172a; border: 1px solid #475569; padding: 12px; border-radius: 8px; color: white; outline: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; background: #0f172a; font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; }
        td { padding: 15px; border-bottom: 1px solid #334155; font-size: 0.9rem; }
        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; }
        .btn-blue { background: #38bdf8; color: #0f172a; }
        .btn-red { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        .resolved-row { opacity: 0.5; filter: grayscale(0.5); }
    </style>
</head>
<body>

<div class="dashboard-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <h1>Staff Dashboard <span style="font-size:0.8rem; color:#38bdf8;">Secure Access</span></h1>
        <a href="logout.php" class="btn btn-red">Secure Logout</a>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <small>Database Population</small>
            <h2><?php echo $total; ?> Inquiries</h2>
        </div>
        <div class="stat-box" style="border-color:#10b981;">
            <small>Resolution Rate</small>
            <h2><?php echo $percent; ?>% Complete</h2>
            <div class="progress-bar"><div style="width:<?php echo $percent; ?>%; height:100%; background:#10b981;"></div></div>
        </div>
    </div>

    <form method="GET" class="search-container">
        <input type="text" name="search" class="search-input" placeholder="Search by name, email, or breed..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-blue">Filter Records</button>
        <button type="button" class="btn btn-blue" style="background:#475569;" onclick="exportProExcel()">Export Report</button>
    </form>

    <table id="adminTable">
        <thead>
            <tr>
                <th>Status</th>
                <th>Sender Detail</th>
                <th>Breed Interest</th>
                <th>Message Snippet</th>
                <th>Timestamp</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): 
                $isRes = ($row['status'] === 'Resolved');
            ?>
            <tr class="<?php echo $isRes ? 'resolved-row' : ''; ?>">
                <td><b style="color:<?php echo $isRes ? '#10b981':'#f59e0b'; ?>;"><?php echo $row['status'] ?? 'New'; ?></b></td>
                <td>
                    <strong><?php echo htmlspecialchars($row['fullname']); ?></strong><br>
                    <small style="color:#94a3b8;"><?php echo htmlspecialchars($row['email']); ?></small>
                </td>
                <td><small><?php echo htmlspecialchars($row['breed']); ?></small></td>
                <td><?php echo htmlspecialchars(substr($row['message'], 0, 50)); ?>...</td>
                <td><small><?php echo $row['submitted_at']; ?></small></td>
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
// Professional CSV Export
function exportProExcel() {
    let csv = "Status,Full Name,Email,Breed,Message,Date\n";
    let rows = document.querySelectorAll("#adminTable tbody tr");
    rows.forEach(tr => {
        let cells = tr.querySelectorAll("td");
        let name = cells[1].querySelector("strong").innerText;
        let email = cells[1].querySelector("small").innerText;
        csv += `"${cells[0].innerText}","${name}","${email}","${cells[2].innerText}","${cells[3].innerText}","${cells[4].innerText}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'TailTalks_Inquiry_Report.csv';
    a.click();
}
</script>

</body>
</html>
