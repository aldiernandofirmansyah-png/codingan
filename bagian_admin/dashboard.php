<?php
// NAMA FILE: dashboard.php
// DESKRIPSI: Halaman dashboard admin untuk mengelola event kampus
// DIBUAT OLEH: [Nama Kamu] - NIM: [NIM Kamu]
// TANGGAL: [Tanggal Pembuatan]

session_start();
require_once 'koneksi.php';

// Cek login admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: landing_page.php');
    exit();
}

// Generate CSRF token untuk keamanan
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Ambil statistik event
$totalEventQuery = mysqli_query($connection, "SELECT COUNT(*) AS total FROM events");
$totalEvent = mysqli_fetch_assoc($totalEventQuery)['total'];

$eventAktifQuery = mysqli_query($connection, "SELECT COUNT(*) AS aktif FROM events WHERE status = 'aktif'");
$eventAktif = mysqli_fetch_assoc($eventAktifQuery)['aktif'];

$eventDraftQuery = mysqli_query($connection, "SELECT COUNT(*) AS draft FROM events WHERE status = 'draft'");
$eventDraft = mysqli_fetch_assoc($eventDraftQuery)['draft'];

// Ambil semua event untuk ditampilkan
$eventsQuery = mysqli_query($connection, "SELECT * FROM events ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - Informasi Event Kampus</title>
    
    <!-- External Stylesheets -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR NAVIGATION -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar text-white min-vh-100">
                <div class="text-center py-3 border-bottom">
                    <img src="logoo.png" alt="Logo Polibatam" width="160" class="img-fluid mb-2" />
                </div>
                
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link text-white active" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="btn btn-outline-light w-75 ms-3" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- MAIN CONTENT AREA -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Session Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-dark">Dashboard Admin Kampus</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahEventModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Event
                    </button>
                </div>

                <!-- STATISTICS CARDS -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-primary text-center">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Total Event</h6>
                                <h3 class="fw-bold text-primary"><?= htmlspecialchars($totalEvent) ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-success text-center">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Event Aktif</h6>
                                <h3 class="fw-bold text-success"><?= htmlspecialchars($eventAktif) ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-warning text-center">
                            <div class="card-body">
                                <h6 class="text-muted mb-2">Event Draft</h6>
                                <h3 class="fw-bold text-warning"><?= htmlspecialchars($eventDraft) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EVENTS TABLE -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Daftar Event Kampus</h5>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Gambar</th>
                                        <th>Nama Event</th>
                                        <th>Kategori</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <?php if (mysqli_num_rows($eventsQuery) > 0): ?>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($eventsQuery)): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <?php if (!empty($row['gambar'])): ?>
                                                        <img src="<?= htmlspecialchars($row['gambar']) ?>" 
                                                             alt="<?= htmlspecialchars($row['nama_event']) ?>" 
                                                             class="img-fluid rounded" 
                                                             width="80" 
                                                             style="object-fit: cover; height: 60px;" />
                                                    <?php else: ?>
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                             style="width: 80px; height: 60px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <td><?= htmlspecialchars($row['nama_event']) ?></td>
                                                
                                                <td>
                                                    <?php if (!empty($row['kategori'])): ?>
                                                        <span class="badge bg-info">
                                                            <?= htmlspecialchars($row['kategori']) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                
                                                <td><?= date('d M Y', strtotime($row['tanggal_mulai'])) ?></td>
                                                <td><?= date('d M Y', strtotime($row['tanggal_selesai'])) ?></td>
                                                <td><?= htmlspecialchars($row['lokasi']) ?></td>
                                                
                                                <td>
                                                    <span class="badge bg-<?= $row['status'] == 'aktif' ? 'success' : 'warning' ?>">
                                                        <?= $row['status'] == 'aktif' ? 'Aktif' : 'Draft' ?>
                                                    </span>
                                                </td>
                                                
                                                <td>
                                                    <button class="btn btn-sm btn-warning me-1" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editEventModal" 
                                                            onclick="setEditData(
                                                                <?= $row['id'] ?>, 
                                                                '<?= htmlspecialchars($row['nama_event'], ENT_QUOTES) ?>', 
                                                                '<?= htmlspecialchars($row['lokasi'], ENT_QUOTES) ?>', 
                                                                '<?= $row['status'] ?>', 
                                                                '<?= $row['tanggal_mulai'] ?>', 
                                                                '<?= $row['tanggal_selesai'] ?>', 
                                                                `<?= addslashes($row['deskripsi']) ?>`, 
                                                                '<?= $row['kategori'] ?>'
                                                            )">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
                                                    
                                                    <a href="hapus_event.php?id=<?= $row['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Yakin hapus event <?= htmlspecialchars(addslashes($row['nama_event'])) ?>?')">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                Belum ada event yang ditambahkan.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="text-center mt-4 text-muted small">
                    <hr />
                    <p class="mb-0">© 2025 Informasi Event Kampus Polibatam</p>
                </footer>
            </main>
        </div>
    </div>

    <!-- MODAL TAMBAH EVENT -->
    <div class="modal fade" 
         id="tambahEventModal" 
         tabindex="-1" 
         aria-labelledby="tambahEventModalLabel" 
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="tambahEventModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Event Baru
                    </h5>
                    <button type="button" 
                            class="btn-close btn-close-white" 
                            data-bs-dismiss="modal" 
                            aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <form method="POST" 
                          action="tambah_event.php" 
                          enctype="multipart/form-data" 
                          id="formTambahEvent">
                        <input type="hidden" 
                               name="csrf_token" 
                               value="<?= $_SESSION['csrf_token'] ?>" />
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_event" class="form-label fw-bold">
                                        Nama Event <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama_event" 
                                           name="nama_event" 
                                           required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="kategori" class="form-label fw-bold">
                                        Kategori <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" 
                                            id="kategori" 
                                            name="kategori" 
                                            required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Akademik">Akademik</option>
                                        <option value="Non Akademik">Non Akademik</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label fw-bold">
                                        Lokasi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="lokasi" 
                                           name="lokasi" 
                                           required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label fw-bold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="draft">Draft</option>
                                        <option value="aktif">Aktif</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label fw-bold">
                                        Gambar Event
                                    </label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="gambar" 
                                           name="gambar" 
                                           accept="image/*" />
                                    <div class="form-text">
                                        Format: JPG, PNG, GIF. Maksimal 2MB
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tanggal_mulai" class="form-label fw-bold">
                                        Tanggal Mulai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="tanggal_mulai" 
                                           name="tanggal_mulai" 
                                           required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tanggal_selesai" class="form-label fw-bold">
                                        Tanggal Selesai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="tanggal_selesai" 
                                           name="tanggal_selesai" 
                                           required />
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-bold">
                                Deskripsi Event <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="deskripsi" 
                                      name="deskripsi" 
                                      rows="4" 
                                      placeholder="Deskripsikan event secara detail..." 
                                      required></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" 
                                    class="btn btn-secondary me-md-2" 
                                    data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT EVENT -->
    <div class="modal fade" 
         id="editEventModal" 
         tabindex="-1" 
         aria-labelledby="editEventModalLabel" 
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bold" id="editEventModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Event
                    </h5>
                    <button type="button" 
                            class="btn-close btn-close-white" 
                            data-bs-dismiss="modal" 
                            aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <form method="POST" 
                          action="edit_event.php" 
                          enctype="multipart/form-data" 
                          id="formEditEvent">
                        <input type="hidden" 
                               id="edit_id" 
                               name="id" />
                        <input type="hidden" 
                               name="csrf_token" 
                               value="<?= $_SESSION['csrf_token'] ?>" />
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nama_event" class="form-label fw-bold">
                                        Nama Event <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="edit_nama_event" 
                                           name="nama_event" 
                                           required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_kategori" class="form-label fw-bold">
                                        Kategori <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" 
                                            id="edit_kategori" 
                                            name="kategori" 
                                            required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Akademik">Akademik</option>
                                        <option value="Non Akademik">Non Akademik</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_lokasi" class="form-label fw-bold">
                                        Lokasi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="edit_lokasi" 
                                           name="lokasi" 
                                           required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label fw-bold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" 
                                            id="edit_status" 
                                            name="status" 
                                            required>
                                        <option value="draft">Draft</option>
                                        <option value="aktif">Aktif</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_gambar" class="form-label fw-bold">
                                        Gambar Event
                                    </label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="edit_gambar" 
                                           name="gambar" 
                                           accept="image/*" />
                                    <div class="form-text">
                                        Kosongkan jika tidak ingin mengubah gambar
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_tanggal_mulai" class="form-label fw-bold">
                                        Tanggal Mulai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="edit_tanggal_mulai" 
                                           name="tanggal_mulai" 
                                           required />
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_tanggal_selesai" class="form-label fw-bold">
                                        Tanggal Selesai <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="edit_tanggal_selesai" 
                                           name="tanggal_selesai" 
                                           required />
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label fw-bold">
                                Deskripsi Event <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="edit_deskripsi" 
                                      name="deskripsi" 
                                      rows="4" 
                                      placeholder="Deskripsikan event secara detail..." 
                                      required></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" 
                                    class="btn btn-secondary me-md-2" 
                                    data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle me-1"></i> Update Event
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- External JavaScript -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Internal JavaScript -->
    <script>
        // Function untuk set data edit ke modal
        function setEditData(id, nama, lokasi, status, tanggalMulai, tanggalSelesai, deskripsi, kategori) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama_event').value = nama;
            document.getElementById('edit_lokasi').value = lokasi;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_tanggal_mulai').value = tanggalMulai;
            document.getElementById('edit_tanggal_selesai').value = tanggalSelesai;
            document.getElementById('edit_deskripsi').value = deskripsi;
            document.getElementById('edit_kategori').value = kategori;
        }

        // Validasi tanggal
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            
            // Set min date untuk form tambah
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalSelesai = document.getElementById('tanggal_selesai');
            
            if (tanggalMulai) {
                tanggalMulai.min = today;
                tanggalMulai.addEventListener('change', function() {
                    if (tanggalSelesai) tanggalSelesai.min = this.value;
                });
            }

            // Set min date untuk form edit
            const editTanggalMulai = document.getElementById('edit_tanggal_mulai');
            const editTanggalSelesai = document.getElementById('edit_tanggal_selesai');
            
            if (editTanggalMulai) {
                editTanggalMulai.addEventListener('change', function() {
                    if (editTanggalSelesai) editTanggalSelesai.min = this.value;
                });
            }
        });
    </script>
</body>
</html>
