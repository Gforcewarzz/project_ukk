<?php
// master_penjualan_detail.php
include '../config/koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data penjualan
$query_penjualan = $conn->prepare("
    SELECT p.*, pel.NamaPelanggan 
    FROM penjualan p
    JOIN pelanggan pel ON p.PelangganID = pel.PelangganID 
    WHERE p.PenjualanID = ?
");
$query_penjualan->bind_param('i', $id);
$query_penjualan->execute();
$result_penjualan = $query_penjualan->get_result();
$penjualan = $result_penjualan->fetch_assoc();

// Ambil detail penjualan
$query_detail = $conn->prepare("
    SELECT dp.*, p.NamaProduk, p.Harga
    FROM detailpenjualan dp
    JOIN produk p ON dp.ProdukID = p.ProdukID 
    WHERE dp.PenjualanID = ?
");
$query_detail->bind_param('i', $id);
$query_detail->execute();
$result_detail = $query_detail->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penjualan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 2rem;
        background-color: #f4f4f9;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #1d2671;
        margin-bottom: 1.5rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }

    th,
    td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #edf2f7;
    }

    th {
        background-color: #f8fafc;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    tbody tr:hover {
        background-color: #f8fafc;
    }

    .btn {
        text-decoration: none;
        background: #1d2671;
        color: #ffffff;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.3s ease;
    }

    .btn:hover {
        background: #2a3282;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Detail Penjualan</h2>
        <p><strong>Tanggal:</strong> <?= htmlspecialchars($penjualan['TanggalPenjualan']) ?></p>
        <p><strong>Pelanggan:</strong> <?= htmlspecialchars($penjualan['NamaPelanggan']) ?></p>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($detail = $result_detail->fetch_assoc()):
                    $subtotal = $detail['JumlahProduk'] * $detail['Harga'];
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($detail['NamaProduk']) ?></td>
                    <td><?= htmlspecialchars($detail['JumlahProduk']) ?></td>
                    <td>Rp<?= number_format($detail['Harga'], 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="4" align="right"><strong>Total:</strong></td>
                    <td><strong>Rp<?= number_format($penjualan['TotalHarga'], 0, ',', '.') ?></strong></td>
                </tr>
            </tbody>
        </table>

        <a href="master_penjualan.php" class="btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</body>

</html>