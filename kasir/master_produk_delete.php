<?php
session_start();
include 'session.php';
include '../config/koneksi.php';
// Periksa apakah ProdukID dikirim melalui URL
if (isset($_GET['ProdukID'])) {
    $produkID = $_GET['ProdukID'];

    // Query untuk menghapus produk berdasarkan ProdukID
    $query = "DELETE FROM produk WHERE ProdukID = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind parameter dan eksekusi
        mysqli_stmt_bind_param($stmt, 'i', $produkID);

        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, tampilkan alert dan redirect
            echo "<script>
                    alert('Produk berhasil dihapus!');
                    window.location.href = 'master_produk.php';
                  </script>";
        } else {
            // Jika gagal, tampilkan alert dengan pesan error
            $errorMessage = mysqli_error($conn);
            echo "<script>
                    alert('Gagal menghapus produk: $errorMessage');
                    window.location.href = 'master_produk.php';
                  </script>";
        }

        // Tutup statement
        mysqli_stmt_close($stmt);
    } else {
        // Gagal mempersiapkan query
        $errorMessage = mysqli_error($conn);
        echo "<script>
                alert('Gagal mempersiapkan query: $errorMessage');
                window.location.href = 'master_produk.php';
              </script>";
    }
} else {
    // Redirect jika ProdukID tidak disediakan
    echo "<script>
            alert('ProdukID tidak ditemukan!');
            window.location.href = 'master_produk.php';
          </script>";
    exit();
}

// Tutup koneksi
mysqli_close($conn);
