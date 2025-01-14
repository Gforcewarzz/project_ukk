<?php
include '../config/koneksi.php';

$query = mysqli_query($conn, "SELECT * FROM pelanggan");

if (!$query) {
    die('Query failed: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data pelanggan</title>
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

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-admin {
        background-color: var(--primary-color);
        color: white;
    }

    .badge-pelanggan {
        background-color: var(--secondary-color);
        color: white;
    }

    .edit-btn {
        background-color: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .edit-btn:hover {
        background-color: var(--primary-hover);
    }

    .delete-btn {
        background-color: var(--danger);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .delete-btn:hover {
        background-color: var(--danger-hover);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
        }

        .header-buttons {
            width: 100%;
            justify-content: flex-end;
        }

        .card {
            overflow-x: auto;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1>Data pelanggan</h1>
            <div class="header-buttons">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="master_pelanggan_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah pelanggan
                </a>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat</th>
                        <th>Nomor Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($pelanggan = mysqli_fetch_array($query)):
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['NamaPelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['Alamat']); ?></td>
                        <td><?php echo htmlspecialchars($pelanggan['NomorTelepon']); ?></td>

                        <td class="action-buttons">
                            <a href="master_pelanggan_edit.php?PelangganID=<?= $pelanggan['PelangganID'] ?>"
                                class="btn edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <a href="master_pelanggan_delete.php?PelangganID=<?= $pelanggan['PelangganID'] ?>"
                                class="btn delete-btn"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">
                                <i class="fas fa-trash"></i> Hapus
                            </a>

                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>