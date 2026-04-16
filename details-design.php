<?php 
// 1. DATABASE CONNECTION & SECURE DATA FETCHING
include 'db.php';

// Get ID from URL and ensure it's a number (default to 1 if not set)
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Using a Prepared Statement (Student Outcome: Secure Database Management)
$stmt = $conn->prepare("SELECT * FROM breeds WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Professional fallback array using your exact database column names
$breed = [
    'breed_name' => 'Breed Not Found',
    'animal_type' => 'Encyclopedia',
    'summary' => 'We are currently updating our database with new breed information.',
    'health_tips' => 'Detailed health and wellness data coming soon.',
    'personality' => 'Behavioral analysis is being compiled by our experts.',
    'history' => 'Historical legacy and origin data coming soon.'
];

// If the record exists, override the fallback
if ($result && $result->num_rows > 0) {
    $breed = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($breed['breed_name']); ?> | TailTalks</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --accent: #5dade2; 
            --glass: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            margin: 0;
            padding: 40px 20px;
            background: radial-gradient(circle at center, #1e293b, #000000); 
            color: white;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* THE GLASSMORPHISM CONTAINER */
        .encyclopedia-wrapper {
            width: 100%;
            max-width: 1100px;
            background: var(--glass);
            backdrop-filter: blur(25px);
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 60px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6);
            position: relative;
        }

        .back-nav {
            position: absolute;
            top: 40px;
            left: 40px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            transition: 0.3s;
        }
        .back-nav:hover { color: var(--accent); }

        .header { text-align: center; margin-bottom: 60px; }

        .category-badge {
            background: var(--accent);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-block;
        }

        h1 { font-size: 3.5rem; margin: 15px 0 0 0; }

        /* CONTENT GRID */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 30px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.02);
            border-left: 4px solid var(--accent);
            padding: 35px;
            border-radius: 24px;
            transition: transform 0.3s ease;
        }

        .info-card:hover { transform: translateY(-5px); background: rgba(255, 255, 255, 0.05); }

        .info-card label {
            color: var(--accent);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .info-card p {
            font-size: 15px;
            line-height: 1.8;
            opacity: 0.85;
            margin: 0;
        }

        .footer-action { text-align: center; margin-top: 60px; }

        .btn-adopt {
            background: var(--accent);
            color: white;
            text-decoration: none;
            padding: 18px 55px;
            border-radius: 18px;
            font-weight: 700;
            box-shadow: 0 10px 25px rgba(93, 173, 226, 0.3);
            transition: 0.3s;
        }

        .btn-adopt:hover { transform: scale(1.05); }

        @media (max-width: 768px) {
            .info-grid { grid-template-columns: 1fr; }
            .encyclopedia-wrapper { padding: 40px 25px; }
            h1 { font-size: 2.5rem; }
        }
    </style>
</head>
<body>

    <div class="encyclopedia-wrapper">
        <a href="index.php" class="back-nav"><i class="fa fa-arrow-left"></i></a>

        <div class="header">
            <span class="category-badge"><?php echo htmlspecialchars($breed['animal_type']); ?></span>
            <h1><?php echo htmlspecialchars($breed['breed_name']); ?></h1>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <label><i class="fa-solid fa-book-open"></i> Full Introduction</label>
                <p><?php echo nl2br(htmlspecialchars($breed['summary'])); ?></p>
            </div>

            <div class="info-card">
                <label><i class="fa-solid fa-stethoscope"></i> Health & Wellness</label>
                <p><?php echo nl2br(htmlspecialchars($breed['health_tips'])); ?></p>
            </div>

            <div class="info-card">
                <label><i class="fa-solid fa-brain"></i> Behavioral Analysis</label>
                <p><?php echo nl2br(htmlspecialchars($breed['personality'])); ?></p>
            </div>

            <div class="info-card">
                <label><i class="fa-solid fa-clock-rotate-left"></i> Historical Legacy</label>
                <p><?php echo nl2br(htmlspecialchars($breed['history'])); ?></p>
            </div>
        </div>

        <div class="footer-action">
          <a href="inquiry.php?breed=<?php echo urlencode($breed['breed_name']); ?>" class="btn-adopt">Inquire About Adoption</a>
        </div>
    </div>

</body>
</html>