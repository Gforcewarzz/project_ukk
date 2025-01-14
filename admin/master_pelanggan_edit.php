<?php
session_start();
include 'session.php';
include '../config/koneksi.php';

// Periksa apakah PelangganID dikirim melalui GET
if (isset($_GET['PelangganID'])) {
    $pelangganID = $_GET['PelangganID'];

    // Ambil data pelanggan berdasarkan PelangganID
    $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE PelangganID = ?");
    $stmt->bind_param("i", $pelangganID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah data ditemukan
    if ($result->num_rows > 0) {
        $pelanggan = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Pelanggan tidak ditemukan.');
                window.location.href = 'index.php';
              </script>";
        exit;
    }

    $stmt->close();
} else {
    echo "<script>
            alert('PelangganID tidak ditemukan.');
            window.location.href = 'index.php'; 
          </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaPelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $nomorTelepon = $_POST['nomor_telepon'];

    // Update data ke database
    $stmt = $conn->prepare("UPDATE pelanggan SET NamaPelanggan = ?, Alamat = ?, NomorTelepon = ? WHERE PelangganID = ?");
    $stmt->bind_param("sssi", $namaPelanggan, $alamat, $nomorTelepon, $pelangganID);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data berhasil diperbarui.');
                window.location.href = 'index.php'; // Ganti dengan halaman master pelanggan Anda
              </script>";
    } else {
        echo "<script>
                alert('Gagal memperbarui data: " . $conn->error . "');
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #4b6cb7, #182848);
            color: #fff;
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
            color: #4b6cb7;
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
            border-color: #4b6cb7;
            outline: none;
            box-shadow: 0 0 8px rgba(75, 108, 183, 0.3);
        }

        .form-container button {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-container button:hover {
            background: linear-gradient(to right, #182848, #4b6cb7);
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Edit Pelanggan</h2>
        <form method="POST">
            <label for="nama_pelanggan">Nama Pelanggan</label>
            <input type="text" name="nama_pelanggan" id="nama_pelanggan" value="<?= $pelanggan['NamaPelanggan'] ?>"
                required>

            <label for="alamat">Alamat</label>
            <input type="text" name="alamat" id="alamat" value="<?= $pelanggan['Alamat'] ?>" required>

            <label for="nomor_telepon">Nomor Telepon</label>
            <input type="text" name="nomor_telepon" id="nomor_telepon" value="<?= $pelanggan['NomorTelepon'] ?>"
                required>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</body>

</html>