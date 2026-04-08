<?php
session_start();
require_once '../config/database.php';

$error = "";

if (isset($_POST['register'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']); // sementara md5

    // Cek username
    $checkUsername = mysqli_query($conn, 
        "SELECT id FROM users WHERE username='$username' LIMIT 1"
    );

    // Cek email
    $checkEmail = mysqli_query($conn, 
        "SELECT id FROM users WHERE email='$email' LIMIT 1"
    );

    if (mysqli_num_rows($checkUsername) > 0) {

        $error = "Username sudah digunakan";

    } elseif (mysqli_num_rows($checkEmail) > 0) {

        $error = "Email sudah terdaftar";

    } else {

        mysqli_query($conn, "
            INSERT INTO users (username, email, password, role) 
            VALUES ('$username', '$email', '$password', 'customer')
        ");

        // Redirect langsung ke login
        header("Location: login-customer.php?registered=success");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register Customer</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    background-color: #f8f9fa;
}

.login-container {
    min-height: 100vh;
    display: flex;
    background: #fff;
    flex-direction: row-reverse; /* Flip the layout so grafic is on right */
}

/* Panel Grafik (Aslinya Kiri, sekarang Kanan) */
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
    left: -200px; /* Flip this as well */
}

/* Panel Form */
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
.btn-register {
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

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(255,164,164,0.4);
    color: white;
}

.login-link {
    color: #FFA4A4;
    text-decoration: none;
    font-weight: 500;
}

.login-link:hover {
    text-decoration: underline;
}
</style>
</head>

<body class="login-container">

<div class="left-bar border-start">
    <div class="left-content">
        <div class="mb-4">
            <i class="bi bi-person-plus" style="font-size: 3rem; opacity: 0.9;"></i>
        </div>
        <h1 class="fw-bold display-5 mb-3">Join Us Today!</h1>
        <p class="fs-6 opacity-75 fw-light px-4">Create your BuyBuy account.<br> Start a new shopping journey.</p>
    </div>
</div>

<div class="right-area">
    <div class="login-box text-center">

        <div class="mb-5 text-start">
            <h2 class="fw-bold mb-1" style="color: #333;">Create <span style="color:#FFA4A4;">Account</span></h2>
            <p class="text-muted small">Please fill out the form to register.</p>
        </div>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success" style="border-radius: 12px; font-size: 14px;">
                Register berhasil! Silakan login.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger" style="border-radius: 12px; font-size: 14px;">
                <?= $error ?>
            </div>
        <?php endif; ?>
            
        <form method="POST">

            <input 
                type="text" 
                name="username" 
                class="form-control"
                placeholder="Username"
                required
            >

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

            <button type="submit" name="register" class="btn btn-register mt-3">
                Sign Up
            </button>

        </form>

        <p class="mt-4" style="color:#666;">
            Already have an account? 
            <a href="login-customer.php" class="login-link">Sign In</a>
        </p>

    </div>
</div>

</body>
</html>