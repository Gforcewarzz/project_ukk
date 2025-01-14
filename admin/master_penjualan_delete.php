<?php
session_start();
include 'session.php';
include '../config/koneksi.php';
// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
        alert('ID Penjualan tidak ditemukan!');
        window.location.href = 'master_penjualan.php';
    </script>";
    exit;
}

$penjualanID = mysqli_real_escape_string($conn, $_GET['id']);

try {
    // Delete from detailpenjualan first (child table)
    $deleteDetail = mysqli_query($conn, "DELETE FROM detailpenjualan WHERE PenjualanID = '$penjualanID'");

    if (!$deleteDetail) {
        throw new Exception("Error menghapus detail penjualan: " . mysqli_error($conn));
    }

    // Then delete from penjualan (parent table)
    $deletePenjualan = mysqli_query($conn, "DELETE FROM penjualan WHERE PenjualanID = '$penjualanID'");

    if (!$deletePenjualan) {
        throw new Exception("Error menghapus penjualan: " . mysqli_error($conn));
    }

    echo "<script>
        alert('Data penjualan berhasil dihapus!');
        window.location.href = 'master_penjualan.php';
    </script>";
} catch (Exception $e) {
    echo "<script>
        alert('Gagal menghapus data: " . addslashes($e->getMessage()) . "');
        window.location.href = 'master_penjualan.php';
    </script>";
}

mysqli_close($conn);
