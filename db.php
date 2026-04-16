<?php
// Securely fetching Aiven credentials from Railway's environment
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = "defaultdb"; // Your Aiven database name

// Establishing connection with the specific Aiven port
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>