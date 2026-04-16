<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TailTalks | Adoption Inquiry</title>
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('images/bbnner.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .inquiry-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 40px;
            padding: 50px;
            width: 90%;
            max-width: 550px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
            text-align: center;
        }

        .inquiry-card h2 {
            color: #5dade2;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .inquiry-card p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #5dade2;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px;
            color: white;
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: #5dade2;
            background: rgba(255, 255, 255, 0.1);
        }

        .submit-btn {
            background: #5dade2;
            color: white;
            border: none;
            width: 100%;
            padding: 18px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.4s;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(93, 173, 226, 0.3);
        }

        .submit-btn:hover {
            background: #3498db;
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(93, 173, 226, 0.5);
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .back-link:hover { color: #5dade2; }
    </style>
</head>
<body>

    <div class="inquiry-card">
        <h2>Adoption Inquiry</h2>
        <p>Start your journey to finding a new family member.</p>

        <form action="submit_inquiry.php" method="POST">
            <div class="form-group">
                <label><i class="fa fa-user"></i> Full Name</label>
                <input type="text" name="name" placeholder="Enter your name" required>
            </div>

            <div class="form-group">
                <label><i class="fa fa-envelope"></i> Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label><i class="fa fa-paw"></i> Breed of Interest</label>
                <input type="text" name="breed" placeholder="e.g. Golden Retriever" required>
            </div>

            <div class="form-group">
                <label><i class="fa fa-comment-dots"></i> Why do you want to adopt?</label>
                <textarea name="message" rows="4" placeholder="Tell us about your home environment..."></textarea>
            </div>

            <button type="submit" class="submit-btn">Send Application</button>
        </form>

        <a href="index.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Encyclopedia</a>
    </div>

</body>
</html>