<?php
// NAMA FILE: process_login.php
// DESKRIPSI: File ini menangani proses autentikasi login admin dengan validasi username dan password
// DIBUAT OLEH: [Nama Kamu] - NIM: [NIM Kamu]
// TANGGAL: [Tanggal Pembuatan]

session_start();
require_once 'koneksi.php';

// Cek jika request method adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validasi input username dan password
    if (isset($_POST['username']) && isset($_POST['password'])) {
        
        // Sanitize input untuk mencegah SQL injection
        $username = mysqli_real_escape_string($connection, trim($_POST['username']));
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        
        // Query untuk memeriksa kredensial
        $query = "SELECT * FROM login WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($connection, $query);
        
        // Jika query berhasil dan data ditemukan
        if ($result && mysqli_num_rows($result) > 0) {
            
            // Set session untuk admin
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = $username;
            
            // Kirim response 'success' untuk AJAX
            echo "success";
            
        } else {
            // Jika kredensial tidak valid
            echo "error";
        }
        
        // Bebaskan memory result
        if (isset($result)) {
            mysqli_free_result($result);
        }
        
    } else {
        // Jika username atau password tidak dikirim
        echo "error";
    }
    
} else {
    // Jika bukan request POST
    echo "error";
}

// Tutup koneksi database
mysqli_close($connection);
?>
