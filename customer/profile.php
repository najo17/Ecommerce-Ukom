<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php"); // Redirect ke login jika belum
    exit();
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Ambil data user dari database
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query); // Simpan hasil query ke array

// Jika tombol update profile ditekan
if(isset($_POST['update_profile'])){

    // Ambil dan amankan input
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    // Query update data user
    $update = mysqli_query($conn, "UPDATE users SET 
        full_name='$full_name',
        address='$address'
        WHERE id='$user_id'
    ");

    // Jika berhasil update
    if($update){
        echo "<script>alert('Profile updated successfully!'); window.location='index.php';</script>";
        exit();
    } else {
        // Jika gagal
        echo "<script>alert('Failed to update profile!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Profile - BuyBuy</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Jersey+20&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Simonetta:ital,wght@0,400;0,900;1,400;1,900&display=swap" rel="stylesheet">

<style>
/* Body */
body{
    background:#fff;
    font-family:'Poppins', sans-serif;
    min-height:100vh;
}

/* Navbar */
.navbar{
    background:#FFA4A4;
    padding-top:14px;
    padding-bottom:14px;
}

/* Brand */
.navbar-brand {
    font-weight: 700;
    color: white !important;
    font-size: 35px;
    font-family: Simonetta;
}

/* Icon navbar */
.nav-icon{
    color:white !important;
    font-size:22px;
    margin-left:20px;
    transition:0.2s;
    text-decoration:none;
}

.nav-icon:hover{
    transform:scale(1.08);
    opacity:0.85;
}

/* Wrapper atas */
.profile-top-wrapper{
    position:relative;
    margin-top:40px;
    margin-bottom:35px;
}

/* Tombol back */
.back-btn{
    position:absolute;
    left:0;
    top:0;
    font-size:22px;
}

.back-btn a{
    color:black;
    text-decoration:none;
}

/* Judul section */
.profile-heading{
    text-align:center;
}

.profile-heading h2{
    font-weight:700;
    margin-bottom:8px;
    color:#222;
}

.profile-heading p{
    color:#777;
    margin-bottom:0;
    font-size:14px;
}

/* Card profile */
.profile-card{
    background:white;
    border-radius:24px;
    padding:35px;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
    border:1px solid #f4f4f4;
}

/* Badge avatar */
.profile-avatar{
    width:80px;
    height:80px;
    border-radius:50%;
    background:#ffe6ea;
    color:#FFA4A4;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:34px;
    margin:0 auto 20px;
}

/* Label */
.form-label{
    font-weight:600;
    color:#444;
    margin-bottom:8px;
}

/* Input */
.form-control{
    border-radius:14px;
    padding:12px 15px;
    border:1px solid #e6e6e6;
    box-shadow:none !important;
    transition:0.2s ease;
}

.form-control:focus{
    border-color:#FFA4A4;
    box-shadow:0 0 0 0.2rem rgba(255,164,164,0.15) !important;
}

/* Readonly input */
.form-control[readonly]{
    background:#f8f8f8;
    color:#666;
}

/* Save button */
.save-btn{
    background:#FFA4A4;
    border:none;
    color:white;
    padding:11px 28px;
    border-radius:12px;
    font-weight:600;
    transition:0.2s;
}

.save-btn:hover{
    background:#f78d8d;
    transform:translateY(-1px);
}

/* Back button */
.back-home-btn{
    border-radius:12px;
    padding:11px 22px;
    font-weight:500;
}

/* Small note */
.profile-note{
    background:#fff7f8;
    border:1px solid #ffd9de;
    color:#666;
    border-radius:16px;
    padding:14px 16px;
    font-size:14px;
    margin-bottom:24px;
}

/* Responsive */
@media (max-width: 768px){
    .navbar{
        padding-left:20px !important;
        padding-right:20px !important;
    }

    .profile-card{
        padding:24px;
    }

    .profile-heading h2{
        font-size:28px;
    }

    .back-btn{
        position:static;
        margin-bottom:15px;
    }

    .profile-top-wrapper{
        margin-top:30px;
    }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-5">
    <a class="navbar-brand" href="index.php">BuyBuy</a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Cart -->
        <a href="cart.php" class="nav-icon">
            <i class="bi bi-cart"></i>
        </a>

        <!-- Home -->
        <a href="index.php" class="nav-icon">
            <i class="bi bi-house-door"></i>
        </a>

        <!-- Logout -->
        <a href="../auth/logout.php" class="nav-icon">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</nav>

<!-- CONTAINER -->
<div class="container py-5">

    <!-- HEADER -->
    <div class="profile-top-wrapper">
        <div class="back-btn">
            <a href="index.php">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>

        <div class="profile-heading">
            <h2>My Profile</h2>
            <p>Manage your personal information and shipping address.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <!-- CARD PROFILE -->
            <div class="profile-card">

                <!-- Avatar -->
                <div class="profile-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>

                <!-- Note -->
                <div class="profile-note">
                    Please make sure your <strong>full name</strong> and <strong>address</strong> are correct before checkout.
                </div>

                <!-- FORM UPDATE PROFILE -->
                <form method="POST">

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" readonly>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="5" required><?= htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" name="update_profile" class="save-btn">
                            <i class="bi bi-check-circle me-1"></i> Save Changes
                        </button>

                        <a href="index.php" class="btn btn-outline-secondary back-home-btn">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>