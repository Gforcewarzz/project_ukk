<?php
session_start();
require 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['Password'])) {
            // Set session
            $_SESSION['username'] = $user['Username'];
            $_SESSION['nama_lengkap'] = $user['NamaLengkap'];
            $_SESSION['role'] = $user['Role'];

            // Redirect berdasarkan role
            if ($user['Role'] === 'admin') {
                header("Location: admin/index.php");
            } elseif ($user['Role'] === 'kasir') {
                header("Location: kasir/index.php");
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <form method="POST">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <a href="login_pelanggan.php" class="login-pelanggan-link">Login as Pelanggan</a>

    </div>
</body>

</html>