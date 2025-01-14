<?php
require '../config/koneksi.php';

// Periksa apakah PelangganID dikirim melalui GET
if (isset($_GET['PelangganID'])) {
    $pelangganID = $_GET['PelangganID'];

    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("DELETE FROM pelanggan WHERE PelangganID = ?");
    $stmt->bind_param("i", $pelangganID);

    if ($stmt->execute()) {
        echo "<script>
                alert('pelanggan berhasil dihapus.');
                window.location.href = 'index.php'; // Ganti dengan halaman master pelanggan Anda
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus pelanggan: " . $conn->error . "');
              </script>";
    }

    $stmt->close();
} else {
    echo "<script>
            alert('PelangganID tidak ditemukan.');
            window.location.href = 'index.php'; // Ganti dengan halaman master pelanggan Anda
          </script>";
}

$conn->close();