<?php
session_start();
require_once 'koneksi.php';

// Auth Guard: Pastikan user sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'tambah') {
        // Ambil dan bersihkan data input
        $kode_barang   = mysqli_real_escape_string($koneksi, trim($_POST['kode_barang']));
        $nama_barang   = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
        $id_kategori   = !empty($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 'NULL';
        $id_lokasi     = !empty($_POST['id_lokasi']) ? intval($_POST['id_lokasi']) : 'NULL';
        $jumlah        = intval($_POST['jumlah']);
        $stok_minimum  = intval($_POST['stok_minimum']);
        $kondisi       = mysqli_real_escape_string($koneksi, $_POST['kondisi']);
        $tanggal_masuk = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk']);
        $keterangan    = mysqli_real_escape_string($koneksi, trim($_POST['keterangan']));
        $created_by    = intval($_SESSION['id_user']);

        // Validasi server-side
        if (empty($kode_barang) || empty($nama_barang) || empty($kondisi) || empty($tanggal_masuk)) {
            echo "<script>
                alert('Gagal: Semua inputan wajib harus diisi!');
                window.history.back();
            </script>";
            exit;
        }

        if ($jumlah < 0 || $stok_minimum < 0) {
            echo "<script>
                alert('Gagal: Jumlah dan stok minimum tidak boleh bernilai negatif!');
                window.history.back();
            </script>";
            exit;
        }

        // Cek keunikan kode_barang
        $cek_query  = "SELECT id_barang FROM barang WHERE kode_barang = '$kode_barang'";
        $cek_result = mysqli_query($koneksi, $cek_query);
        if (mysqli_num_rows($cek_result) > 0) {
            echo "<script>
                alert('Gagal: Kode barang sudah terdaftar! Gunakan kode barang lain.');
                window.history.back();
            </script>";
            exit;
        }

        // Query Insert
        $query = "INSERT INTO barang (kode_barang, nama_barang, id_kategori, id_lokasi, jumlah, stok_minimum, kondisi, tanggal_masuk, keterangan, created_by) 
                  VALUES ('$kode_barang', '$nama_barang', $id_kategori, $id_lokasi, $jumlah, $stok_minimum, '$kondisi', '$tanggal_masuk', '$keterangan', $created_by)";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                alert('Berhasil: Data barang berhasil ditambahkan.');
                window.location.href = 'dashboard.php?page=barang';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menambahkan data: " . mysqli_error($koneksi) . "');
                window.history.back();
            </script>";
        }
        exit;

    } elseif ($action === 'ubah') {
        // Ambil dan bersihkan data input
        $id_barang     = intval($_POST['id_barang']);
        $kode_barang   = mysqli_real_escape_string($koneksi, trim($_POST['kode_barang']));
        $nama_barang   = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
        $id_kategori   = !empty($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 'NULL';
        $id_lokasi     = !empty($_POST['id_lokasi']) ? intval($_POST['id_lokasi']) : 'NULL';
        $jumlah        = intval($_POST['jumlah']);
        $stok_minimum  = intval($_POST['stok_minimum']);
        $kondisi       = mysqli_real_escape_string($koneksi, $_POST['kondisi']);
        $tanggal_masuk = mysqli_real_escape_string($koneksi, $_POST['tanggal_masuk']);
        $keterangan    = mysqli_real_escape_string($koneksi, trim($_POST['keterangan']));

        // Validasi server-side
        if (empty($id_barang) || empty($kode_barang) || empty($nama_barang) || empty($kondisi) || empty($tanggal_masuk)) {
            echo "<script>
                alert('Gagal: Semua inputan wajib harus diisi!');
                window.history.back();
            </script>";
            exit;
        }

        if ($jumlah < 0 || $stok_minimum < 0) {
            echo "<script>
                alert('Gagal: Jumlah dan stok minimum tidak boleh bernilai negatif!');
                window.history.back();
            </script>";
            exit;
        }

        // Cek keunikan kode_barang (kecuali milik data ini sendiri)
        $cek_query  = "SELECT id_barang FROM barang WHERE kode_barang = '$kode_barang' AND id_barang != $id_barang";
        $cek_result = mysqli_query($koneksi, $cek_query);
        if (mysqli_num_rows($cek_result) > 0) {
            echo "<script>
                alert('Gagal: Kode barang sudah terdaftar pada barang lain! Gunakan kode barang lain.');
                window.history.back();
            </script>";
            exit;
        }

        // Query Update
        $query = "UPDATE barang SET 
                    kode_barang = '$kode_barang', 
                    nama_barang = '$nama_barang', 
                    id_kategori = $id_kategori, 
                    id_lokasi = $id_lokasi, 
                    jumlah = $jumlah, 
                    stok_minimum = $stok_minimum, 
                    kondisi = '$kondisi', 
                    tanggal_masuk = '$tanggal_masuk', 
                    keterangan = '$keterangan' 
                  WHERE id_barang = $id_barang";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                alert('Berhasil: Data barang berhasil diperbarui.');
                window.location.href = 'dashboard.php?page=barang';
            </script>";
        } else {
            echo "<script>
                alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');
                window.history.back();
            </script>";
        }
        exit;

    } elseif ($action === 'hapus') {
        $id_barang = intval($_POST['id_barang']);

        if (empty($id_barang)) {
            echo "<script>
                alert('Gagal: ID barang tidak valid.');
                window.location.href = 'dashboard.php?page=barang';
            </script>";
            exit;
        }

        // Query Delete
        $query = "DELETE FROM barang WHERE id_barang = $id_barang";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                alert('Berhasil: Data barang telah berhasil dihapus.');
                window.location.href = 'dashboard.php?page=barang';
            </script>";
        } else {
            echo "<script>
                alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');
                window.location.href = 'dashboard.php?page=barang';
            </script>";
        }
        exit;
    }
}

// Jika diakses tidak sah
header("Location: dashboard.php?page=barang");
exit;
?>
