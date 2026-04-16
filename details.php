<?php
include('db.php');

// 1. Get the ID from the URL
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 1;

// 2. Fetch the breed from your table
$query = "SELECT * FROM breeds WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$breed = mysqli_fetch_assoc($result);

if (!$breed) {
    die("Breed not found!");
}

// 3. This line "plugs in" your HTML file below
include('details-design.php'); 
?>