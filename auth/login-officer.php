<?php
// Memulai session agar data login officer bisa disimpan dan digunakan di halaman lain
session_start();

// Memanggil file koneksi database
require_once '../config/database.php';

// Variabel untuk menampung pesan error jika login gagal
$error = "";

// Mengecek apakah tombol login sudah ditekan
if (isset($_POST['login'])) {

    // Mengambil input username dari form lalu mengamankannya dari karakter khusus SQL
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    // Mengambil password dari form lalu mengubahnya ke format MD5
    // Agar bisa dicocokkan dengan password yang tersimpan di database
    $password = md5($_POST['password']);

    // Query untuk mencari user dengan:
    // 1. username sesuai
    // 2. password sesuai
    // 3. role harus officer
    // LIMIT 1 berarti hanya mengambil satu data saja
    $query = mysqli_query($conn, "
        SELECT * FROM users 
        WHERE username='$username' 
        AND password='$password'
        AND role='officer'
        LIMIT 1
    ");

    // Mengecek apakah data officer ditemukan
    if (mysqli_num_rows($query) > 0) {

        // Mengambil data officer dari hasil query
        $data = mysqli_fetch_assoc($query);

        // Menyimpan data officer ke dalam session
        // Session ini akan digunakan agar officer tetap login
        $_SESSION['user_id']  = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Jika login berhasil, arahkan officer ke dashboard officer
        header("Location: ../officer/index.php");

        // Menghentikan program agar tidak melanjutkan kode di bawahnya
        exit;

    } else {
        // Jika username/password salah atau role bukan officer,
        // maka tampilkan pesan error
        $error = "Username atau password salah / bukan petugas";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Menentukan karakter encoding -->
    <meta charset="UTF-8">

    <!-- Judul halaman yang tampil di tab browser -->
    <title>Login Officer</title>

    <!-- Icon website / favicon -->
    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <!-- Menghubungkan Bootstrap 5 untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Menghubungkan font Poppins dari Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Mengatur font utama untuk seluruh halaman */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f8f9fa;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            background: #fff;
        }

        /* Panel Kiri dengan Gradient */
        .left-bar {
            flex: 1;
            background: linear-gradient(135deg, #FFA4A4 0%, #FF7E7E 100%);
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        @media (min-width: 992px) {
            .left-bar { display: flex; }
            .right-area { flex: 1.2; }
        }

        .left-content {
            z-index: 1;
            text-align: center;
            padding: 40px;
        }

        .left-bar::after {
            content: '';
            position: absolute;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
            top: -200px;
            right: -200px;
        }

        /* Panel Kanan (Form) */
        .right-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #ffffff;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
        }

        /* Styling Input Form Modern */
        .form-control {
            width: 100%;
            height: 56px;
            border-radius: 16px;
            border: 2px solid #f0f0f0;
            background: #fafafa;
            padding: 0 20px;
            font-size: 15px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #FFA4A4;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(255,164,164,0.15);
            outline: none;
        }

        /* Styling Button Login Modern */
        .btn-login {
            width: 100%;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, #FFA4A4, #FF8E8E);
            color: white;
            border: none;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(255,164,164,0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255,164,164,0.4);
            color: white;
        }
    </style>
</head>

<body class="login-container">

    <!-- Bagian kiri halaman berupa bar pink dengan efek gradien -->
    <div class="left-bar">
        <div class="left-content">
            <!-- Icon/Logo opsional -->
            <div class="mb-4">
                <i class="bi bi-person-badge" style="font-size: 3rem; opacity: 0.9;"></i>
            </div>
            <h1 class="fw-bold display-5 mb-3">Welcome Back!</h1>
            <p class="fs-6 opacity-75 fw-light px-4">Secure Access for Officer.<br> Manage orders and inventory smoothly.</p>
        </div>
    </div>

    <!-- Bagian kanan untuk form login -->
    <div class="right-area">
        <div class="login-box text-center">

            <!-- Judul halaman login -->
            <div class="mb-5 text-start">
                <h2 class="fw-bold mb-1" style="color: #333;">Sign In <span style="color:#FFA4A4;">Officer</span></h2>
                <p class="text-muted small">Please enter your credentials.</p>
            </div>

            <!-- Jika ada pesan error, maka tampilkan -->
            <?php if ($error): ?>
                <div class="alert alert-danger" style="border-radius: 12px; font-size: 14px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Form login -->
            <form method="POST">
                <input 
                    type="text" 
                    name="username" 
                    class="form-control"
                    placeholder="Username" 
                    required
                >

                <input 
                    type="password" 
                    name="password" 
                    class="form-control"
                    placeholder="Password" 
                    required
                >

                <button type="submit" name="login" class="btn btn-login mt-3">
                    Sign In
                </button>
            </form>

        </div>
    </div>

</body>
</html>