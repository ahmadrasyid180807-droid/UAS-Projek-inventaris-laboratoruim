<?php
session_start();

// Mengosongkan semua variabel session
$_SESSION = [];

// Menghancurkan session
session_unset();
session_destroy();

// Redirect dengan notifikasi berhasil keluar
echo "<script>
    alert('Anda telah berhasil keluar dari sistem.');
    window.location.href = 'index.php';
</script>";
exit;
?>
