<?php
// master_penjualan_edit.php
include '../config/koneksi.php';

$penjualan_id = $_GET['id'] ?? '';

// Get data penjualan
$query_penjualan = "SELECT p.*, pel.NamaPelanggan 
                    FROM penjualan p
                    JOIN pelanggan pel ON p.PelangganID = pel.PelangganID
                    WHERE p.PenjualanID = '$penjualan_id'";
$result_penjualan = mysqli_query($conn, $query_penjualan);
$penjualan = mysqli_fetch_assoc($result_penjualan);

// Get detail penjualan
$query_detail = "SELECT dp.*, p.NamaProduk, p.Stok 
                 FROM detailpenjualan dp
                 JOIN produk p ON dp.ProdukID = p.ProdukID
                 WHERE dp.PenjualanID = '$penjualan_id'";
$result_detail = mysqli_query($conn, $query_detail);
$details = mysqli_fetch_all($result_detail, MYSQLI_ASSOC);

if (isset($_POST['submit'])) {
    mysqli_begin_transaction($conn);

    try {
        $pelanggan_id = $_POST['pelanggan_id'];
        $total_harga = 0;

        // Restore old stocks first
        $query_old_details = "SELECT ProdukID, JumlahProduk FROM detailpenjualan 
                            WHERE PenjualanID = '$penjualan_id'";
        $result_old_details = mysqli_query($conn, $query_old_details);

        while ($old_detail = mysqli_fetch_assoc($result_old_details)) {
            $produk_id = $old_detail['ProdukID'];
            $jumlah_lama = $old_detail['JumlahProduk'];

            // Kembalikan stok lama
            mysqli_query($conn, "UPDATE produk 
                               SET Stok = Stok + $jumlah_lama 
                               WHERE ProdukID = '$produk_id'");
        }

        // Delete old details
        mysqli_query($conn, "DELETE FROM detailpenjualan WHERE PenjualanID = '$penjualan_id'");

        // Process new details
        foreach ($_POST['produk_id'] as $key => $produk_id) {
            $jumlah = $_POST['jumlah'][$key];

            // Check stock
            $query_stok = "SELECT Harga, Stok, NamaProduk FROM produk 
                          WHERE ProdukID = '$produk_id' FOR UPDATE";
            $result_stok = mysqli_query($conn, $query_stok);
            $produk = mysqli_fetch_assoc($result_stok);

            if ($produk['Stok'] < $jumlah) {
                throw new Exception("Stok {$produk['NamaProduk']} tidak mencukupi! Tersedia: {$produk['Stok']}, Diminta: $jumlah");
            }

            $subtotal = $produk['Harga'] * $jumlah;
            $total_harga += $subtotal;

            // Update stock
            $stok_baru = $produk['Stok'] - $jumlah;
            mysqli_query($conn, "UPDATE produk 
                               SET Stok = $stok_baru 
                               WHERE ProdukID = '$produk_id'");

            // Insert new detail
            mysqli_query($conn, "INSERT INTO detailpenjualan 
                               (PenjualanID, ProdukID, JumlahProduk, SubTotal) 
                               VALUES ('$penjualan_id', '$produk_id', '$jumlah', '$subtotal')");
        }

        // Update penjualan
        mysqli_query($conn, "UPDATE penjualan 
                           SET TotalHarga = '$total_harga', PelangganID = '$pelanggan_id' 
                           WHERE PenjualanID = '$penjualan_id'");

        mysqli_commit($conn);

        echo "<script>
                alert('Penjualan berhasil diupdate!');
                window.location.href = 'master_penjualan.php';
              </script>";
        exit();
    } catch (Exception $e) {
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
    <title>Edit Penjualan</title>
    <style>
    /* Gunakan style yang sama dengan form add */
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
    }

    .remove-button:hover {
        background-color: #a51212;
    }
    </style>
</head>

<body>
    <h2>Edit Penjualan</h2>
    <form method="POST">
        <div class="form-group">
            <label for="pelanggan">Pelanggan:</label>
            <select name="pelanggan_id" id="pelanggan" required>
                <?php
                $query = mysqli_query($conn, "SELECT * FROM pelanggan");
                while ($pel = mysqli_fetch_assoc($query)) {
                    $selected = ($pel['PelangganID'] == $penjualan['PelangganID']) ? 'selected' : '';
                    echo "<option value='{$pel['PelangganID']}' $selected>{$pel['NamaPelanggan']}</option>";
                }
                ?>
            </select>
        </div>

        <div id="produk-container">
            <?php foreach ($details as $detail): ?>
            <div class="produk-item">
                <select name="produk_id[]" required>
                    <?php
                        $query = mysqli_query($conn, "SELECT * FROM produk");
                        while ($pro = mysqli_fetch_assoc($query)) {
                            $selected = ($pro['ProdukID'] == $detail['ProdukID']) ? 'selected' : '';
                            echo "<option value='{$pro['ProdukID']}' $selected>{$pro['NamaProduk']}</option>";
                        }
                        ?>
                </select>
                <input type="number" name="jumlah[]" value="<?= $detail['JumlahProduk'] ?>" required>
                <button type="button" class="remove-button" onclick="removeProduk(this)">Kurangi</button>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" onclick="tambahProduk()">Tambah Produk</button>
        <button type="submit" name="submit">Simpan</button>
    </form>

    <script>
    function tambahProduk() {
        var container = document.getElementById('produk-container');
        var itemFirst = container.children[0];
        var item = itemFirst.cloneNode(true);

        // Reset values
        item.getElementsByTagName('select')[0].value = '';
        item.getElementsByTagName('input')[0].value = '';

        // Show remove button
        item.querySelector('.remove-button').style.display = 'inline-block';

        container.appendChild(item);
    }

    function removeProduk(button) {
        var container = document.getElementById('produk-container');
        if (container.children.length > 1) {
            var item = button.closest('.produk-item');
            item.remove();
        } else {
            alert('Minimal harus ada satu produk!');
        }
    }
    </script>
</body>

</html>