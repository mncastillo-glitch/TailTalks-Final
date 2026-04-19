<?php
session_start();
include 'db.php';

// Must be logged in
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

// Get their inquiries by email
$inq_stmt = $conn->prepare("SELECT * FROM inquiries WHERE email = ? ORDER BY submitted_at DESC");
$inq_stmt->bind_param("s", $user['email']);
$inq_stmt->execute();
$inquiries = $inq_stmt->get_result();
$inq_rows = [];
while ($row = $inquiries->fetch_assoc()) { $inq_rows[] = $row; }

$total = count($inq_rows);
$resolved = count(array_filter($inq_rows, fn($r) => $r['status'] === 'Resolved'));
$pending = $total - $resolved;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inquiries | TailTalks</title>
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
            filter: blur(12px) brightness(0.3);
            transform: scale(1.1);
            z-index: 0;
        }

        .container { position: relative; z-index: 1; max-width: 900px; margin: 0 auto; }

        /* Header */
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; flex-wrap: wrap; gap: 15px;
        }
        .page-title { font-size: 1.8rem; font-weight: 800; color: #38bdf8; }
        .page-subtitle { color: #94a3b8; font-size: 0.9rem; margin-top: 4px; }
        .nav-links { display: flex; gap: 10px; }
        .nav-btn {
            padding: 10px 18px; border-radius: 10px; text-decoration: none;
            font-weight: 600; font-size: 0.85rem; transition: 0.2s;
        }
        .nav-btn-outline { border: 1px solid #475569; color: #94a3b8; }
        .nav-btn-outline:hover { border-color: #38bdf8; color: #38bdf8; }
        .nav-btn-primary { background: #38bdf8; color: #0f172a; }

        /* Stats */
        .stats-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 25px; }
        .stat-card {
            background: rgba(30,41,59,0.9);
            border: 1px solid #334155;
            border-radius: 14px; padding: 20px; text-align: center;
        }
        .stat-card .stat-num { font-size: 2rem; font-weight: 800; }
        .stat-card .stat-label { font-size: 0.8rem; color: #94a3b8; margin-top: 4px; }

        /* Inquiry Cards */
        .inquiry-card {
            background: rgba(30,41,59,0.95);
            border: 1px solid #334155;
            border-radius: 16px; padding: 24px;
            margin-bottom: 16px; position: relative;
            transition: transform 0.2s;
        }
        .inquiry-card:hover { transform: translateY(-2px); }

        .inquiry-card.pending { border-left: 4px solid #f59e0b; }
        .inquiry-card.resolved { border-left: 4px solid #10b981; }

        .card-top { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; }
        .breed-name { font-size: 1.1rem; font-weight: 700; color: #f8fafc; }
        .inquiry-date { font-size: 0.8rem; color: #64748b; }

        .status-badge {
            padding: 5px 14px; border-radius: 20px;
            font-size: 0.8rem; font-weight: 700;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .status-pending { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
        .status-resolved { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }

        .inquiry-message {
            margin-top: 12px; padding: 12px;
            background: rgba(15,23,42,0.6);
            border-radius: 8px; font-size: 0.9rem;
            color: #cbd5e1; line-height: 1.6;
        }

        /* Admin Response Box */
        .admin-response {
            margin-top: 15px;
            background: rgba(16,185,129,0.08);
            border: 1px solid rgba(16,185,129,0.25);
            border-radius: 12px; padding: 16px;
        }
        .admin-response-label {
            font-size: 0.75rem; color: #10b981;
            text-transform: uppercase; font-weight: 700;
            margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
        }
        .admin-response-text {
            font-size: 0.9rem; color: #f8fafc; line-height: 1.6;
        }

        /* Contact Section (shows when resolved) */
        .contact-section {
            margin-top: 15px;
            background: rgba(56,189,248,0.08);
            border: 1px solid rgba(56,189,248,0.25);
            border-radius: 12px; padding: 16px;
        }
        .contact-label {
            font-size: 0.75rem; color: #38bdf8;
            text-transform: uppercase; font-weight: 700;
            margin-bottom: 12px;
        }
        .contact-items { display: flex; gap: 20px; flex-wrap: wrap; }
        .contact-item { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #cbd5e1; }
        .contact-icon { font-size: 1.1rem; }

        /* Pending Info Box */
        .pending-info {
            margin-top: 15px;
            background: rgba(245,158,11,0.08);
            border: 1px solid rgba(245,158,11,0.2);
            border-radius: 10px; padding: 14px;
            font-size: 0.85rem; color: #fcd34d;
            display: flex; align-items: center; gap: 10px;
        }

        /* Empty State */
        .empty-state {
            text-align: center; padding: 60px 20px;
            background: rgba(30,41,59,0.9);
            border: 1px solid #334155; border-radius: 16px;
        }
        .empty-state .emoji { font-size: 3rem; margin-bottom: 15px; }
        .empty-state h3 { color: #f8fafc; margin-bottom: 10px; }
        .empty-state p { color: #94a3b8; margin-bottom: 20px; }

        @media (max-width: 600px) {
            .stats-row { grid-template-columns: 1fr; }
            .page-header { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="page-header">
        <div>
            <div class="page-title">🐾 My Inquiries</div>
            <div class="page-subtitle">Welcome back, <strong><?php echo htmlspecialchars($user['username']); ?></strong> — track your adoption requests here</div>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-btn nav-btn-outline">← Home</a>
            <a href="inquire.php" class="nav-btn nav-btn-primary">+ New Inquiry</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num" style="color:#38bdf8;"><?php echo $total; ?></div>
            <div class="stat-label">Total Inquiries</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#f59e0b;"><?php echo $pending; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#10b981;"><?php echo $resolved; ?></div>
            <div class="stat-label">Approved</div>
        </div>
    </div>

    <!-- Inquiry List -->
    <?php if (empty($inq_rows)): ?>
    <div class="empty-state">
        <div class="emoji">🐶</div>
        <h3>No inquiries yet!</h3>
        <p>You haven't submitted any adoption inquiries. Find a breed you love and send one!</p>
        <a href="index.php" class="nav-btn nav-btn-primary">Browse Breeds</a>
    </div>
    <?php else: ?>

    <?php foreach($inq_rows as $inq):
        $isResolved = ($inq['status'] === 'Resolved');
        $statusClass = $isResolved ? 'resolved' : 'pending';
        $date = date('M d, Y h:i A', strtotime($inq['submitted_at']));
    ?>
    <div class="inquiry-card <?php echo $statusClass; ?>">
        <div class="card-top">
            <div>
                <div class="breed-name">🐾 <?php echo htmlspecialchars($inq['breed']); ?></div>
                <div class="inquiry-date">Submitted on <?php echo $date; ?></div>
            </div>
            <span class="status-badge <?php echo $isResolved ? 'status-resolved' : 'status-pending'; ?>">
                <?php echo $isResolved ? '✅ Approved' : '⏳ Pending Review'; ?>
            </span>
        </div>

        <!-- User's Message -->
        <div class="inquiry-message">
            <strong style="color:#94a3b8; font-size:0.75rem; text-transform:uppercase;">Your Message</strong><br>
            <?php echo nl2br(htmlspecialchars($inq['message'])); ?>
        </div>

        <?php if ($isResolved && !empty($inq['admin_response'])): ?>
        <!-- Admin Response -->
        <div class="admin-response">
            <div class="admin-response-label">💬 Response from TailTalks Staff</div>
            <div class="admin-response-text"><?php echo nl2br(htmlspecialchars($inq['admin_response'])); ?></div>
        </div>

        <!-- Contact Details (shown when approved) -->
        <div class="contact-section">
            <div class="contact-label">📋 Next Steps — Contact Us</div>
            <div class="contact-items">
                <div class="contact-item"><span class="contact-icon">📍</span> Visit our shelter to meet your pet</div>
                <div class="contact-item"><span class="contact-icon">📧</span> tailtalks@gmail.com</div>
                <div class="contact-item"><span class="contact-icon">📞</span> +63 912 345 6789</div>
            </div>
        </div>

        <?php elseif ($isResolved): ?>
        <div class="admin-response">
            <div class="admin-response-label">✅ Status</div>
            <div class="admin-response-text">Your inquiry has been approved! Please contact us to schedule your visit.</div>
        </div>
        <div class="contact-section">
            <div class="contact-label">📋 Next Steps — Contact Us</div>
            <div class="contact-items">
                <div class="contact-item"><span class="contact-icon">📍</span> Visit our shelter to meet your pet</div>
                <div class="contact-item"><span class="contact-icon">📧</span> tailtalks@gmail.com</div>
                <div class="contact-item"><span class="contact-icon">📞</span> +63 912 345 6789</div>
            </div>
        </div>

        <?php else: ?>
        <!-- Pending Message -->
        <div class="pending-info">
            ⏳ Your inquiry is currently being reviewed by our staff. We'll update your status soon — please check back here!
        </div>
        <?php endif; ?>

    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div>
</body>
</html>
