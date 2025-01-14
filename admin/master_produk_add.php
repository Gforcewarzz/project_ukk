<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaProduk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $stmt = $conn->prepare("INSERT INTO produk (NamaProduk, Harga, Stok) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $namaProduk, $harga, $stok);

    if ($stmt->execute()) {
        echo "<script>
                alert('Produk berhasil ditambahkan.');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menambahkan produk.');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
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
        <h2>Tambah Produk</h2>
        <form method="POST">
            <label for="nama_produk">Nama Produk:</label>
            <input type="text" id="nama_produk" name="nama_produk" required>

            <label for="harga">Harga:</label>
            <input type="number" id="harga" name="harga" required>

            <label for="stok">Stok:</label>
            <input type="number" id="stok" name="stok" required>

            <button type="submit">Tambah</button>
        </form>
    </div>
</body>

</html>