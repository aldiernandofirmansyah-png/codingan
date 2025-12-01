<?php
// NAMA FILE: tambah_event.php
// DESKRIPSI: File untuk menangani proses penambahan event baru oleh admin
// DIBUAT OLEH: [Nama Kamu] - NIM: [NIM Kamu]
// TANGGAL: [Tanggal Pembuatan]

session_start();
require_once 'koneksi.php';

// Validasi session admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: landing_page.php');
    exit();
}

// Validasi request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Metode request tidak valid!";
    header('Location: dashboard.php');
    exit();
}

// Validasi CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Token CSRF tidak valid!";
    header('Location: dashboard.php');
    exit();
}

// Ambil dan sanitasi data dari form
$namaEvent    = mysqli_real_escape_string($connection, trim($_POST['nama_event']));
$kategori     = mysqli_real_escape_string($connection, trim($_POST['kategori']));
$lokasi       = mysqli_real_escape_string($connection, trim($_POST['lokasi']));
$status       = mysqli_real_escape_string($connection, trim($_POST['status']));
$tanggalMulai = mysqli_real_escape_string($connection, trim($_POST['tanggal_mulai']));
$tanggalSelesai = mysqli_real_escape_string($connection, trim($_POST['tanggal_selesai']));
$deskripsi    = mysqli_real_escape_string($connection, trim($_POST['deskripsi']));

// Validasi input wajib
if (empty($namaEvent) || empty($kategori) || empty($lokasi) || 
    empty($tanggalMulai) || empty($tanggalSelesai) || empty($deskripsi)) {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header('Location: dashboard.php');
    exit();
}

// Validasi panjang input
if (strlen($namaEvent) > 255) {
    $_SESSION['error'] = "Nama event terlalu panjang! Maksimal 255 karakter.";
    header('Location: dashboard.php');
    exit();
}

// Validasi tanggal
if ($tanggalSelesai < $tanggalMulai) {
    $_SESSION['error'] = "Tanggal selesai tidak boleh lebih awal dari tanggal mulai!";
    header('Location: dashboard.php');
    exit();
}

// Validasi tanggal tidak boleh di masa lalu
$today = date('Y-m-d');
if ($tanggalMulai < $today) {
    $_SESSION['error'] = "Tanggal mulai tidak boleh di masa lalu!";
    header('Location: dashboard.php');
    exit();
}

// Handle upload gambar
$gambar = '';
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $fileType = mime_content_type($_FILES['gambar']['tmp_name']);
    $fileExtension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    
    // Validasi tipe file
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error'] = "Format file tidak didukung! Hanya JPG, PNG, dan GIF yang diizinkan.";
        header('Location: dashboard.php');
        exit();
    }
    
    // Validasi ekstensi file
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION['error'] = "Ekstensi file tidak valid!";
        header('Location: dashboard.php');
        exit();
    }
    
    // Validasi ukuran file (maksimal 2MB)
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($_FILES['gambar']['size'] > $maxFileSize) {
        $_SESSION['error'] = "Ukuran file terlalu besar! Maksimal 2MB.";
        header('Location: dashboard.php');
        exit();
    }
    
    // Buat folder uploads jika belum ada
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Generate nama file unik
    $filename = 'event_' . date('Ymd_His') . '_' . uniqid() . '.' . $fileExtension;
    $targetFile = $targetDir . $filename;
    
    // Pindahkan file ke folder uploads
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
        $gambar = $targetFile;
    } else {
        $_SESSION['error'] = "Gagal mengupload gambar!";
        header('Location: dashboard.php');
        exit();
    }
} elseif ($_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Jika ada error selain "tidak ada file"
    $_SESSION['error'] = "Error upload gambar: " . $_FILES['gambar']['error'];
    header('Location: dashboard.php');
    exit();
}

// Insert ke database dengan prepared statement
$query = "INSERT INTO events (nama_event, kategori, gambar, lokasi, status, tanggal_mulai, tanggal_selesai, deskripsi, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = mysqli_prepare($connection, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssssssss", 
        $namaEvent, 
        $kategori, 
        $gambar, 
        $lokasi, 
        $status, 
        $tanggalMulai, 
        $tanggalSelesai, 
        $deskripsi
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Event '{$namaEvent}' berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan event: " . mysqli_error($connection);
    }
    
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = "Error preparing statement: " . mysqli_error($connection);
}

// Regenerate CSRF token untuk keamanan
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Redirect ke dashboard
header('Location: dashboard.php');
exit();
?>
