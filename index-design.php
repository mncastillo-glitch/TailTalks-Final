<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TailTalk | Home</title>
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div id="overlay" onclick="closeNav()"></div>
    
    <nav id="mySidebar" class="sidebar">
        <div class="sidebar-header">
            <span>TailTalk 🐾</span>
            <span class="closebtn" onclick="closeNav()">&times;</span>
        </div>
        <div class="sidebar-links">
            <a href="index.php"><i class="fa fa-home"></i> Home</a>
            <a href="index.php#dogs-section" onclick="closeNav()"><i class="fa fa-dog"></i> All Breeds</a>
            <a href="about-design.php"><i class="fa fa-info-circle"></i> About Us</a>
            <a href="contact.php"><i class="fa fa-envelope"></i> Contact</a>
        </div>
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-link"><i class="fa fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <header class="sticky-header">
        <div class="navbar">
            <div class="nav-left">
                <button class="burger-btn" onclick="openNav()">
                    <i class="fa fa-bars"></i>
                </button>
                <a href="index.php" class="logo-link">
                    <div class="logo">Tail<span>Talks</span> 🐾</div>
                </a>
            </div>
            <div class="welcome-text">
                <?php if($username !== "Guest Member"): ?>
                    <a href="profile.php" class="profile-link">
                        <span>Hello,</span> <strong><?php echo htmlspecialchars($username); ?></strong> 🐾
                    </a>
                <?php else: ?>
                    <a href="login.php" class="login-link">Login / Signup</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Find Your Pawsome Match</h1>
            <p>Experience the glass-smooth way to discover your next family member.</p>
            <a href="#search-area" class="hero-cta">Get Started</a>
        </div>
        <div class="scroll-arrow">&#8595;</div>
    </section>

    <div class="search-container" id="search-area">
        <form class="search-bar" action="index.php" method="GET">
            <input type="text" name="search" placeholder="Search breeds (e.g. Husky, Cat)..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>

    <div class="container">
        <?php 
        $sections = !empty($search) ? ['Search Results' => $search_results] : [
            'Dogs' => $dogs, 
            'Cats' => $cats, 
            'Birds' => $birds, 
            'Hamsters' => $hamsters
        ];

        foreach ($sections as $title => $result):
            if ($result && mysqli_num_rows($result) > 0): ?>
                <div class="section-header" <?php if($title == 'Dogs') echo 'id="dogs-section"'; ?>>
                    <h2><?php echo $title; ?></h2>
                </div>
             <div class="animal-grid">
    <?php while ($row = mysqli_fetch_assoc($result)): 
        $breed = $row['breed_name'];
        
        // 1. Create multiple naming possibilities
        $with_hyphen = str_replace(' ', '-', $breed); // "Maine Coon" -> "Maine-Coon"
        $lower_name = strtolower($with_hyphen);       // "Maine-Coon" -> "maine-coon"
        $plain_lower = strtolower($breed);            // "Syrian" -> "syrian"

        // 2. Check each possibility in order
    if (file_exists(__DIR__ . "/images/$with_hyphen.jpg")) {
    $display_img = "images/$with_hyphen.jpg";
} elseif (file_exists(__DIR__ . "/images/$lower_name.jpg")) {
    $display_img = "images/$lower_name.jpg";
} elseif (file_exists(__DIR__ . "/images/$plain_lower.jpg")) {
    $display_img = "images/$plain_lower.jpg";
} elseif (!empty($row['image']) && file_exists(__DIR__ . "/images/" . $row['image'])) {
    $display_img = "images/" . $row['image'];
} else {
    $display_img = "images/Collage.jpg";
}
    ?>
    <div class="animal-card">
        <div class="img-box">
            <img src="<?php echo $display_img; ?>" alt="<?php echo htmlspecialchars($breed); ?>">
        </div>
        <h3><?php echo htmlspecialchars($breed); ?></h3>
        <a href="details.php?id=<?php echo $row['id']; ?>" class="info-btn">View Details</a>
    </div>
    <?php endwhile; ?>
</div>
            <?php elseif (!empty($search) && $title === 'Search Results'): ?>
                <div style="text-align:center; padding:50px; color:#666;">
                    <i class="fa fa-search-minus" style="font-size: 3rem; margin-bottom: 10px;"></i>
                    <p>No results found for "<?php echo htmlspecialchars($search); ?>"</p>
                    <a href="index.php" style="color:#5dade2;">Clear Search</a>
                </div>
            <?php endif; 
        endforeach; ?>
    </div>

    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>TailTalks 🐾</h3>
                <p>Your number one source for pet health and breed information.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php#dogs-section">Dogs</a></li>
                    <li><a href="index.php">Cats</a></li>
                    <li><a href="index.php">Birds</a></li>
                    <li><a href="index.php">Hamsters</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Connect</h4>
                <p>Email: support@tailtalks.com</p>
                <p>Location: Manila, Philippines</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 TailTalks. All Rights Reserved.
        </div>
    </footer>

    <script>
        function openNav() {
            document.getElementById("mySidebar").style.width = "300px";
            document.getElementById("overlay").style.display = "block";
            document.body.style.overflow = "hidden";
        }
        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.getElementById("overlay").style.display = "none";
            document.body.style.overflow = "auto";
        }
    </script>
</body>
</html> 
