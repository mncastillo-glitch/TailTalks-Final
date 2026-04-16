<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | TailTalk</title>
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="profile-page">

    <div class="profile-container">
        <div class="profile-card">
            <a href="profile.php" class="back-btn">
                <i class="fa fa-chevron-left"></i> Back to Profile
            </a>
            
            <div class="profile-header">
                <h2>Edit Profile</h2>
                <?php if($message): ?>
                    <span class="status-badge" style="margin: 10px auto;"><?php echo $message; ?></span>
                <?php endif; ?>
            </div>

            <form method="POST" class="edit-form">
                <div class="profile-details">
                    <div class="detail-group">
                        <label><i class="fa fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                    </div>
              <div class="detail-group">
    <label>
        <i class="fa fa-pencil"></i> About Me 
        <span id="char-count" style="float: right; font-size: 10px; opacity: 0.6;">0 / 150</span>
    </label>
    <textarea 
        name="bio" 
        id="bio-input" 
        rows="4" 
        maxlength="150" 
        class="glass-input" 
        placeholder="Tell us about your pets..."
    ><?php echo htmlspecialchars($userData['bio'] ?? ''); ?></textarea>
</div>
    <label><i class="fa fa-heart"></i> Favorite Category</label>
<select name="favorite_category" class="glass-input">
    <option value="Dogs & Puppies" <?php if(($userData['favorite_category'] ?? '') == 'Dogs & Puppies') echo 'selected'; ?>>Dogs & Puppies</option>
    <option value="Cats & Kittens" <?php if(($userData['favorite_category'] ?? '') == 'Cats & Kittens') echo 'selected'; ?>>Cats & Kittens</option>
    <option value="Birds" <?php if(($userData['favorite_category'] ?? '') == 'Birds') echo 'selected'; ?>>Birds</option>
</select>
                </div>

                <div class="profile-actions">
                    <button type="submit" class="edit-link" style="width: 100%; border: none; cursor: pointer;">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
<script>
    const bioInput = document.getElementById('bio-input');
    const charCount = document.getElementById('char-count');
    const maxLength = 150;

    // Function to update the count
    function updateCount() {
        const currentLength = bioInput.value.length;
        charCount.innerText = `${currentLength} / ${maxLength}`;
        
        // Turn the text red if they hit the limit
        if (currentLength >= maxLength) {
            charCount.style.color = "#ff7675";
        } else {
            charCount.style.color = "white";
        }
    }

    // Run it every time the user types
    bioInput.addEventListener('input', updateCount);

    // Run it once on page load (in case they already have a bio)
    updateCount();
</script>
</body>
</html>