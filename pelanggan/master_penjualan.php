<?php
// Mulai sesi untuk mengambil data pelanggan yang sedang login
session_start();
include '../config/koneksi.php';

// Ambil nama pelanggan yang sedang login dari session
$nama_pelanggan = isset($_SESSION['nama_pelanggan']) ? $_SESSION['nama_pelanggan'] : null;

if (!$nama_pelanggan) {
    // Jika tidak ada pelanggan yang login, redirect ke halaman login
    header("Location: login.php");
    exit();
}

// Periode yang dipilih (bulan dan tahun)
$periode = isset($_GET['periode']) ? $_GET['periode'] : date('Y-m');
list($tahun, $bulan) = explode('-', $periode);

// Query untuk mendapatkan detail penjualan per bulan sesuai dengan nama pelanggan
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
          AND pel.NamaPelanggan = ?
          GROUP BY p.PenjualanID
          ORDER BY p.TanggalPenjualan DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $periode, $nama_pelanggan); // Menyediakan dua parameter, periode dan nama pelanggan
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
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary-color: #4f46e5;
            --secondary-color: #818cf8;
            --background-color: #f3f4f6;
            --sidebar-color: #1e1b4b;
            --card-color: #ffffff;
            --text-primary: #111827;
            --text-secondary: #6b7280;
        }

        body {
            background-color: var(--background-color);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-color);
            padding: 2rem 1rem;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 0 1rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            gap: 1rem;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link i {
            width: 1.5rem;
            text-align: center;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 2rem;
        }

        /* Navbar Styles */
        .navbar {
            background-color: var(--card-color);
            padding: 1rem 2rem;
            border-radius: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        /* Dashboard Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .card {
            background-color: var(--card-color);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        .total {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .page-header {
            background-color: var(--card-color);
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
            background-color: #3b42b8;
        }

        .btn-danger {
            background-color: #e11d48;
            color: white;
        }

        .btn-danger:hover {
            background-color: #9f1239;
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .logout-btn {
            background: linear-gradient(135deg, #f64f59, #c471ed);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #c471ed, #12c2e9);
            transform: translateY(-3px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .logout-btn i {
            font-size: 1.2rem;
        }

        .logout-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 300%;
            height: 300%;
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(45deg);
            transition: left 0.5s ease;
        }

        .logout-btn:hover::after {
            left: 0;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1>Detail Pembelian Bulanan</h1>
            <div class="header-buttons">
                <a href="index.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i>Kembali</a>
                <a href="master_penjualan_add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah
                    Penjualan</a>
                <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Total Harga</th>
                        <th>Jumlah Item</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($data)) {
                        echo '<tr><td colspan="6" style="text-align:center;">Tidak ada data untuk periode ini.</td></tr>';
                    } else {
                        $no = 1;
                        foreach ($data as $row) {
                            echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['TanggalPenjualan']}</td>
                                <td>{$row['NamaPelanggan']}</td>
                                <td>" . number_format($row['TotalHarga'], 2, ',', '.') . "</td>
                                <td>{$row['JumlahItem']}</td>
                                <td><a href='master_penjualan_detail.php?id={$row['PenjualanID']}' class='btn btn-secondary'><i class='fas fa-eye'></i> Detail</a></td>
                            </tr>";
                            $no++;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="total">Total Pembelian</td>
                        <td colspan="3" class="total"><?php echo number_format($total_penjualan, 2, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>

</html>