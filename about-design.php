<?php
// Team data with image paths
$team = [
    ['name' => 'Lim, Renz Andrei', 'role' => 'Lead Developer', 'img' => 'images/member1.jpg'],
    ['name' => 'Clacio, Denmark Lee', 'role' => 'UI Designer', 'img' => 'images/member2.jpg'],
    ['name' => 'Manalo, Amvher', 'role' => 'Database Admin', 'img' => 'images/member4.jpg'],
    ['name' => 'Castillo, Nelo', 'role' => 'Documentation', 'img' => 'images/member3.jpg']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Our Team | TailTalks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Locking the layout to prevent accidental scrolling */
        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #0f172a; /* Deep professional navy */
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
        }

        .main-container {
            width: 90vw;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 50px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Header & Intro */
        .intro-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .back-link {
            position: absolute;
            top: 40px;
            left: 40px;
            color: #94a3b8;
            text-decoration: none;
            transition: 0.3s;
        }

        .intro-section h1 {
            color: #f8fafc;
            font-size: 2.5rem;
            margin: 0 0 15px 0;
        }

        .intro-section p {
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Team Grid - Professional Row */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 50px;
        }

        .team-card {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            aspect-ratio: 4/5;
            background: #1e293b;
        }

        .team-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(20%); /* Modern professional touch */
        }

        /* The Label from your Reference Photo */
        .member-label {
            position: absolute;
            bottom: 15px;
            left: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px;
            border-radius: 12px;
            border-left: 4px solid #38bdf8;
        }

        .member-label h3 {
            margin: 0;
            color: white;
            font-size: 0.95rem;
        }

        .member-label span {
            color: #38bdf8;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Features Section */
        .features-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .feature-item i { color: #38bdf8; font-size: 1.2rem; margin-bottom: 10px; }
        .feature-item h4 { color: white; margin: 5px 0; font-size: 0.9rem; }
        .feature-item p { color: #64748b; font-size: 0.8rem; margin: 0; }
        .team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Adaptive grid */
    gap: 25px;
    width: 100%;
}

.main-container {
    margin-left: auto;
    margin-right: auto;
    /* If you have the sidebar active on this page, add: margin-left: 270px; */
}

    </style>
</head>
<body>

    <a href="index.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Home</a>

    <div class="main-container">
        <div class="intro-section">
            <h1>The Minds Behind TailTalks</h1>
            <p>We are a team of developers passionate about animal welfare, building digital tools to help pet owners make informed health decisions.</p>
        </div>

        <div class="team-grid">
            <?php foreach ($team as $member): ?>
            <div class="team-card">
                <img src="<?php echo $member['img']; ?>" alt="Team Member">
                
                <div class="member-label">
                    <span><?php echo $member['role']; ?></span>
                    <h3><?php echo $member['name']; ?></h3>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="features-row">
            <div class="feature-item">
                <i class="fa fa-code"></i>
                <h4>Our Development</h4>
                <p>Built with PHP/MySQL for speed and reliability.</p>
            </div>
            <div class="feature-item">
                <i class="fa fa-heart"></i>
                <h4>Our Mission</h4>
                <p>Bridging the gap between pet knowledge and owners.</p>
            </div>
            <div class="feature-item">
                <i class="fa fa-rocket"></i>
                <h4>Future Vision</h4>
                <p>Expanding to include interactive pet adoption maps.</p>
            </div>
        </div>
    </div>

</body>
</html>