<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $nomorTelepon = $_POST['nomor_telepon'];

    // Query untuk menambahkan data ke tabel pelanggan
    $sql = "INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) 
            VALUES ('$namaPelanggan', '$alamat', '$nomorTelepon')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Pengguna berhasil ditambahkan!');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan pengguna: " . $conn->error . "');
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(to bottom, #1d2671, #c33764);
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .form-container {
        background: #fff;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 400px;
        color: #333;
    }

    .form-container h2 {
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
        color: #6a11cb;
        text-align: center;
    }

    .form-container label {
        font-size: 1rem;
        font-weight: bold;
        display: block;
        margin-bottom: 0.5rem;
    }

    .form-container input {
        width: 100%;
        padding: 0.8rem;
        margin-bottom: 1.2rem;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-container input:focus {
        border-color: #6a11cb;
        outline: none;
        box-shadow: 0 0 8px rgba(106, 17, 203, 0.3);
    }

    .form-container button {
        width: 100%;
        padding: 0.8rem;
        background: linear-gradient(to right, #1d2671, #c33764);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
    }

    .form-container button:hover {
        background: linear-gradient(to right, #2575fc, #6a11cb);
    }

    .form-container .note {
        font-size: 0.9rem;
        color: #555;
        margin-top: 0.5rem;
        text-align: center;
    }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Tambah Pengguna</h2>
        <form method="POST">
            <label for="nama_pelanggan">Nama Pelanggan</label>
            <input type="text" name="nama_pelanggan" id="nama_pelanggan" placeholder="Masukkan nama pelanggan" required>

            <label for="alamat">Alamat</label>
            <input type="text" name="alamat" id="alamat" placeholder="Masukkan alamat" required>

            <label for="nomor_telepon">Nomor Telepon</label>
            <input type="text" name="nomor_telepon" id="nomor_telepon" placeholder="Masukkan nomor telepon" required>

            <button type="submit">Tambah Pengguna</button>
        </form>
    </div>
</body>

</html>