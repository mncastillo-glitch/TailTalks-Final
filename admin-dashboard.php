<?php 
session_start();

if (!isset($_SESSION['staff_logged_in'])) {
    header("Location: staff-login.php");
    exit();
}

include 'db.php';

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

$stats_query = "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved FROM inquiries";
$stats_result = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));
$total = $stats_result['total'] ?? 0;
$res = $stats_result['resolved'] ?? 0;
$percent = ($total > 0) ? round(($res / $total) * 100) : 0;

$users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'] ?? 0;
$breeds_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM breeds"))['total'] ?? 0;

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

// Store all rows in array first
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TailTalks | Elite Admin</title>
    <style>
        body { background: #0f172a; color: #f8fafc; font-family: 'Inter', sans-serif; padding: 40px; }
        .dashboard-card { background: #1e293b; border-radius: 20px; padding: 30px; border: 1px solid #334155; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #334155; padding: 20px; border-radius: 12px; border-left: 4px solid #38bdf8; }
        .progress-bar { height: 8px; background: #0f172a; border-radius: 10px; margin-top: 10px; overflow: hidden; }
        .search-container { display: flex; gap: 10px; margin-bottom: 15px; }
        .search-input { flex: 1; background: #0f172a; border: 1px solid #475569; padding: 12px; border-radius: 8px; color: white; outline: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; background: #0f172a; font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; }
        td { padding: 15px; border-bottom: 1px solid #334155; font-size: 0.9rem; }
        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; font-size: 0.85rem; }
        .btn-blue { background: #38bdf8; color: #0f172a; }
        .btn-red { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-purple { background: rgba(167, 139, 250, 0.2); color: #a78bfa; border: 1px solid rgba(167, 139, 250, 0.3); cursor: pointer; }
        .resolved-row { opacity: 0.5; filter: grayscale(0.5); }

        .filter-tabs { display: flex; gap: 10px; margin-bottom: 15px; }
        .filter-tab { padding: 8px 18px; border-radius: 20px; border: 1px solid #475569; background: transparent; color: #94a3b8; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
        .filter-tab.active { background: #38bdf8; color: #0f172a; border-color: #38bdf8; font-weight: bold; }
        .filter-tab:hover:not(.active) { border-color: #38bdf8; color: #38bdf8; }

        /* Modal */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 30px; max-width: 500px; width: 90%; position: relative; }
        .modal-close { position: absolute; top: 15px; right: 20px; background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; }
        .modal-close:hover { color: white; }
        .modal-field { margin-bottom: 15px; }
        .modal-label { font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
        .modal-value { background: #0f172a; padding: 10px 14px; border-radius: 8px; font-size: 0.9rem; line-height: 1.6; word-wrap: break-word; }

        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            body { padding: 15px; }
        }
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
        <div class="stat-box" style="border-color:#a78bfa;">
            <small>Registered Users</small>
            <h2><?php echo $users_count; ?> Users</h2>
        </div>
        <div class="stat-box" style="border-color:#f59e0b;">
            <small>Total Breeds</small>
            <h2><?php echo $breeds_count; ?> Breeds</h2>
        </div>
    </div>

    <div class="search-container">
        <input type="text" id="liveSearch" class="search-input" placeholder="Search by name, email, or breed..." oninput="liveFilter()">
        <button type="button" class="btn btn-blue" onclick="liveFilter()">Filter Records</button>
        <button type="button" class="btn btn-blue" style="background:#475569;" onclick="exportProExcel()">Export Report</button>
    </div>

    <div class="filter-tabs">
        <button class="filter-tab active" onclick="filterTab('all', this)">All</button>
        <button class="filter-tab" onclick="filterTab('new', this)">New</button>
        <button class="filter-tab" onclick="filterTab('resolved', this)">Resolved</button>
    </div>

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
        <tbody id="tableBody">
            <?php foreach ($rows as $index => $row): 
                $isRes = ($row['status'] === 'Resolved');
                $statusLabel = $row['status'] ?? 'New';
                $timestamp = date('M d, Y h:i A', strtotime($row['submitted_at']));
                $snippet = strlen($row['message']) > 50 ? substr($row['message'], 0, 50) . '...' : $row['message'];
            ?>
            <tr class="<?php echo $isRes ? 'resolved-row' : ''; ?>" 
                data-status="<?php echo strtolower($statusLabel); ?>"
                data-search="<?php echo strtolower(htmlspecialchars($row['fullname'] . ' ' . $row['email'] . ' ' . $row['breed'])); ?>"
                data-id="<?php echo $index; ?>">
                <td><b style="color:<?php echo $isRes ? '#10b981':'#f59e0b'; ?>;"><?php echo $statusLabel; ?></b></td>
                <td>
                    <strong><?php echo htmlspecialchars($row['fullname']); ?></strong><br>
                    <small style="color:#94a3b8;"><?php echo htmlspecialchars($row['email']); ?></small>
                </td>
                <td><small><?php echo htmlspecialchars($row['breed']); ?></small></td>
                <td style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-size:0.85rem;">
                    <?php echo htmlspecialchars($snippet); ?>
                </td>
                <td><small><?php echo $timestamp; ?></small></td>
                <td style="display:flex; gap:6px; flex-wrap:wrap;">
                    <!-- FIX: Use data attributes instead of inline JS parameters -->
                    <button class="btn btn-purple view-btn"
                        data-name="<?php echo htmlspecialchars($row['fullname'], ENT_QUOTES); ?>"
                        data-email="<?php echo htmlspecialchars($row['email'], ENT_QUOTES); ?>"
                        data-breed="<?php echo htmlspecialchars($row['breed'], ENT_QUOTES); ?>"
                        data-message="<?php echo htmlspecialchars($row['message'], ENT_QUOTES); ?>"
                        data-timestamp="<?php echo $timestamp; ?>"
                        data-status="<?php echo $statusLabel; ?>">
                        View
                    </button>
                    <?php if(!$isRes): ?>
                        <a href="?resolve_id=<?php echo $row['id']; ?>" class="btn btn-blue">Resolve</a>
                    <?php else: ?>
                        <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-red" onclick="return confirm('Delete permanently?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="noResults" style="display:none; text-align:center; padding:30px; color:#94a3b8;">
        No inquiries found matching your search.
    </div>
</div>

<!-- VIEW MESSAGE MODAL -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal()">✕</button>
        <h2 style="margin-bottom:20px; color:#38bdf8;">Inquiry Details</h2>
        <div class="modal-field">
            <div class="modal-label">Full Name</div>
            <div class="modal-value" id="mName"></div>
        </div>
        <div class="modal-field">
            <div class="modal-label">Email</div>
            <div class="modal-value" id="mEmail"></div>
        </div>
        <div class="modal-field">
            <div class="modal-label">Breed Interest</div>
            <div class="modal-value" id="mBreed"></div>
        </div>
        <div class="modal-field">
            <div class="modal-label">Full Message</div>
            <div class="modal-value" id="mMessage" style="min-height:80px;"></div>
        </div>
        <div style="display:flex; gap:10px;">
            <div class="modal-field" style="flex:1;">
                <div class="modal-label">Submitted</div>
                <div class="modal-value" id="mTimestamp"></div>
            </div>
            <div class="modal-field" style="flex:1;">
                <div class="modal-label">Status</div>
                <div class="modal-value" id="mStatus"></div>
            </div>
        </div>
    </div>
</div>

<script>
// FIX: Use event delegation with data attributes — no more broken inline JS
document.getElementById('tableBody').addEventListener('click', function(e) {
    const btn = e.target.closest('.view-btn');
    if (!btn) return;

    document.getElementById('mName').textContent      = btn.dataset.name;
    document.getElementById('mEmail').textContent     = btn.dataset.email;
    document.getElementById('mBreed').textContent     = btn.dataset.breed;
    document.getElementById('mMessage').textContent   = btn.dataset.message;
    document.getElementById('mTimestamp').textContent = btn.dataset.timestamp;
    document.getElementById('mStatus').textContent    = btn.dataset.status;

    document.getElementById('viewModal').classList.add('active');
});

function closeModal() {
    document.getElementById('viewModal').classList.remove('active');
}

document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

let currentTab = 'all';

function liveFilter() {
    const search = document.getElementById('liveSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        const searchData = row.getAttribute('data-search') || '';
        const status = row.getAttribute('data-status') || '';
        const matchesSearch = searchData.includes(search);
        const matchesTab = currentTab === 'all' || status === currentTab;
        const show = matchesSearch && matchesTab;
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
}

function filterTab(type, btn) {
    currentTab = type;
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    liveFilter();
}

function exportProExcel() {
    let csv = "Status,Full Name,Email,Breed,Message,Date\n";
    let rows = document.querySelectorAll("#adminTable tbody tr");
    rows.forEach(tr => {
        if (tr.style.display === 'none') return;
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
