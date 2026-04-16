<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TailTalk | My Profile</title>
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="profile-page">

    <div class="profile-container">
        <div class="profile-card">
            <a href="index.php" class="back-btn">
                <i class="fa fa-chevron-left"></i> Back to Home
            </a>
            
            <div class="profile-header">
                <div class="user-avatar">
                    <i class="fa fa-user"></i>
                </div>
                <h1><?php echo htmlspecialchars($userData['username']); ?></h1>
                <p class="member-since">Member since <?php echo date('F Y', strtotime($userData['created_at'])); ?></p>
            </div>
<div class="bio-section">
    <p>"<?php echo htmlspecialchars($userData['bio'] ?: 'No bio yet. Click edit to add one!'); ?>"</p>
</div>
        <div class="profile-details">
    <div class="detail-group">
        <label><i class="fa fa-envelope"></i> Email Address</label>
        <p><?php echo htmlspecialchars($userData['email']); ?></p>
    </div>
    
    <div class="detail-row">
        <div class="detail-group small">
            <label><i class="fa fa-shield-cat"></i> Status</label>
            <div class="status-container">
                <p>Pet Enthusiast</p>
                <span class="status-badge"><i class="fa fa-star"></i> Verified</span>
            </div>
        </div>

      <div class="detail-group small">
    <label><i class="fa fa-heart"></i> Favorite</label>
    
    <p><?php echo htmlspecialchars($userData['favorite_category'] ?? 'None'); ?></p>
</div>
    </div>
</div>
            <div class="profile-actions">
                <a href="edit-profile.php" class="edit-link">
                    <i class="fa fa-edit"></i> Edit Profile
                </a>
                <a href="logout.php" class="logout-button">Logout Account</a>
            </div>
        </div>
    </div>

</body>
</html>