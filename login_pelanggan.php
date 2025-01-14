<?php
// Menyertakan file conn database
include 'config/koneksi.php';

// Mengecek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);

    // Query untuk mencocokkan nomor telepon dan nama pelanggan
    $query = "SELECT * FROM pelanggan WHERE NomorTelepon = '$nomor_telepon' AND NamaPelanggan = '$nama_pelanggan'";
    $result = $conn->query($query);

    // Jika data ditemukan
    if ($result->num_rows > 0) {
        $pelanggan = $result->fetch_assoc();
        // Jika login berhasil, bisa redirect ke halaman lain atau menampilkan pesan sukses
        session_start();
        $_SESSION['pelanggan_id'] = $pelanggan['PelangganID']; // Menyimpan ID pelanggan dalam session
        $_SESSION['nama_pelanggan'] = $pelanggan['NamaPelanggan']; // Menyimpan nama pelanggan dalam session
        header("Location: pelanggan/master_penjualan.php"); // Ganti dengan halaman tujuan setelah login
        exit();
    } else {
        // Jika nomor telepon atau nama pelanggan tidak ditemukan
        $error = "Nomor telepon atau nama pelanggan tidak valid!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(to bottom, #1d2671, #c33764);
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background: #fff;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .form-container h2 {
        margin-bottom: 1.5rem;
        font-size: 1.8rem;
        color: #1d2671;
    }

    .form-container label {
        font-weight: bold;
        font-size: 0.9rem;
        color: #444;
        display: block;
        margin-bottom: 0.5rem;
        text-align: left;
    }

    .form-container input {
        width: 100%;
        padding: 0.9rem;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-container input:focus {
        border-color: #1d2671;
        outline: none;
        box-shadow: 0 0 5px rgba(29, 38, 113, 0.5);
    }

    .form-container button {
        width: 100%;
        padding: 0.9rem;
        background-color: #c33764;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-container button:hover {
        background-color: #e94e77;
    }

    .success,
    .error {
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .success {
        color: #1d801d;
    }

    .error {
        color: #e94e77;
    }

    a {
        color: #1d2671;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }

    .form-container .login-pelanggan-link {
        display: inline-block;
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        color: blue;
        font-weight: bold;
        border-radius: 5px;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Form Login Pelanggan</h2>

        <?php if (isset($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <label for="nomor_telepon">Nomor Telepon</label>
            <input type="text" id="nomor_telepon" name="nomor_telepon" required>

            <label for="nama_pelanggan">Nama Pelanggan</label>
            <input type="text" id="nama_pelanggan" name="nama_pelanggan" required>

            <button type="submit">Login</button>
        </form>
        <a href="index.php" class="login-pelanggan-link">Login as admin</a>

    </div>

</body>

</html>