<?php
include '../config/koneksi.php';

$periode = isset($_GET['periode']) ? $_GET['periode'] : date('Y-m');
list($tahun, $bulan) = explode('-', $periode);

// Query untuk mendapatkan detail penjualan per bulan
$query = "SELECT 
            p.PenjualanID,
            p.TanggalPenjualan,
            pel.NamaPelanggan,
            p.TotalHarga,
            COUNT(dp.DetailID) as JumlahItem
          FROM penjualan p
          JOIN pelanggan pel ON p.PelangganID = pel.PelangganID
          JOIN detailpenjualan dp ON p.PenjualanID = dp.PenjualanID
          WHERE DATE_FORMAT(p.TanggalPenjualan, '%Y-%m') = ?
          GROUP BY p.PenjualanID
          ORDER BY p.TanggalPenjualan DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $periode);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Hitung total
$total_penjualan = 0;
$data = [];
while ($row = mysqli_fetch_array($result)) {
    $total_penjualan += $row['TotalHarga'];
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penjualan Bulanan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    :root {
        --primary-color: #1d2671;
        --primary-hover: #2a3282;
        --secondary-color: #c33764;
        --background: #f4f4f9;
        --card-bg: #ffffff;
        --text-primary: #1a1a1a;
        --text-secondary: #666666;
        --danger: #ef4444;
        --danger-hover: #dc2626;
        --success: #059669;
        --success-hover: #047857;
    }

    body {
        background-color: var(--background);
        padding: 2rem;
        color: var(--text-primary);
        min-height: 100vh;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        background-color: var(--card-bg);
        padding: 1.5rem 2rem;
        border-radius: 1rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    h1 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .header-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
    }

    .btn-secondary {
        background-color: #e5e7eb;
        color: var(--text-primary);
    }

    .btn-secondary:hover {
        background-color: #d1d5db;
    }

    .btn-success {
        background-color: var(--success);
        color: white;
    }

    .btn-success:hover {
        background-color: var(--success-hover);
    }

    .card {
        background-color: var(--card-bg);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 1rem 1.5rem;
        text-align: left;
        border-bottom: 1px solid #edf2f7;
    }

    td a {
        margin-top: 5px;
    }

    th {
        background-color: #f8fafc;
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    tbody tr:hover {
        background-color: #f8fafc;
    }


    .btn-secondary {
        background-color: #3b82f6;
        color: white;
    }

    .btn -secondary:hover {
        background-color: #2563eb;
    }

    .btn-danger {
        background-color: var(--danger);
        color: white;
    }

    .btn -danger:hover {
        background-color: var(--danger-hover);
    }


    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .print-preview {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }

    .print-content {
        background: white;
        padding: 2rem;
        border-radius: 0.5rem;
        width: 100%;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .print-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .print-buttons {
        margin-top: 2rem;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .print-content,
        .print-content * {
            visibility: visible;
        }

        .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 2rem;
        }

        .print-buttons {
            display: none;
        }
    }

    .filters {
        background-color: var(--card-bg);
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    select {
        padding: 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background-color: var(--card-bg);
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .summary-card h3 {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .summary-card p {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-color);
    }

    .chart-container {
        background-color: var(--card-bg);
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
    }

    .detail-header {
        background-color: var(--card-bg);
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .detail-info {
        display: flex;
        gap: 2rem;
    }

    .detail-info-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .detail-info-item span:first-child {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .detail-info-item span:last-child {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>Detail Penjualan - <?= date('F Y', strtotime($periode . '-01')) ?></h1>
            <div class="header-buttons">
                <button onclick="showPrintPreview()" class="btn btn-success">
                    <i class="fas fa-print"></i> Cetak Detail
                </button>
                <a href="master_penjualan_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Penjualan
                </a>
                <a href="master_penjualan.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="detail-header">
            <div class="detail-info">
                <div class="detail-info-item">
                    <span>Total Transaksi</span>
                    <span><?= count($data) ?></span>
                </div>
                <div class="detail-info-item">
                    <span>Total Penjualan</span>
                    <span>Rp<?= number_format($total_penjualan) ?></span>
                </div>
                <div class="detail-info-item">
                    <span>Rata-rata Transaksi</span>
                    <span>Rp<?= number_format($total_penjualan / (count($data) ?: 1)) ?></span>
                </div>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Jumlah Item</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($data as $row):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($row['TanggalPenjualan'])) ?></td>
                        <td><?= htmlspecialchars($row['NamaPelanggan']) ?></td>
                        <td><?= number_format($row['JumlahItem']) ?> item</td>
                        <td>Rp<?= number_format($row['TotalHarga']) ?></td>
                        <td class="action-buttons">
                            <a href="master_penjualan_detail.php?id=<?= $row['PenjualanID'] ?>" class="btn btn-primary">
                                <i class="fas fa-info-circle"></i> Detail
                            </a>
                            <a href="master_penjualan_edit.php?id=<?= $row['PenjualanID']; ?>"
                                class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="master_penjualan_delete.php?id=<?= $row['PenjualanID'] ?>" class="btn btn-danger"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print Preview Modal -->
    <div id="printPreview" class="print-preview">
        <div class="print-content">
            <div class="print-header">
                <h2>DETAIL PENJUALAN BULANAN</h2>
                <p>Periode: <?= date('F Y', strtotime($periode . '-01')) ?></p>
            </div>

            <div style="margin-bottom: 2rem;">
                <table>
                    <tr>
                        <td style="width: 33%; padding: 0.5rem;">
                            <strong>Total Transaksi:</strong> <?= count($data) ?>
                        </td>
                        <td style="width: 33%; padding: 0.5rem;">
                            <strong>Total Penjualan:</strong> Rp<?= number_format($total_penjualan) ?>
                        </td>
                        <td style="width: 33%; padding: 0.5rem;">
                            <strong>Rata-rata Transaksi:</strong>
                            Rp<?= number_format($total_penjualan / (count($data) ?: 1)) ?>
                        </td>
                    </tr>
                </table>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Jumlah Item</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    foreach ($data as $row):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($row['TanggalPenjualan'])) ?></td>
                        <td><?= htmlspecialchars($row['NamaPelanggan']) ?></td>
                        <td><?= number_format($row['JumlahItem']) ?> item</td>
                        <td>Rp<?= number_format($row['TotalHarga']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                        <td style="font-weight: bold;">Rp<?= number_format($total_penjualan) ?></td>
                    </tr>
                </tfoot>
            </table>

            <div style="margin-top: 50px; text-align: right;">
                <p>Jakarta, <?= date('d F Y') ?></p>
                <br><br><br>
                <p>(_____________________)</p>
                <p>Manager</p>
            </div>

            <div class="print-buttons">
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <button onclick="closePrintPreview()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
    function showPrintPreview() {
        document.getElementById('printPreview').style.display = 'flex';
    }

    function closePrintPreview() {
        document.getElementById('printPreview').style.display = 'none';
    }

    document.getElementById('printPreview').addEventListener('click', function(e) {
        if (e.target === this) {
            closePrintPreview();
        }
    });
    </script>
</body>

</html>