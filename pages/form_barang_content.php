<main class="content">
  <header class="topbar">
    <div>
      <p class="eyebrow mb-1">Form CRUD</p>
      <h1 class="page-title">Tambah atau Edit Barang</h1>
    </div>
    <a href="dashboard.php?page=barang" class="btn btn-outline-secondary">Kembali</a>
  </header>

  <section class="panel">
    <form action="dashboard.php?page=barang" method="POST" class="row g-4">
      <div class="col-md-4">
        <label class="form-label">Kode Barang</label>
        <input type="text" name="kode_barang" class="form-control" placeholder="LAB-001" required>
      </div>

      <div class="col-md-8">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama_barang" class="form-control" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <select name="kategori_id" class="form-select" required>
          <option value="">Pilih kategori</option>
          <option value="1">Alat Praktikum</option>
          <option value="2">Peralatan Kaca</option>
          <option value="3">Elektronik</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Lokasi</label>
        <select name="lokasi_id" class="form-select" required>
          <option value="">Pilih lokasi</option>
          <option value="1">Lab Biologi</option>
          <option value="2">Lab Fisika</option>
          <option value="3">Lab Kimia</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Kondisi</label>
        <select name="kondisi" class="form-select" required>
          <option value="">Pilih kondisi</option>
          <option>Baik</option>
          <option>Rusak Ringan</option>
          <option>Rusak Berat</option>
          <option>Hilang</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Jumlah Stok</label>
        <input type="number" name="jumlah" class="form-control" min="0" required>
        <div class="form-text">Stok tidak boleh kurang dari 0.</div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Stok Minimum</label>
        <input type="number" name="stok_minimum" class="form-control" min="0" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Tanggal Masuk</label>
        <input type="date" name="tanggal_masuk" class="form-control" required>
      </div>

      <div class="col-12">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" rows="4" class="form-control"></textarea>
      </div>

      <div class="col-12 d-flex justify-content-end gap-2">
        <a href="dashboard.php?page=barang" class="btn btn-light">Batal</a>
        <button type="reset" class="btn btn-outline-secondary">Reset</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </section>
</main>
