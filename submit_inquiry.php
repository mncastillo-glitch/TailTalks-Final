<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $breed = mysqli_real_escape_string($conn, $_POST['breed']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // SQL query to insert data
    $sql = "INSERT INTO inquiries (fullname, email, breed, message) 
            VALUES ('$name', '$email', '$breed', '$message')";

    if (mysqli_query($conn, $sql)) {
        // Success: Alert and redirect back to Home
        echo "<script>
                alert('Thank you! Your adoption inquiry for $breed has been sent.');
                window.location.href='index.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>