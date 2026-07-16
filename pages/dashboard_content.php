<?php
require_once 'koneksi.php';

// Query for Total Barang (Sum of jumlah)
$query_total = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM barang");
$row_total = mysqli_fetch_assoc($query_total);
$total_barang = $row_total['total'] ?? 0;

// Query for Kondisi Baik (Sum of jumlah where kondisi = 'Baik')
$query_baik = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM barang WHERE kondisi = 'Baik'");
$row_baik = mysqli_fetch_assoc($query_baik);
$kondisi_baik = $row_baik['total'] ?? 0;

// Query for Perlu Perbaikan (Sum of jumlah where kondisi IN ('Rusak Ringan', 'Rusak Berat'))
$query_perbaikan = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM barang WHERE kondisi IN ('Rusak Ringan', 'Rusak Berat')");
$row_perbaikan = mysqli_fetch_assoc($query_perbaikan);
$perlu_perbaikan = $row_perbaikan['total'] ?? 0;

// Query for Stok Menipis (Count of unique items where jumlah <= stok_minimum)
$query_menipis = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE jumlah <= stok_minimum");
$row_menipis = mysqli_fetch_assoc($query_menipis);
$stok_menipis = $row_menipis['total'] ?? 0;

// Query for Barang Stok Rendah table with category and location names
$query_rendah = mysqli_query($koneksi, "
    SELECT b.*, k.nama_kategori, l.nama_lokasi
    FROM barang b
    LEFT JOIN kategori_barang k ON b.id_kategori = k.id_kategori
    LEFT JOIN lokasi l ON b.id_lokasi = l.id_lokasi
    WHERE b.jumlah <= b.stok_minimum
    ORDER BY b.jumlah ASC
");
?>
<main class="content">
  <header class="topbar">
    <div>
      <p class="eyebrow mb-1">Project 1</p>
      <h1 class="page-title">Sistem Inventaris Barang Laboratorium</h1>
    </div>
    <div class="user-chip"><i class="bi bi-person-circle"></i> <?= isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'Admin Lab'; ?></div>
  </header>

  <section class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="stat-card">
        <p>Total Barang</p>
        <h2><?= number_format($total_barang) ?></h2>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="stat-card">
        <p>Kondisi Baik</p>
        <h2><?= number_format($kondisi_baik) ?></h2>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="stat-card">
        <p>Perlu Perbaikan</p>
        <h2><?= number_format($perlu_perbaikan) ?></h2>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="stat-card">
        <p>Stok Menipis</p>
        <h2><?= number_format($stok_menipis) ?></h2>
      </div>
    </div>
  </section>

  <section class="panel">
    <div class="panel-header">
      <div>
        <h2>Barang Stok Rendah</h2>
        <p>Rekap barang yang jumlahnya mendekati batas minimum.</p>
      </div>
      <a href="dashboard.php?page=barang" class="btn btn-outline-primary btn-sm">Lihat Data</a>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Stok</th>
            <th>Kondisi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($query_rendah) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($query_rendah)): ?>
              <?php
                // Determine condition badge color
                $badge_kondisi = 'text-bg-success';
                if ($row['kondisi'] == 'Rusak Ringan') {
                    $badge_kondisi = 'text-bg-warning';
                } elseif ($row['kondisi'] == 'Rusak Berat') {
                    $badge_kondisi = 'text-bg-danger';
                } elseif ($row['kondisi'] == 'Hilang') {
                    $badge_kondisi = 'text-bg-secondary';
                }

                // Determine stock badge color (danger if <= 50% of min stock or <= 2, warning otherwise)
                $badge_stok = 'text-bg-warning';
                if ($row['jumlah'] == 0 || $row['jumlah'] <= ($row['stok_minimum'] / 2) || $row['jumlah'] <= 2) {
                    $badge_stok = 'text-bg-danger';
                }
              ?>
              <tr>
                <td class="fw-semibold"><?= htmlspecialchars($row['kode_barang']) ?></td>
                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td><?= htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada') ?></td>
                <td><?= htmlspecialchars($row['nama_lokasi'] ?? 'Tidak ada') ?></td>
                <td><span class="badge <?= $badge_stok ?>"><?= $row['jumlah'] ?></span></td>
                <td><span class="badge <?= $badge_kondisi ?>"><?= htmlspecialchars($row['kondisi']) ?></span></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-3">Tidak ada barang dengan stok rendah.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
