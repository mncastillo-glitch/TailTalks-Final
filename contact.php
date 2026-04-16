<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TailTalks | Contact Us</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --tt-blue: #5dade2;
            --tt-dark-blue: #0b1522;
            --tt-glass: rgba(255, 255, 255, 0.05);
        }

        body {
            /* Lively Hero Background: Replace the URL with your actual hero image path */
            background: linear-gradient(rgba(11, 21, 34, 0.7), rgba(11, 21, 34, 0.8)), 
                        url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            color: white;
            font-family: 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            display: grid;
            grid-template-columns: 1.2fr 1.1fr;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.6);
        }

        /* LEFT SIDE: INFO */
        .info-side {
            padding: 60px;
            background: rgba(11, 21, 34, 0.4);
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--tt-blue);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .back-home:hover { color: white; transform: translateX(-5px); }

        .info-side h2 { font-size: 2.2rem; margin-bottom: 40px; line-height: 1.2; }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .info-item i { font-size: 1.5rem; color: var(--tt-blue); margin-bottom: 10px; }
        .info-item h4 { margin: 0; color: var(--tt-blue); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }
        .info-item p { margin: 5px 0 0; opacity: 0.9; font-size: 1rem; line-height: 1.4; }

        .social-row { display: flex; gap: 15px; margin-top: 15px; }
        .social-row a { color: white; font-size: 1.3rem; transition: 0.3s; }
        .social-row a:hover { color: var(--tt-blue); transform: scale(1.2); }

        /* RIGHT SIDE: FORM */
        .form-side { padding: 60px; background: rgba(255, 255, 255, 0.02); }
        .form-side h2 { text-align: center; margin-bottom: 40px; font-size: 1.8rem; }

        .contact-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full { grid-column: span 2; }

        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        .contact-form input:focus, .contact-form textarea:focus {
            border-color: var(--tt-blue);
            background: rgba(255, 255, 255, 0.15);
        }

        .submit-btn {
            grid-column: span 2;
            background: var(--tt-blue);
            color: var(--tt-dark-blue);
            border: none;
            padding: 18px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(93, 173, 226, 0.4);
        }

        @media (max-width: 900px) {
            .container { grid-template-columns: 1fr; margin: 20px; }
            .info-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="info-side">
        <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Homepage</a>
        <h2>Get In Touch With Us Now!</h2>
        
        <div class="info-grid">
            <div class="info-item">
                <i class="fas fa-phone-alt"></i>
                <h4>Phone Number</h4>
                <p>+63 912 345 6789</p>
            </div>
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <h4>Working Hours</h4>
                <p>Mon - Sat<br>9:00 AM - 6:00 PM</p>
            </div>
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <h4>Email Address</h4>
                <p>support@tailtalks.com</p>
            </div>
            <div class="info-item">
                <i class="fas fa-share-alt"></i>
                <h4>Social Media</h4>
                <div class="social-row">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-side">
        <h2>Send Inquiry</h2>
        <form class="contact-form" action="submit_inquiry.php" method="POST">
            <input type="text" name="name" placeholder="Full Name *" required class="full">
            <input type="email" name="email" placeholder="Email Address *" required class="full">
            <input type="text" name="mobile" placeholder="Mobile Number">
            <input type="text" name="breed" placeholder="Breed of Interest">
            <textarea name="message" rows="4" placeholder="Your Message..." class="full"></textarea>
            <button type="submit" class="submit-btn">
                SUBMIT INQUIRY <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<?php include('chatbot.php'); ?>

</body>
</html>