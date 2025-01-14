<?php
session_start();
include 'session.php';
include '../config/koneksi.php';
// Periksa apakah UserID dikirim melalui GET
if (isset($_GET['UserID'])) {
    $userID = $_GET['UserID'];

    // Ambil data user berdasarkan UserID
    $stmt = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Periksa apakah data ditemukan
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('User tidak ditemukan.');
                window.location.href = 'index.php'; // Ganti dengan halaman master user Anda
              </script>";
        exit;
    }

    $stmt->close();
} else {
    echo "<script>
            alert('UserID tidak ditemukan.');
            window.location.href = 'index.php'; // Ganti dengan halaman master user Anda
          </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaLengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Jika password tidak kosong, hash password baru
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Ambil password lama dari database
        $hashedPassword = $user['Password'];
    }

    // Update data ke database
    $stmt = $conn->prepare("UPDATE users SET NamaLengkap = ?, Username = ?, Password = ?, Role = ? WHERE UserID = ?");
    $stmt->bind_param("ssssi", $namaLengkap, $username, $hashedPassword, $role, $userID);

    if ($stmt->execute()) {
        echo "<script>
                alert('Data berhasil diperbarui.');
                window.location.href = 'index.php'; // Ganti dengan halaman master user Anda
              </script>";
    } else {
        echo "<script>
                alert('Gagal memperbarui data: " . $conn->error . "');
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #4b6cb7, #182848);
            color: #fff;
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
            color: #4b6cb7;
            text-align: center;
        }

        .form-container label {
            font-size: 1rem;
            font-weight: bold;
            display: block;
            margin-bottom: 0.5rem;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1.2rem;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #4b6cb7;
            outline: none;
            box-shadow: 0 0 8px rgba(75, 108, 183, 0.3);
        }

        .form-container button {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-container button:hover {
            background: linear-gradient(to right, #182848, #4b6cb7);
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
        <h2>Edit User</h2>
        <form method="POST">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" value="<?= $user['NamaLengkap'] ?>" required>

            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?= $user['Username'] ?>" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengubah">

            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="admin" <?= $user['Role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="kasir" <?= $user['Role'] === 'kasir' ? 'selected' : '' ?>>Kasir</option>
                <option value="pelanggan" <?= $user['Role'] === 'pelanggan' ? 'selected' : '' ?>>Pelanggan</option>
            </select>

            <button type="submit">Simpan Perubahan</button>
            <div class="note">Kosongkan password jika tidak ingin mengubah.</div>
        </form>
    </div>
</body>

</html>