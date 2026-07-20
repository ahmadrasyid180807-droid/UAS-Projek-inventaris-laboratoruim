<?php
session_start();

// Auth Guard: Memastikan pengguna telah login sebelum mengakses halaman ini
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo "<script>
        alert('Silakan login terlebih dahulu untuk mengakses sistem.');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// 1. Definisikan halaman default jika parameter 'page' tidak diset di URL
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// 2. Tentukan daftar halaman yang diizinkan (Whitelist) demi keamanan (mencegah Local File Inclusion)
$allowed_pages = [
    'dashboard'   => 'pages/dashboard_content.php',
    'barang'      => 'pages/barang_content.php',
    'form-barang' => 'pages/form_barang_content.php'
];

// 3. Cek apakah halaman yang diakses ada dalam daftar
if (array_key_exists($page, $allowed_pages)) {
    $content = $allowed_pages[$page];
} else {
    // Jika tidak ditemukan atau tidak diizinkan, arahkan ke halaman 404
    $content = 'pages/404.php';
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= ucwords(str_replace('-', ' ', $page)) ?> | Inventaris Laboratorium</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <a class="sidebar-brand" href="dashboard.php?page=dashboard">
        <span><i class="bi bi-box-seam"></i></span><strong>LabInv</strong>
      </a>
      <nav class="nav flex-column gap-1">
        <!-- class 'active' ditambahkan secara dinamis berdasarkan parameter $page -->
        <a class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>" href="dashboard.php?page=dashboard">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link <?= ($page == 'barang' && !isset($_GET['trigger'])) ? 'active' : '' ?>" href="dashboard.php?page=barang">
          <i class="bi bi-boxes"></i> Data Barang
        </a>
        <a class="nav-link <?= ($page == 'barang' && isset($_GET['trigger']) && $_GET['trigger'] == 'add_modal') ? 'active' : '' ?>" href="dashboard.php?page=barang&trigger=add_modal">
          <i class="bi bi-plus-circle"></i> Tambah Barang
        </a>
        <a class="nav-link" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')"><i class="bi bi-box-arrow-left"></i> Keluar</a>
      </nav>
    </aside>

    <!-- KONTEN DINAMIS -->
    <?php include $content; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
