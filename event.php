<?php
include 'koneksi.php';

// Ambil events dari database
$events_query = mysqli_query($connection, "SELECT * FROM events WHERE status='aktif' ORDER BY tanggal_mulai DESC");
?>

<!-- ==========================================================
    Nama File   : event.php
    Deskripsi   : Halaman melihat banyak event untuk mahasiswa di Web Informasi Event Kampus Polibatam.
    Dibuat oleh : Aldi Ernando Firmansyah
    Tanggal     : 12 Oktober - 14 Oktober 2025
=========================================================== -->

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Event Kampus Polibatam</title>

  <!-- Bootstrap -->
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8fafc;
      padding-top: 80px;
    }

    .card {
      transition: transform 0.2s;
      cursor: pointer;
    }

    .card:hover {
      transform: scale(1.03);
    }

    .navbar-brand img {
      width: 180px;
      height: auto;
    }
    
    /* Perbaikan untuk gambar card */
    .card-img-top {
      height: 200px;
      object-fit: cover;
      width: 100%;
    }
    
    /* Modal styling sesuai gambar */
    .modal-detail-item {
      margin-bottom: 15px;
    }
    
    .modal-detail-label {
      font-weight: bold;
      color: #333;
      margin-bottom: 5px;
    }
    
    .modal-detail-value {
      color: #666;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }

    .status-badge {
      font-size: 0.8em;
      padding: 4px 8px;
    }
  </style>
</head>

<body>

  <!-- =======================================================
       NAVBAR
       ======================================================= -->
  <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="logoo.png" alt="Logo Polibatam">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarNav" aria-controls="navbarNav"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="lading_page.php">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="event.php">Event</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="kalender.php">Kalender</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>


  <!-- =======================================================
       KONTEN EVENT
       ======================================================= -->
  <div class="container py-5">
    <h2 class="text-center mb-5 fw-bold">Informasi Event Kampus</h2>

    <!-- FILTER -->
    <div class="row mb-4 g-3">
      <div class="col-md-4">
        <input type="text" id="filterKataKunci" class="form-control shadow-sm" placeholder="Cari event...">
      </div>

      <div class="col-md-4">
        <select id="filterKategori" class="form-select shadow-sm">
          <option value="">Semua Kategori</option>
          <option value="akademik">Akademik</option>
          <option value="non akademik">Non Akademik</option>
        </select>
      </div>

      <div class="col-md-4">
        <input type="date" id="filterTanggal" class="form-control shadow-sm">
      </div>
    </div>

    <!-- DAFTAR EVENT -->
    <div class="row" id="eventList">
      <?php if(mysqli_num_rows($events_query) > 0): ?>
        <?php while($event = mysqli_fetch_assoc($events_query)): ?>
          <div class="col-md-4 mb-4 event" 
               data-judul="<?= strtolower(htmlspecialchars($event['nama_event'])) ?>" 
               data-tanggal="<?= $event['tanggal_mulai'] ?>"
               data-kategori="<?= !empty($event['kategori']) ? strtolower(htmlspecialchars($event['kategori'])) : '' ?>">
            
            <div class="card h-100 shadow-sm">
              <?php if(!empty($event['gambar'])): ?>
                <img src="<?= $event['gambar'] ?>" class="card-img-top" alt="<?= htmlspecialchars($event['nama_event']) ?>">
              <?php else: ?>
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                  <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                </div>
              <?php endif; ?>
              
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($event['nama_event']) ?></h5>
                
                <!-- Tampilkan Kategori -->
                <?php if(!empty($event['kategori'])): ?>
                  <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($event['kategori']) ?></span>
                <?php endif; ?>
                
                <p class="text-muted mb-1">
                  <i class="bi bi-geo-alt"></i> 
                  <?= htmlspecialchars($event['lokasi']) ?>
                </p>
                <p class="text-muted">
                  <i class="bi bi-calendar"></i> 
                  <?= date('d M Y', strtotime($event['tanggal_mulai'])) ?>
                </p>
                
                <span class="badge bg-success status-badge">
                  Aktif
                </span>
                
                <button class="btn btn-outline-primary w-100 mt-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modal<?= $event['id'] ?>">
                  Lihat Detail
                </button>
              </div>
            </div>
          </div>

          <!-- MODAL FOR THIS EVENT -->
          <div class="modal fade" id="modal<?= $event['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $event['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title fw-bold" id="modalLabel<?= $event['id'] ?>">
                    <?= htmlspecialchars($event['nama_event']) ?>
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <!-- GAMBAR DIHAPUS DARI MODAL -->
                  
                  <div class="modal-detail-item">
                    <div class="modal-detail-label">Kategori</div>
                    <div class="modal-detail-value">
                      <?php if(!empty($event['kategori'])): ?>
                        <span class="badge bg-info"><?= htmlspecialchars($event['kategori']) ?></span>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <div class="modal-detail-item">
                    <div class="modal-detail-label">Lokasi</div>
                    <div class="modal-detail-value"><?= htmlspecialchars($event['lokasi']) ?></div>
                  </div>
                  
                  <div class="modal-detail-item">
                    <div class="modal-detail-label">Tanggal Mulai</div>
                    <div class="modal-detail-value"><?= date('d M Y', strtotime($event['tanggal_mulai'])) ?></div>
                  </div>
                  
                  <div class="modal-detail-item">
                    <div class="modal-detail-label">Tanggal Selesai</div>
                    <div class="modal-detail-value"><?= date('d M Y', strtotime($event['tanggal_selesai'])) ?></div>
                  </div>
                  
                  <div class="modal-detail-item">
                    <div class="modal-detail-label">Status</div>
                    <div class="modal-detail-value">
                      <span class="badge bg-success">Aktif</span>
                    </div>
                  </div>
                  
                  <div class="modal-detail-item">
                    <div class="modal-detail-label">Deskripsi</div>
                    <div class="modal-detail-value"><?= nl2br(htmlspecialchars($event['deskripsi'])) ?></div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Belum ada event yang tersedia.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- =======================================================
       FOOTER
       ======================================================= -->
  <footer class="bg-light text-center py-3">
    <p class="mb-0">© Informasi Event Kampus Polibatam 2025</p>
  </footer>

  <!-- =======================================================
       SCRIPT JS
       ======================================================= -->
  <script src="bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- =======================================================
       FILTER EVENT
       ======================================================= -->
  <script>
    const kataKunciFilter = document.getElementById('filterKataKunci');
    const kategoriFilter = document.getElementById('filterKategori');
    const tanggalFilter = document.getElementById('filterTanggal');

    function filterEvents() {
      const kataKunciValue = kataKunciFilter.value.toLowerCase();
      const kategoriValue = kategoriFilter.value.toLowerCase();
      const tanggalValue = tanggalFilter.value;
      const eventList = document.querySelectorAll('.event');

      eventList.forEach(event => {
        const judul = event.getAttribute('data-judul');
        const kategori = event.getAttribute('data-kategori');
        const tanggal = event.getAttribute('data-tanggal');

        const matchKataKunci = !kataKunciValue || judul.includes(kataKunciValue);
        const matchKategori = !kategoriValue || kategori === kategoriValue;
        const matchTanggal = !tanggalValue || tanggal === tanggalValue;

        event.style.display = (matchKataKunci && matchKategori && matchTanggal) ? 'block' : 'none';
      });
    }

    kataKunciFilter.addEventListener('input', filterEvents);
    kategoriFilter.addEventListener('change', filterEvents);
    tanggalFilter.addEventListener('change', filterEvents);
  </script>

</body>
</html>
