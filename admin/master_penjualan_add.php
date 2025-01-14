<?php
// master_penjualan_add.php
include '../config/koneksi.php';

if (isset($_POST['submit'])) {
    $pelanggan_id = $_POST['pelanggan_id'];
    $tanggal = date('Y-m-d');
    $total_harga = 0;

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert penjualan
        $query_penjualan = "INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID) 
                           VALUES ('$tanggal', '$total_harga', '$pelanggan_id')";
        if (!mysqli_query($conn, $query_penjualan)) {
            throw new Exception("Gagal menambahkan penjualan!");
        }
        $penjualan_id = mysqli_insert_id($conn);

        // Insert detail dan hitung total
        foreach ($_POST['produk_id'] as $key => $produk_id) {
            $jumlah = $_POST['jumlah'][$key];

            // Cek stok produk
            $query_stok = "SELECT ProdukID, Harga, Stok, NamaProduk FROM produk WHERE ProdukID = '$produk_id' FOR UPDATE";
            $result_stok = mysqli_query($conn, $query_stok);
            if (!$result_stok) {
                throw new Exception("Gagal mengecek stok produk!");
            }
            $produk = mysqli_fetch_assoc($result_stok);

            // Validasi stok mencukupi
            if ($produk['Stok'] < $jumlah) {
                throw new Exception("Stok {$produk['NamaProduk']} tidak mencukupi! Tersedia: {$produk['Stok']}, Diminta: $jumlah");
            }

            $subtotal = $produk['Harga'] * $jumlah;
            $total_harga += $subtotal;

            // Update stok produk
            $stok_baru = $produk['Stok'] - $jumlah;
            $query_update_stok = "UPDATE produk SET Stok = $stok_baru WHERE ProdukID = '$produk_id'";
            if (!mysqli_query($conn, $query_update_stok)) {
                throw new Exception("Gagal mengupdate stok!");
            }

            // Insert detail penjualan
            $query_detail = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, SubTotal) 
                           VALUES ('$penjualan_id', '$produk_id', '$jumlah', '$subtotal')";
            if (!mysqli_query($conn, $query_detail)) {
                throw new Exception("Gagal menambahkan detail penjualan!");
            }
        }

        // Update total harga di tabel penjualan
        $query_update = "UPDATE penjualan SET TotalHarga = '$total_harga' 
                        WHERE PenjualanID = '$penjualan_id'";
        if (!mysqli_query($conn, $query_update)) {
            throw new Exception("Gagal mengupdate total harga!");
        }

        // Commit transaction
        mysqli_commit($conn);

        echo "<script>
                alert('Penjualan berhasil ditambahkan!');
                window.location.href = 'master_penjualan.php';
              </script>";
        exit();
    } catch (Exception $e) {
        // Rollback transaction jika terjadi error
        mysqli_rollback($conn);
        echo "<script>
                alert('" . $e->getMessage() . "');
                window.history.back();
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Penjualan</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7fc;
        color: #333;
        margin: 0;
        padding: 0;
    }

    h2 {
        color: #1d2671;
        text-align: center;
        margin-top: 20px;
    }

    form {
        width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    select,
    input {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        background-color: #1d2671;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    button:hover {
        background-color: #4a2b98;
    }

    #produk-container .produk-item {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .produk-item select,
    .produk-item input {
        width: 45%;
    }

    .remove-button {
        background-color: red;
        color: white;
        padding: 5px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: none;
        /* Hide initially */
    }

    .remove-button:hover {
        background-color: #a51212;
    }
    </style>
</head>

<body>
    <h2>Tambah Penjualan Baru</h2>
    <form method="POST">
        <div class="form-group">
            <label for="pelanggan">Pelanggan:</label>
            <select name="pelanggan_id" id="pelanggan" required>
                <option value="">Pilih Pelanggan</option>
                <?php
                $query = mysqli_query($conn, "SELECT * FROM pelanggan");
                foreach ($query as $pel): ?>
                <option value="<?= $pel['PelangganID'] ?>"><?= $pel['NamaPelanggan'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="produk-container">
            <div class="produk-item">
                <select name="produk_id[]" required>
                    <option value="">Pilih Produk</option>
                    <?php
                    $query = mysqli_query($conn, "SELECT * FROM produk");
                    foreach ($query as $pro): ?>
                    <option value="<?= $pro['ProdukID'] ?>"><?= $pro['NamaProduk'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="jumlah[]" placeholder="Jumlah" required>
                <button type="button" class="remove-button" onclick="removeProduk(this)">Kurangi</button>
            </div>
        </div>

        <button type="button" onclick="tambahProduk()">Tambah Produk</button>
        <button type="submit" name="submit">Simpan</button>
        <button type="button" onclick="window.history.back();">Kembali</button>

    </form>

    <script>
    function tambahProduk() {
        var container = document.getElementById('produk-container');
        var item = container.children[0].cloneNode(true);
        item.getElementsByTagName('select')[0].value = '';
        item.getElementsByTagName('input')[0].value = '';
        // Show the "Kurangi" button for the new item
        item.querySelector('.remove-button').style.display = 'inline-block';
        container.appendChild(item);
    }

    function removeProduk(button) {
        var item = button.closest('.produk-item');
        item.remove();
    }
    </script>
</body>

</html>