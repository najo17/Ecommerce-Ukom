<?php
// Memulai session agar data login customer bisa disimpan
session_start();

// Memanggil file koneksi database
require_once '../config/database.php';

// Variabel untuk menampung pesan error jika login gagal
$error = "";

// Mengecek apakah tombol login ditekan
if (isset($_POST['login'])) {

    // Mengambil input email dari form lalu mengamankannya dari karakter khusus SQL
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Mengambil password dari form lalu mengubahnya ke format MD5
    // Agar bisa dicocokkan dengan password yang tersimpan di database
    $password = md5($_POST['password']); // sementara md5

    // Query untuk mencari data user customer berdasarkan:
    // 1. email
    // 2. password
    // 3. role harus customer
    // LIMIT 1 berarti hanya ambil 1 data saja
    $query = mysqli_query($conn, "
        SELECT * FROM users 
        WHERE email='$email' 
        AND password='$password'
        AND role='customer'
        LIMIT 1
    ");

    // Mengecek apakah data customer ditemukan
    if (mysqli_num_rows($query) > 0) {

        // Mengambil data customer dari hasil query
        $data = mysqli_fetch_assoc($query);

        // Menyimpan data customer ke dalam session
        // Session ini akan digunakan agar customer tetap login
        $_SESSION['user_id']  = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];

        // Jika login berhasil, customer diarahkan ke halaman utama customer
        header("Location: ../customer/index.php");

        // Menghentikan program agar tidak menjalankan kode di bawahnya
        exit;

    } else {
        // Jika email atau password salah, tampilkan pesan error
        $error = "Email atau password salah";
    }
}
?>  

<!DOCTYPE html>
<html lang="en">
<head>
<!-- Menentukan karakter encoding -->
<meta charset="UTF-8">

<!-- Judul halaman yang tampil di tab browser -->
<title>Login Customer</title>

<!-- Icon website / favicon -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Menghubungkan Bootstrap 5 -->
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

/* Styling link Sign Up */
.signup-link {
    color: #FFA4A4;
    text-decoration: none;
    font-weight: 500;
}

/* Efek saat link Sign Up disentuh mouse */
.signup-link:hover {
    text-decoration: underline;
}
</style>
</head>

<body class="login-container">

<!-- Bagian kiri halaman berupa bar pink dengan efek gradien -->
<div class="left-bar">
    <div class="left-content">
        <!-- Icon/Logo opsional -->
        <div class="mb-4">
            <i class="bi bi-bag-heart" style="font-size: 3rem; opacity: 0.9;"></i>
        </div>
        <h1 class="fw-bold display-5 mb-3">Welcome to BuyBuy!</h1>
        <p class="fs-6 opacity-75 fw-light px-4">Discover products you love.<br> Secure login for customers.</p>
    </div>
</div>

<!-- Bagian kanan untuk form login -->
<div class="right-area">
    <div class="login-box text-center">

        <!-- Judul halaman login -->
        <div class="mb-5 text-start">
            <h2 class="fw-bold mb-1" style="color: #333;">Sign In to <span style="color:#FFA4A4;">BuyBuy</span></h2>
            <p class="text-muted small">Please enter your email and password.</p>
        </div>

        <!-- Jika ada pesan error, maka tampilkan -->
        <?php if ($error): ?>
            <div class="alert alert-danger" style="border-radius: 12px; font-size: 14px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Form login customer -->
        <form method="POST">

            <input 
                type="email" 
                name="email" 
                class="form-control"
                placeholder="Email Address"
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

        <!-- Link ke halaman register jika customer belum punya akun -->
        <p class="mt-4" style="color:#666;">
            Don’t have an account? 
            <a href="register-customer.php" class="signup-link">Sign Up</a>
        </p>

    </div>
</div>

</body>
</html>