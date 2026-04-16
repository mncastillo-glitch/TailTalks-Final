<?php
<?php
$conn = mysqli_connect(
    getenv('DB_HOST'), 
    getenv('DB_USER'), 
    getenv('DB_PASS'), 
    getenv('DB_NAME'), 
    getenv('DB_PORT')
);

// Connect using the specific Aiven port (15368)
$conn = mysqli_connect($hostname, $username, $password, $database, $port);

if (!$conn) {
    // This will help us see if the connection is still failing
    die("Database Connection failed: " . mysqli_connect_error());
}
?>
