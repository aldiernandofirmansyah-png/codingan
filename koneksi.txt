<?php
$servername = "localhost";
$username = "root";
$password = "";  // Password default XAMPP/Laragon biasanya kosong
$dbname = "event_kampus";

// Create connection
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Optional: Set charset
mysqli_set_charset($connection, "utf8");

// echo "Database connected successfully!"; // Bisa diuncomment untuk testing
?>
