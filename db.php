<?php
$conn = mysqli_init();

mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL); // enables SSL without cert verification

mysqli_real_connect(
    $conn,
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_NAME'),
    getenv('DB_PORT'),
    NULL,
    MYSQLI_CLIENT_SSL
);

if (!$conn) {
    die("Database Connection failed: " . mysqli_connect_error());
}
?>
