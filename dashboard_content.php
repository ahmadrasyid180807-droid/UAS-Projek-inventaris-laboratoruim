<main class="content">
  <header class="topbar">
    <div>
      <p class="eyebrow mb-1">Project 1</p>
      <h1 class="page-title">Sistem Inventaris Barang Laboratorium</h1>
    </div>
    <div class="user-chip"><i class="bi bi-person-circle"></i> <?= isset($_SESSION['nama_lengkap']) ? htmlspecialchars($_SESSION['nama_lengkap']) : 'Admin Lab'; ?></div>
  </header>

  <section class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="stat-card"><p>Total Barang</p><h2>128</h2></div></div>
    <div class="col-sm-6 col-xl-3"><div class="stat-card"><p>Kondisi Baik</p><h2>104</h2></div></div>
    <div class="col-sm-6 col-xl-3"><div class="stat-card"><p>Perlu Perbaikan</p><h2>17</h2></div></div>
    <div class="col-sm-6 col-xl-3"><div class="stat-card"><p>Stok Menipis</p><h2>7</h2></div></div>
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
            <th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Lokasi</th><th>Stok</th><th>Kondisi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>LAB-001</td><td>Mikroskop Binokuler</td><td>Alat Praktikum</td>
            <td>Lab Biologi</td><td><span class="badge text-bg-danger">2</span></td>
            <td><span class="badge text-bg-success">Baik</span></td>
          </tr>
          <tr>
            <td>LAB-014</td><td>Gelas Ukur 100 ml</td><td>Peralatan Kaca</td>
            <td>Gudang A</td><td><span class="badge text-bg-warning">5</span></td>
            <td><span class="badge text-bg-success">Baik</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</main>
