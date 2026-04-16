<?php
// Get connection settings from Railway Environment Variables
$hostname = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$database = getenv('DB_NAME');
$port     = getenv('DB_PORT');

// Connect using the specific Aiven port (15368)
$conn = mysqli_connect($hostname, $username, $password, $database, $port);

if (!$conn) {
    // This will help us see if the connection is still failing
    die("Database Connection failed: " . mysqli_connect_error());
}
?>
