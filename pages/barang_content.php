<?php
require_once 'koneksi.php';

// Fetch lists for select dropdowns
$query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori_barang ORDER BY nama_kategori ASC");
$kategori_list  = mysqli_fetch_all($query_kategori, MYSQLI_ASSOC);

$query_lokasi   = mysqli_query($koneksi, "SELECT * FROM lokasi ORDER BY nama_lokasi ASC");
$lokasi_list    = mysqli_fetch_all($query_lokasi, MYSQLI_ASSOC);

// Fetch data barang with joins
$query_barang = mysqli_query($koneksi, "
    SELECT b.*, k.nama_kategori, l.nama_lokasi, u.nama_lengkap AS pembuat
    FROM barang b
    LEFT JOIN kategori_barang k ON b.id_kategori = k.id_kategori
    LEFT JOIN lokasi l ON b.id_lokasi = l.id_lokasi
    LEFT JOIN users u ON b.created_by = u.id_user
    ORDER BY b.id_barang DESC
");
?>

<!-- DataTables CSS CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
  /* Menyesuaikan style DataTables agar selaras dengan desain premium */
  .dataTables_wrapper .dataTables_paginate .paginate_button {
      padding: 0 !important;
      margin: 0 !important;
  }
  .dataTables_wrapper .dataTables_length select,
  .dataTables_wrapper .dataTables_filter input {
      border: 1px solid var(--border-color, #e2e8f0);
      border-radius: 6px;
      padding: 0.375rem 0.75rem;
      outline: none;
  }
  .dataTables_wrapper .dataTables_filter input:focus {
      border-color: var(--primary-color, #4f46e5);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
  }
  table.dataTable {
      border-collapse: collapse !important;
      margin-top: 15px !important;
      margin-bottom: 15px !important;
  }
  table.dataTable thead th {
      background-color: #f8fafc;
      color: #475569;
      font-weight: 600;
      border-bottom: 2px solid #cbd5e1 !important;
  }
  .modal-header {
      border-bottom: 1px solid #e2e8f0;
      background-color: #f8fafc;
  }
  .modal-footer {
      border-top: 1px solid #e2e8f0;
      background-color: #f8fafc;
  }
</style>

<main class="content">
  <header class="topbar">
    <div>
      <p class="eyebrow mb-1">Manajemen Inventaris</p>
      <h1 class="page-title">Data Barang Laboratorium</h1>
    </div>
    <!-- Tombol pemicu Modal Tambah -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg me-2"></i>Tambah Barang
    </button>
  </header>

  <section class="panel">
    <div class="panel-header">
      <div>
        <h2>Daftar Barang</h2>
        <p>Gunakan pencarian dan filter kolom tabel untuk menyortir data barang.</p>
      </div>
    </div>

    <div class="table-responsive p-2">
      <table id="tabel-barang" class="table table-hover align-middle w-100">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Stok</th>
            <th>Min. Stok</th>
            <th>Kondisi</th>
            <th>Tanggal Masuk</th>
            <th>Pembuat</th>
            <th class="text-end no-sort">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($query_barang)): ?>
            <?php 
              // Kondisi badge warna
              $badge_class = 'text-bg-success';
              if ($row['kondisi'] == 'Rusak Ringan') {
                  $badge_class = 'text-bg-warning';
              } elseif ($row['kondisi'] == 'Rusak Berat') {
                  $badge_class = 'text-bg-danger';
              } elseif ($row['kondisi'] == 'Hilang') {
                  $badge_class = 'text-bg-secondary';
              }

              // Peringatan stok kritis
              $stok_class = '';
              if ($row['jumlah'] <= $row['stok_minimum']) {
                  $stok_class = 'text-danger fw-bold';
              }
            ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($row['kode_barang']) ?></td>
              <td>
                <span class="d-block fw-medium text-dark"><?= htmlspecialchars($row['nama_barang']) ?></span>
                <?php if (!empty($row['keterangan'])): ?>
                  <small class="text-muted d-block text-truncate" style="max-width: 200px;"><?= htmlspecialchars($row['keterangan']) ?></small>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['nama_kategori'] ?? 'Tidak ada') ?></td>
              <td><?= htmlspecialchars($row['nama_lokasi'] ?? 'Tidak ada') ?></td>
              <td class="<?= $stok_class ?>">
                <?= $row['jumlah'] ?>
                <?php if ($row['jumlah'] <= $row['stok_minimum']): ?>
                  <i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="Stok Kritis"></i>
                <?php endif; ?>
              </td>
              <td><?= $row['stok_minimum'] ?></td>
              <td><span class="badge <?= $badge_class ?>"><?= htmlspecialchars($row['kondisi']) ?></span></td>
              <td><?= date('d M Y', strtotime($row['tanggal_masuk'])) ?></td>
              <td><small class="text-secondary"><?= htmlspecialchars($row['pembuat'] ?? 'Sistem') ?></small></td>
              <td class="text-end">
                <div class="btn-group gap-1">
                  <!-- Tombol Ubah (Memicu Modal & Mengisi Data via JS) -->
                  <button class="btn btn-sm btn-outline-secondary btn-ubah" 
                          data-bs-toggle="modal" 
                          data-bs-target="#modalUbah"
                          data-id="<?= $row['id_barang'] ?>"
                          data-kode="<?= htmlspecialchars($row['kode_barang']) ?>"
                          data-nama="<?= htmlspecialchars($row['nama_barang']) ?>"
                          data-kategori="<?= $row['id_kategori'] ?>"
                          data-lokasi="<?= $row['id_lokasi'] ?>"
                          data-jumlah="<?= $row['jumlah'] ?>"
                          data-stok_min="<?= $row['stok_minimum'] ?>"
                          data-kondisi="<?= htmlspecialchars($row['kondisi']) ?>"
                          data-tanggal="<?= $row['tanggal_masuk'] ?>"
                          data-keterangan="<?= htmlspecialchars($row['keterangan'] ?? '') ?>"
                          title="Ubah Data">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <!-- Tombol Hapus (Memicu Modal Konfirmasi) -->
                  <button class="btn btn-sm btn-outline-danger btn-hapus"
                          data-bs-toggle="modal"
                          data-bs-target="#modalHapus"
                          data-id="<?= $row['id_barang'] ?>"
                          data-nama="<?= htmlspecialchars($row['nama_barang']) ?>"
                          title="Hapus Data">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<!-- ==================== MODAL TAMBAH BARANG ==================== -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTambahLabel"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah Barang Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="proses-barang.php" method="POST">
        <input type="hidden" name="action" value="tambah">
        <div class="modal-body row g-3">
          <div class="col-md-4">
            <label class="form-label fw-medium">Kode Barang <span class="text-danger">*</span></label>
            <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: LAB-001" required>
          </div>
          <div class="col-md-8">
            <label class="form-label fw-medium">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="nama_barang" class="form-control" placeholder="Nama lengkap alat/barang" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Kategori Barang</label>
            <select name="id_kategori" class="form-select">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($kategori_list as $kat): ?>
                <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Lokasi Penyimpanan</label>
            <select name="id_lokasi" class="form-select">
              <option value="">-- Pilih Lokasi --</option>
              <?php foreach ($lokasi_list as $lok): ?>
                <option value="<?= $lok['id_lokasi'] ?>"><?= htmlspecialchars($lok['nama_lokasi']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Jumlah Stok <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control" min="0" value="0" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Batas Minim Stok <span class="text-danger">*</span></label>
            <input type="number" name="stok_minimum" class="form-control" min="0" value="0" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Kondisi Barang <span class="text-danger">*</span></label>
            <select name="kondisi" class="form-select" required>
              <option value="Baik" selected>Baik</option>
              <option value="Rusak Ringan">Rusak Ringan</option>
              <option value="Rusak Berat">Rusak Berat</option>
              <option value="Hilang">Hilang</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_masuk" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-medium">Keterangan Tambahan</label>
            <textarea name="keterangan" rows="3" class="form-control" placeholder="Spesifikasi, serial number, atau detail lainnya..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Barang</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== MODAL UBAH BARANG ==================== -->
<div class="modal fade" id="modalUbah" tabindex="-1" aria-labelledby="modalUbahLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUbahLabel"><i class="bi bi-pencil-square text-warning me-2"></i>Ubah Data Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="proses-barang.php" method="POST">
        <input type="hidden" name="action" value="ubah">
        <input type="hidden" name="id_barang" id="edit_id_barang">
        <div class="modal-body row g-3">
          <div class="col-md-4">
            <label class="form-label fw-medium">Kode Barang <span class="text-danger">*</span></label>
            <input type="text" name="kode_barang" id="edit_kode_barang" class="form-control" required>
          </div>
          <div class="col-md-8">
            <label class="form-label fw-medium">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="nama_barang" id="edit_nama_barang" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Kategori Barang</label>
            <select name="id_kategori" id="edit_id_kategori" class="form-select">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($kategori_list as $kat): ?>
                <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Lokasi Penyimpanan</label>
            <select name="id_lokasi" id="edit_id_lokasi" class="form-select">
              <option value="">-- Pilih Lokasi --</option>
              <?php foreach ($lokasi_list as $lok): ?>
                <option value="<?= $lok['id_lokasi'] ?>"><?= htmlspecialchars($lok['nama_lokasi']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Jumlah Stok <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" id="edit_jumlah" class="form-control" min="0" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Batas Minim Stok <span class="text-danger">*</span></label>
            <input type="number" name="stok_minimum" id="edit_stok_minimum" class="form-control" min="0" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-medium">Kondisi Barang <span class="text-danger">*</span></label>
            <select name="kondisi" id="edit_kondisi" class="form-select" required>
              <option value="Baik">Baik</option>
              <option value="Rusak Ringan">Rusak Ringan</option>
              <option value="Rusak Berat">Rusak Berat</option>
              <option value="Hilang">Hilang</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-medium">Tanggal Masuk <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_masuk" id="edit_tanggal_masuk" class="form-control" required>
          </div>
          <div class="col-12">
            <label class="form-label fw-medium">Keterangan Tambahan</label>
            <textarea name="keterangan" id="edit_keterangan" rows="3" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning text-dark">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== MODAL KONFIRMASI HAPUS ==================== -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalHapusLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Konfirmasi Hapus Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="proses-barang.php" method="POST">
        <input type="hidden" name="action" value="hapus">
        <input type="hidden" name="id_barang" id="hapus_id_barang">
        <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus data barang berikut?</p>
          <div class="alert alert-warning">
            <i class="bi bi-box-seam me-2"></i><strong id="hapus_nama_barang"></strong>
          </div>
          <p class="text-danger mb-0" style="font-size: 0.875rem;"><i class="bi bi-info-circle me-1"></i> Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Pustaka jQuery dan DataTables JS CDN -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {
      // Inisialisasi DataTable
      $('#tabel-barang').DataTable({
          "order": [[0, "desc"]], // Urutkan berdasarkan kode barang default
          "columnDefs": [
              { "orderable": false, "targets": "no-sort" } // Nonaktifkan sorting pada kolom aksi
          ],
          "language": {
              "search": "Cari barang:",
              "lengthMenu": "Tampilkan _MENU_ data per halaman",
              "zeroRecords": "Tidak ada data barang ditemukan",
              "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
              "infoEmpty": "Tidak ada data tersedia",
              "infoFiltered": "(difilter dari _MAX_ total data)",
              "paginate": {
                  "first": "Pertama",
                  "last": "Terakhir",
                  "next": "Berikutnya",
                  "previous": "Sebelumnya"
              }
          }
      });

      // Deteksi parameter URL untuk otomatis membuka Modal Tambah (trigger jika dipanggil dari sidebar)
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('trigger') === 'add_modal') {
          var modalTambahObj = new bootstrap.Modal(document.getElementById('modalTambah'));
          modalTambahObj.show();
          
          // Bersihkan parameter trigger di URL agar tidak terus membuka saat refresh
          window.history.replaceState({}, document.title, "dashboard.php?page=barang");
      }

      // Controller Pengisi Data Modal Ubah (Edit)
      const modalUbah = document.getElementById('modalUbah');
      if (modalUbah) {
          modalUbah.addEventListener('show.bs.modal', function (event) {
              // Tombol yang memicu modal
              const button = event.relatedTarget;
              
              // Ambil data atribut dari tombol
              const id = button.getAttribute('data-id');
              const kode = button.getAttribute('data-kode');
              const nama = button.getAttribute('data-nama');
              const kategori = button.getAttribute('data-kategori');
              const lokasi = button.getAttribute('data-lokasi');
              const jumlah = button.getAttribute('data-jumlah');
              const stok_min = button.getAttribute('data-stok_min');
              const kondisi = button.getAttribute('data-kondisi');
              const tanggal = button.getAttribute('data-tanggal');
              const keterangan = button.getAttribute('data-keterangan');

              // Isi nilai inputan modal
              document.getElementById('edit_id_barang').value = id;
              document.getElementById('edit_kode_barang').value = kode;
              document.getElementById('edit_nama_barang').value = nama;
              document.getElementById('edit_id_kategori').value = kategori;
              document.getElementById('edit_id_lokasi').value = lokasi;
              document.getElementById('edit_jumlah').value = jumlah;
              document.getElementById('edit_stok_minimum').value = stok_min;
              document.getElementById('edit_kondisi').value = kondisi;
              document.getElementById('edit_tanggal_masuk').value = tanggal;
              document.getElementById('edit_keterangan').value = keterangan;
          });
      }

      // Controller Pengisi Data Modal Hapus (Delete)
      const modalHapus = document.getElementById('modalHapus');
      if (modalHapus) {
          modalHapus.addEventListener('show.bs.modal', function (event) {
              const button = event.relatedTarget;
              
              const id = button.getAttribute('data-id');
              const nama = button.getAttribute('data-nama');

              // Isi input form hapus
              document.getElementById('hapus_id_barang').value = id;
              document.getElementById('hapus_nama_barang').textContent = nama;
          });
      }
  });
</script>
