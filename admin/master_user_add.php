<?php
session_start();
include 'session.php';
include '../config/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $namaLengkap = $_POST['nama_lengkap'];

    // Pastikan role defaultnya adalah 'pelanggan'
    $role = isset($_POST['role']) && !empty($_POST['role']) ? $_POST['role'] : 'kasir';

    // Query untuk menambahkan data
    $sql = "INSERT INTO users (Username, Password, NamaLengkap, Role) VALUES ('$username', '$password', '$namaLengkap', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Registrasi berhasil! Data telah ditambahkan.');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>
                alert('Registrasi gagal: " . $conn->error . "');
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi</title>
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
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .form-container h2 {
            margin-bottom: 1.5rem;
            font-size: 2rem;
            color: #1d2671;
            font-family: 'Roboto Slab', serif;
        }

        .form-container label {
            font-size: 1rem;
            font-weight: bold;
            color: #333;
            display: block;
            margin: 1rem 0 0.5rem;
            text-align: left;
        }

        .form-container input,
        .form-container select {
            border: 2px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Roboto', sans-serif;
            width: 100%;
            padding: 0.9rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease-in-out;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #1d2671;
            outline: none;
            box-shadow: 0 0 8px rgba(29, 38, 113, 0.3);
        }

        .form-container button {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(to right, #1d2671, #c33764);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-container button:hover {
            background: linear-gradient(to right, #c33764, #1d2671);
        }

        .retro {
            font-family: 'Roboto Mono', monospace;
            color: #c33764;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <form method="POST">
            <h2>Form Registrasi</h2>

            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" placeholder="Masukkan nama lengkap" required>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Masukkan username" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan password" required>

            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="admin">Admin</option>
                <option value="kasir" selected>Kasir</option>
            </select>


            <button type="submit">Daftar</button>
            <div class="retro">Pastikan semua data sudah benar sebelum mendaftar!</div>
        </form>
    </div>
</body>

</html>