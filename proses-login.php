<?php
session_start();
require_once 'koneksi.php';

// Memastikan data dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitasi input untuk mencegah SQL Injection sederhana
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = trim($_POST['password']);

    // Cek username di database
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Memeriksa status akun (harus aktif)
        if ($user['status'] !== 'aktif') {
            echo "<script>
                alert('Akun Anda tidak aktif. Silakan hubungi Administrator.');
                window.location.href = 'index.php';
            </script>";
            exit;
        }

        // Verifikasi password dengan beberapa metode (password_verify, md5, plaintext)
        // untuk memastikan kompabilitas dengan berbagai jenis data di database
        $password_match = false;
        if (password_verify($password, $user['password'])) {
            $password_match = true;
        } elseif (md5($password) === $user['password']) {
            $password_match = true;
        } elseif ($password === $user['password']) {
            $password_match = true;
        }

        if ($password_match) {
            // Set session user
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = $user['level'];

            // Tampilkan alert berhasil dan redirect ke dashboard
            echo "<script>
                alert('Berhasil Login! Selamat datang, " . addslashes($user['nama_lengkap']) . "');
                window.location.href = 'dashboard.php?page=dashboard';
            </script>";
            exit;
        }
    }

    // Jika username tidak ditemukan atau password salah
    echo "<script>
        alert('Username atau Password salah!');
        window.location.href = 'index.php';
    </script>";
    exit;
} else {
    // Jika diakses tanpa POST, kembalikan ke index.php
    header("Location: index.php");
    exit;
}
?>
