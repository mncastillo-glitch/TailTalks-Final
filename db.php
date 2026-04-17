<?php
$conn = mysqli_connect(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_NAME'),
    getenv('DB_PORT')
);

// Add SSL after connecting
mysqli_ssl_set($conn, NULL, NULL, '/path/to/ca.pem', NULL, NULL);

if (!$conn) {
    die("Database Connection failed: " . mysqli_connect_error());
}
?>
