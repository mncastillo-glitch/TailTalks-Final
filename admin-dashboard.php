<?php 
session_start();

// 1. SECURITY: Access Control
if (!isset($_SESSION['staff_logged_in'])) {
    header("Location: staff-login.php");
    exit();
}

include 'db.php';

// 2. SECURITY: Prepared Statements for Actions (Week 16)
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

// 3. AUTO-MAPPING: Finding your specific Aiven column names
$columns_result = mysqli_query($conn, "SHOW COLUMNS FROM inquiries");
$cols = [];
while($c = mysqli_fetch_assoc($columns_result)) { $cols[] = $c['Field']; }

$name_col  = in_array('name', $cols) ? 'name' : 'user_name';
$email_col = in_array('email', $cols) ? 'email' : 'user_email';
$time_col  = in_array('submitted_at', $cols) ? 'submitted_at' : (in_array('created_at', $cols) ? 'created_at' : 'id');
$subj_col  = in_array('subject', $cols) ? 'subject' : 'breed_interest';

// 4. ANALYTICS: Aggregate Functions (Week 8)
$stats_query = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved FROM inquiries";
$stats_result = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));
$total = $stats_result['total'] ?? 0;
$res = $stats_result['resolved'] ?? 0;
$percent = ($total > 0) ? round(($res / $total) * 100) : 0;

// 5. SECURE SEARCH: Using Prepared Statements
$search = $_GET['search'] ?? '';
$search_param = "%$search%";
$query = "SELECT *, 
    CASE 
        WHEN message LIKE '%urgent%' THEN 'Urgent'
        WHEN $subj_col = 'Adoption' THEN 'Priority'
        ELSE 'General' 
    END AS priority_level
    FROM inquiries 
    WHERE ($name_col LIKE ? OR $email_col LIKE ? OR message LIKE ?)
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
    <title>TailTalks | Professional Admin</title>
    <style>
        body { background: #0f172a; color: #f1f5f9; font-family: 'Inter', sans-serif; padding: 40px; }
        .dashboard-card { background: #1e293b; border-radius: 20px; padding: 30px; border: 1px solid #334155; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #334155; padding: 20px; border-radius: 15px; border-left: 4px solid #38bdf8; }
        .progress-bar { height: 6px; background: #0f172a; border-radius: 10px; margin-top: 10px; overflow: hidden; }
        .search-container { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-input { flex: 1; background: #0f172a; border: 1px solid #475569; padding: 12px; border-radius: 10px; color: white; outline: none; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #0f172a; font-size: 0.8rem; text-transform: uppercase; color: #94a3b8; }
        td { padding: 15px; border-bottom: 1px solid #334155; font-size: 0.9rem; }
        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.8rem; border: none; cursor: pointer; transition: 0.2s; }
        .btn-blue { background: #38bdf8; color: #0f172a; }
        .btn-outline { border: 1px solid #475569; color: #94a3b8; }
        .resolved-row { background: rgba(15, 23, 42, 0.4); opacity: 0.6; }
    </style>
</head>
<body>

<div class="dashboard-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
        <h1>Staff Portal <span style="font-size:0.9rem; color:#38bdf8;">v2.0 Secure</span></h1>
        <a href="logout.php" class="btn btn-outline">Secure Logout</a>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <small>Total Inquiries</small>
            <h2><?php echo $total; ?></h2>
        </div>
        <div class="stat-box" style="border-color:#10b981;">
            <small>Resolution Rate</small>
            <h2><?php echo $percent; ?>%</h2>
            <div class="progress-bar"><div style="width:<?php echo $percent; ?>%; height:100%; background:#10b981;"></div></div>
        </div>
    </div>

    <form method="GET" class="search-container">
        <input type="text" name="search" class="search-input" placeholder="Search name, email, or content..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-blue">Filter</button>
        <button type="button" class="btn btn-outline" onclick="exportProfessionalCSV()">Professional Export</button>
    </form>

    <table id="adminTable">
        <thead>
            <tr>
                <th>Status</th>
                <th>Priority</th>
                <th>Sender & Email</th>
                <th>Message Content</th>
                <th>Date Sent</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): 
                $isRes = ($row['status'] === 'Resolved');
            ?>
            <tr class="<?php echo $isRes ? 'resolved-row' : ''; ?>">
                <td><b style="color:<?php echo $isRes ? '#10b981':'#f59e0b'; ?>;"><?php echo $row['status'] ?? 'New'; ?></b></td>
                <td><small><?php echo $row['priority_level']; ?></small></td>
                <td>
                    <b><?php echo htmlspecialchars($row[$name_col]); ?></b><br>
                    <small style="color:#94a3b8;"><?php echo htmlspecialchars($row[$email_col] ?? 'No Email'); ?></small>
                </td>
                <td style="max-width:250px;"><?php echo htmlspecialchars($row['message']); ?></td>
                <td><small><?php echo $row[$time_col] ?? 'Unknown'; ?></small></td>
                <td>
                    <?php if(!$isRes): ?>
                        <a href="?resolve_id=<?php echo $row['id']; ?>" class="btn btn-blue">Resolve</a>
                    <?php else: ?>
                        <a href="?delete_id=<?php echo $row['id']; ?>" style="color:#ef4444; text-decoration:none; font-size:0.8rem;" onclick="return confirm('Permanent Delete?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
// Professional CSV Export Logic
function exportProfessionalCSV() {
    let rows = document.querySelectorAll("#adminTable tr");
    let csvContent = "Status,Priority,Sender,Email,Message,DateSent\n";

    for (let i = 1; i < rows.length; i++) {
        let cells = rows[i].querySelectorAll("td");
        let status = cells[0].innerText;
        let priority = cells[1].innerText;
        let sender = cells[2].querySelector("b").innerText;
        let email = cells[2].querySelector("small").innerText;
        let message = cells[3].innerText.replace(/,/g, " "); // Remove commas to prevent breaking CSV
        let date = cells[4].innerText;
        
        csvContent += `${status},${priority},${sender},${email},${message},${date}\n`;
    }

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('href', url);
    a.setAttribute('download', 'TailTalks_Admin_Report.csv');
    a.click();
}
</script>

</body>
</html>
