<?php
session_start();
include 'session.php';
include '../config/koneksi.php';
// Periksa apakah UserID dikirim melalui GET
if (isset($_GET['UserID'])) {
  $userID = $_GET['UserID'];

  // Gunakan prepared statement untuk keamanan
  $stmt = $conn->prepare("DELETE FROM users WHERE UserID = ?");
  $stmt->bind_param("i", $userID);

  if ($stmt->execute()) {
    echo "<script>
                alert('User berhasil dihapus.');
                window.location.href = 'index.php'; // Ganti dengan halaman master user Anda
              </script>";
  } else {
    echo "<script>
                alert('Gagal menghapus user: " . $conn->error . "');
              </script>";
  }

  $stmt->close();
} else {
  echo "<script>
            alert('UserID tidak ditemukan.');
            window.location.href = 'index.php'; // Ganti dengan halaman master user Anda
          </script>";
}

$conn->close();
