<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: dashboard.php?page=dashboard");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Inventaris Laboratorium</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="auth-page">
  <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
    <section class="auth-card row g-0 overflow-hidden">
      <div class="col-lg-6 auth-visual p-5 d-flex flex-column justify-content-between">
        <div>
          <span class="brand-mark"><i class="bi bi-box-seam"></i></span>
          <h1 class="mt-4">Inventaris Barang Laboratorium</h1>
          <p>Kelola data barang, kondisi, lokasi, dan rekap stok laboratorium.</p>
        </div>
      </div>

      <div class="col-lg-6 bg-white p-4 p-md-5">
        <p class="eyebrow">Masuk Sistem</p>
        

        <form action="proses-login.php" method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="admin_lab" required>
          </div>

          <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>

          <button class="btn btn-primary w-100" type="submit">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
          </button>
        </form>
      </div>
    </section>
  </main>
</body>
</html>