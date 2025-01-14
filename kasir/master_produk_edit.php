<?php
session_start();
include 'session.php';
include '../config/koneksi.php';
$ProdukID = $_GET['ProdukID'] ?? null;

if (!$ProdukID) {
    echo "ID Produk tidak valid!";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM produk WHERE ProdukID = ?");
$stmt->bind_param("s", $ProdukID);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result->fetch_assoc();

if (!$produk) {
    echo "Produk tidak ditemukan!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $NamaProduk = $_POST['nama_produk'];
    $Harga = $_POST['harga'];
    $Stok = $_POST['stok'];

    $update_stmt = $conn->prepare("UPDATE produk SET NamaProduk = ?, Harga = ?, Stok = ? WHERE ProdukID = ?");
    $update_stmt->bind_param("sdis", $NamaProduk, $Harga, $Stok, $ProdukID);

    if ($update_stmt->execute()) {
        echo "<script>
                alert('Data produk berhasil diperbarui!');
                window.location.href = 'master_produk.php';
              </script>";
    } else {
        echo "<script>alert('Gagal memperbarui data produk!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
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

        .btn-cancel {
            display: block;
            width: 100%;
            padding: 0.8rem;
            text-align: center;
            background: #c33764;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            margin-top: 0.5rem;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-cancel:hover {
            background: #a22c50;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Edit Produk</h2>
        <form method="POST">
            <label for="NamaProduk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk"
                value="<?= htmlspecialchars($produk['NamaProduk']); ?>" required>

            <label for="Harga">Harga</label>
            <input type="number" id="harga" name="harga" value="<?= htmlspecialchars($produk['Harga']); ?>" required>

            <label for="Stok">Stok</label>
            <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($produk['Stok']); ?>" required>

            <button type="submit">Simpan Perubahan</button>
            <a href="master_produk.php" class="btn-cancel">Batal</a>
        </form>
    </div>
</body>

</html>