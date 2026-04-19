<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$user_stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

// CANCEL inquiry
if (isset($_GET['cancel_id'])) {
    $cancel_id = (int)$_GET['cancel_id'];
    $del = $conn->prepare("DELETE FROM inquiries WHERE id = ? AND email = ? AND status = 'New'");
    $del->bind_param("is", $cancel_id, $user['email']);
    $del->execute();
    header("Location: my-inquiries.php?cancelled=1");
    exit();
}

// Get inquiries
$inq_stmt = $conn->prepare("SELECT * FROM inquiries WHERE email = ? ORDER BY submitted_at DESC");
$inq_stmt->bind_param("s", $user['email']);
$inq_stmt->execute();
$inq_rows = $inq_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total    = count($inq_rows);
$resolved = count(array_filter($inq_rows, fn($r) => $r['status'] === 'Resolved'));
$pending  = $total - $resolved;

// Time ago helper
function timeAgo($datetime) {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->d == 0 && $diff->h == 0) return $diff->i . ' min ago';
    if ($diff->d == 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->d == 1) return 'Yesterday';
    if ($diff->d < 7) return $diff->d . ' days ago';
    return date('M d, Y', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inquiries | TailTalks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: #0f172a;
            color: #f8fafc;
            padding: 30px 20px;
        }

        body::before {
            content: "";
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: url('images/Collage.jpg') no-repeat center/cover;
            filter: blur(12px) brightness(0.25);
            transform: scale(1.1);
            z-index: 0;
        }

        .container { position: relative; z-index: 1; max-width: 900px; margin: 0 auto; }

        /* Header */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: #38bdf8; }
        .page-subtitle { color: #94a3b8; font-size: 0.9rem; margin-top: 4px; }
        .nav-links { display: flex; gap: 10px; flex-wrap: wrap; }
        .nav-btn { padding: 10px 18px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.85rem; transition: 0.2s; }
        .nav-btn-outline { border: 1px solid #475569; color: #94a3b8; }
        .nav-btn-outline:hover { border-color: #38bdf8; color: #38bdf8; }
        .nav-btn-primary { background: #38bdf8; color: #0f172a; }
        .nav-btn-primary:hover { background: #0ea5e9; }

        /* Toast */
        .toast { background: rgba(16,185,129,0.15); border: 1px solid #10b981; color: #10b981; padding: 12px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; }

        /* Stats */
        .stats-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 25px; }
        .stat-card { background: rgba(30,41,59,0.9); border: 1px solid #334155; border-radius: 14px; padding: 20px; text-align: center; }
        .stat-num { font-size: 2rem; font-weight: 800; }
        .stat-label { font-size: 0.8rem; color: #94a3b8; margin-top: 4px; }

        /* Filter Tabs */
        .filter-row { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .ftab { padding: 8px 20px; border-radius: 20px; border: 1px solid #475569; background: transparent; color: #94a3b8; cursor: pointer; font-size: 0.85rem; transition: 0.2s; }
        .ftab.active { background: #38bdf8; color: #0f172a; border-color: #38bdf8; font-weight: bold; }

        /* Inquiry Card */
        .inquiry-card { background: rgba(30,41,59,0.95); border: 1px solid #334155; border-radius: 16px; padding: 24px; margin-bottom: 16px; transition: transform 0.2s; }
        .inquiry-card:hover { transform: translateY(-2px); }
        .inquiry-card.pending { border-left: 4px solid #f59e0b; }
        .inquiry-card.resolved { border-left: 4px solid #10b981; }

        .card-top { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; }
        .breed-name { font-size: 1.1rem; font-weight: 700; }
        .time-ago { font-size: 0.78rem; color: #64748b; margin-top: 3px; }

        .status-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
        .status-pending { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
        .status-resolved { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }

        .inquiry-message { margin-top: 12px; padding: 12px; background: rgba(15,23,42,0.6); border-radius: 8px; font-size: 0.9rem; color: #cbd5e1; line-height: 1.6; }

        /* Status Timeline */
        .timeline { display: flex; align-items: center; margin-top: 16px; gap: 0; }
        .tl-step { display: flex; flex-direction: column; align-items: center; flex: 1; }
        .tl-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold; border: 2px solid #334155; background: #0f172a; color: #475569; transition: 0.3s; }
        .tl-circle.done { background: #10b981; border-color: #10b981; color: white; }
        .tl-circle.active { background: #f59e0b; border-color: #f59e0b; color: white; animation: pulse 2s infinite; }
        .tl-label { font-size: 0.7rem; color: #64748b; margin-top: 5px; text-align: center; }
        .tl-label.done { color: #10b981; }
        .tl-label.active { color: #f59e0b; }
        .tl-line { flex: 1; height: 2px; background: #334155; }
        .tl-line.done { background: #10b981; }

        @keyframes pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.4); } 50% { box-shadow: 0 0 0 8px rgba(245,158,11,0); } }

        /* Admin Response */
        .admin-response { margin-top: 15px; background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.25); border-radius: 12px; padding: 16px; }
        .admin-response-label { font-size: 0.75rem; color: #10b981; text-transform: uppercase; font-weight: 700; margin-bottom: 8px; }
        .admin-response-text { font-size: 0.9rem; color: #f8fafc; line-height: 1.6; }

        /* Contact Section */
        .contact-section { margin-top: 15px; background: rgba(56,189,248,0.08); border: 1px solid rgba(56,189,248,0.25); border-radius: 12px; padding: 16px; }
        .contact-label { font-size: 0.75rem; color: #38bdf8; text-transform: uppercase; font-weight: 700; margin-bottom: 12px; }
        .contact-items { display: flex; gap: 20px; flex-wrap: wrap; }
        .contact-item { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #cbd5e1; }

        /* Pending Info */
        .pending-info { margin-top: 15px; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); border-radius: 10px; padding: 14px; font-size: 0.85rem; color: #fcd34d; display: flex; align-items: center; gap: 10px; }

        /* Action Buttons */
        .card-actions { display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap; }
        .action-btn { padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; }
        .btn-cancel { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
        .btn-cancel:hover { background: rgba(239,68,68,0.25); }
        .btn-reinquire { background: rgba(56,189,248,0.15); color: #38bdf8; border: 1px solid rgba(56,189,248,0.3); }
        .btn-reinquire:hover { background: rgba(56,189,248,0.25); }
        .btn-print { background: rgba(167,139,250,0.15); color: #a78bfa; border: 1px solid rgba(167,139,250,0.3); }
        .btn-print:hover { background: rgba(167,139,250,0.25); }

        /* Empty State */
        .empty-state { text-align: center; padding: 60px 20px; background: rgba(30,41,59,0.9); border: 1px solid #334155; border-radius: 16px; }
        .empty-state .emoji { font-size: 3rem; margin-bottom: 15px; }

        /* Print Style */
        @media print {
            body::before { display: none; }
            body { background: white; color: black; padding: 0; }
            .nav-links, .card-actions, .filter-row, .page-header .nav-links { display: none; }
            .inquiry-card { border: 1px solid #ccc; break-inside: avoid; }
            .admin-response, .contact-section { border: 1px solid #ccc; }
        }

        @media (max-width: 600px) {
            .stats-row { grid-template-columns: 1fr; }
            .timeline { flex-direction: column; gap: 8px; }
            .tl-line { width: 2px; height: 20px; }
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="page-header">
        <div>
            <div class="page-title">🐾 My Inquiries</div>
            <div class="page-subtitle">Welcome, <strong><?php echo htmlspecialchars($user['username']); ?></strong> — track your adoption requests here</div>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-btn nav-btn-outline"><i class="fa fa-home"></i> Home</a>
            <a href="inquire.php" class="nav-btn nav-btn-primary"><i class="fa fa-plus"></i> New Inquiry</a>
        </div>
    </div>

    <!-- Cancel Toast -->
    <?php if (isset($_GET['cancelled'])): ?>
    <div class="toast"><i class="fa fa-check-circle"></i> Inquiry cancelled successfully.</div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num" style="color:#38bdf8;"><?php echo $total; ?></div>
            <div class="stat-label">Total Inquiries</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#f59e0b;"><?php echo $pending; ?></div>
            <div class="stat-label"> Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#10b981;"><?php echo $resolved; ?></div>
            <div class="stat-label"> Approved</div>
        </div>
    </div>


    <div class="filter-row">
        <button class="ftab active" onclick="filterCards('all', this)">All (<?php echo $total; ?>)</button>
        <button class="ftab" onclick="filterCards('pending', this)"> Pending (<?php echo $pending; ?>)</button>
        <button class="ftab" onclick="filterCards('resolved', this)"> Approved (<?php echo $resolved; ?>)</button>
    </div>

    <?php if (empty($inq_rows)): ?>
    <div class="empty-state">
        <div class="emoji">🐶</div>
        <h3 style="margin-bottom:10px;">No inquiries yet!</h3>
        <p style="color:#94a3b8; margin-bottom:20px;">Browse our breeds and send an adoption inquiry!</p>
        <a href="index.php" class="nav-btn nav-btn-primary">Browse Breeds</a>
    </div>
    <?php else: ?>

    <?php foreach($inq_rows as $inq):
        $isResolved  = ($inq['status'] === 'Resolved');
        $statusClass = $isResolved ? 'resolved' : 'pending';
        $date        = date('M d, Y h:i A', strtotime($inq['submitted_at']));
        $ago         = timeAgo($inq['submitted_at']);
    ?>
    <div class="inquiry-card <?php echo $statusClass; ?>" data-status="<?php echo $statusClass; ?>">


        <div class="card-top">
            <div>
                <div class="breed-name">🐾 <?php echo htmlspecialchars($inq['breed'] ?: 'No breed specified'); ?></div>
                <div class="time-ago"><i class="fa fa-clock"></i> <?php echo $ago; ?> &nbsp;·&nbsp; <?php echo $date; ?></div>
            </div>
            <span class="status-badge <?php echo $isResolved ? 'status-resolved' : 'status-pending'; ?>">
                <?php echo $isResolved ? ' Approved' : ' Pending Review'; ?>
            </span>
        </div>

        <div class="timeline">
            <div class="tl-step">
                <div class="tl-circle done"><i class="fa fa-check"></i></div>
                <div class="tl-label done">Submitted</div>
            </div>
            <div class="tl-line done"></div>
            <div class="tl-step">
                <div class="tl-circle <?php echo $isResolved ? 'done' : 'active'; ?>">
                    <?php echo $isResolved ? '<i class="fa fa-check"></i>' : '<i class="fa fa-search"></i>'; ?>
                </div>
                <div class="tl-label <?php echo $isResolved ? 'done' : 'active'; ?>">Under Review</div>
            </div>
            <div class="tl-line <?php echo $isResolved ? 'done' : ''; ?>"></div>
            <div class="tl-step">
                <div class="tl-circle <?php echo $isResolved ? 'done' : ''; ?>">
                    <?php echo $isResolved ? '<i class="fa fa-check"></i>' : '3'; ?>
                </div>
                <div class="tl-label <?php echo $isResolved ? 'done' : ''; ?>">Approved</div>
            </div>
        </div>

     
        <div class="inquiry-message">
            <strong style="color:#94a3b8; font-size:0.75rem; text-transform:uppercase; display:block; margin-bottom:5px;">Your Message</strong>
            <?php echo nl2br(htmlspecialchars($inq['message'] ?: 'No message provided.')); ?>
        </div>

        <?php if ($isResolved): ?>
          
            <?php if (!empty($inq['admin_response'])): ?>
            <div class="admin-response">
                <div class="admin-response-label"><i class="fa fa-comment"></i> Response from TailTalks Staff</div>
                <div class="admin-response-text"><?php echo nl2br(htmlspecialchars($inq['admin_response'])); ?></div>
            </div>
            <?php endif; ?>

            <!-- Contact Details -->
            <div class="contact-section">
                <div class="contact-label"> Next Steps — Contact Us</div>
                <div class="contact-items">
                    <div class="contact-item"><span>📍</span> Visit our shelter to meet your pet</div>
                    <div class="contact-item"><span>📧</span> tailtalks@gmail.com</div>
                    <div class="contact-item"><span>📞</span> +63 912 345 6789</div>
                    <div class="contact-item"><span>🕐</span> Mon–Sat, 9AM–5PM</div>
                </div>
            </div>

            <div class="card-actions">
                <a href="inquiry.php?breed=<?php echo urlencode($inq['breed']); ?>" class="action-btn btn-reinquire">
                    <i class="fa fa-redo"></i> Re-inquire for this Breed
                </a>
                <button class="action-btn btn-print" onclick="printCard(<?php echo $inq['id']; ?>)">
                    <i class="fa fa-print"></i> Print / Save as Proof
                </button>
            </div>

        <?php else: ?>
       
            <div class="pending-info">
                <i class="fa fa-hourglass-half"></i>
                Your inquiry is being reviewed by our staff. Check back soon for updates!
            </div>

       
            <div class="card-actions">
                <a href="?cancel_id=<?php echo $inq['id']; ?>" class="action-btn btn-cancel"
                   onclick="return confirm('Are you sure you want to cancel this inquiry?')">
                    <i class="fa fa-times-circle"></i> Cancel Inquiry
                </a>
            </div>
        <?php endif; ?>

    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div>

<script>

function filterCards(type, btn) {
    document.querySelectorAll('.ftab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.inquiry-card').forEach(card => {
        if (type === 'all') {
            card.style.display = '';
        } else {
            card.style.display = card.getAttribute('data-status') === type ? '' : 'none';
        }
    });
}

function printCard(id) {
  
    document.querySelectorAll('.inquiry-card').forEach(card => {
        card.style.display = 'none';
    });
  
    const target = document.querySelector(`.inquiry-card[data-id="${id}"]`);
    if (target) target.style.display = '';
    window.print();
 
    document.querySelectorAll('.inquiry-card').forEach(card => {
        card.style.display = '';
    });
}
</script>

</body>
</html>
