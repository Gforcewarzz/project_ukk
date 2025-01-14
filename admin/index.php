<?php
session_start();
include 'session.php';
include '../config/koneksi.php';
$username = $_SESSION['username'];

// Query untuk mendapatkan data
$query_users = "SELECT COUNT(*) AS total_users FROM users";
$query_pelanggan = "SELECT COUNT(*) AS total_pelanggan FROM pelanggan";
$query_penjualan = "SELECT COUNT(*) AS total_penjualan FROM penjualan";
$query_produk = "SELECT COUNT(*) AS total_produk FROM produk";

// Eksekusi query
$result_users = mysqli_query($conn, $query_users);
$result_pelanggan = mysqli_query($conn, $query_pelanggan);
$result_penjualan = mysqli_query($conn, $query_penjualan);
$result_produk = mysqli_query($conn, $query_produk);

// Ambil hasil query
$data_users = mysqli_fetch_assoc($result_users);
$data_pelanggan = mysqli_fetch_assoc($result_pelanggan);
$data_penjualan = mysqli_fetch_assoc($result_penjualan);
$data_produk = mysqli_fetch_assoc($result_produk);
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

    .logout-btn {
        background-color: var(--primary-color);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background-color: var(--secondary-color);
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
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 4px 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: .3s ease-in-out;
    }

    .card:hover {
        scale: 105%;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    .card-icon {
        width: 48px;
        height: 48px;
        background-color: var(--primary-color);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .card h3 {
        color: var(--text-primary);
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .card p {
        color: var(--text-secondary);
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-top: 0.5rem;
    }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Navbar -->
        <div class="navbar">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo substr($username, 0, 1); ?>
                </div>
                <span>Selamat Datang, <?php echo htmlspecialchars($username); ?>!</span>
            </div>
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <h2>Overview</h2>

        <!-- Dashboard Cards -->
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <h3>Total Pengguna</h3>
                <p>Jumlah pengguna aktif saat ini</p>
                <div class="stat-number"><?php echo $data_users['total_users']; ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <h3>Total Pelanggan</h3>
                <p>Jumlah pelanggan yang terdaftar</p>
                <div class="stat-number"><?php echo $data_pelanggan['total_pelanggan']; ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <h3>Total Penjualan</h3>
                <p>Total penjualan hari ini</p>
                <div class="stat-number"><?php echo $data_penjualan['total_penjualan']; ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
                <h3>Total Produk</h3>
                <p>Jumlah produk yang tersedia</p>
                <div class="stat-number"><?php echo $data_produk['total_produk']; ?></div>
            </div>
        </div>
    </div>
</body>

</html>