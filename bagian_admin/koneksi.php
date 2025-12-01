<?php
// NAMA FILE: database_connection.php
// DESKRIPSI: File ini menangani koneksi ke database MySQL untuk sistem event kampus
// DIBUAT OLEH: [Nama Kamu] - NIM: [NIM Kamu]
// TANGGAL: [Tanggal Pembuatan]

// Konfigurasi koneksi database
$servername = "localhost";
$username   = "root";
$password   = ""; // Password default XAMPP/Laragon biasanya kosong
$dbname     = "event_kampus";

// Membuat koneksi
$connection = mysqli_connect($servername, $username, $password, $dbname);

// Cek koneksi
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset ke UTF-8 untuk support karakter khusus
mysqli_set_charset($connection, "utf8");

// Optional: untuk testing (jangan lupa di-comment di production)
// echo "Database connected successfully!";
?>
