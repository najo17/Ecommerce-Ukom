<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Status login customer (true/false)
$is_login = isset($_SESSION['user_id']);

// Ambil kategori dari URL (query string)
$category = $_GET['category'] ?? '';

// Filter produk berdasarkan kategori jika ada
if($category == 'boy' || $category == 'girl'){
    // Ambil produk sesuai kategori
    $products = mysqli_query($conn, "SELECT * FROM products WHERE category='$category' ORDER BY id DESC");
} else {
    // Ambil semua produk
    $products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
}

// Hitung jumlah item dalam cart hanya jika user login
$cart_count = 0;
if($is_login && isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $qty){
        $cart_count += $qty; // Jumlahkan semua qty
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"> <!-- Encoding -->
<title>BuyBuy - Home</title>

<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Jersey+20&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Simonetta:ital,wght@0,400;0,900;1,400;1,900&display=swap" rel="stylesheet">

<style>
/* Font & background */
body{ 
    font-family: 'Poppins', sans-serif; 
    background:#ffff; 
}

/* Navbar */
.navbar{ 
    background:#FFA4A4; 
}

/* Logo */
.navbar-brand {
    font-weight: 700;
    color: white !important;
    font-size: 35px;
    font-family: Simonetta;
}

/* Link kategori */
.nav-link {
    color: white !important;
    font-weight: 400;
    text-decoration: none;
    padding: 0 px;
    margin-top: 5px;
    margin-left: -20px; 
    margin-right: 40px;  
}

/* Icon navbar */
.nav-icon {
    font-size: 22px;
    color: white;
    position: relative;
    margin-right: 10px;
    transition: 0.3s;
}

.nav-icon:hover {
    transform: scale(1.15);
    opacity: 0.8;
}

/* Redefine nav-icon */
.nav-icon{
    color:white !important;
    font-size:22px;
    margin-left:20px;
    transition:0.2s;
}

.nav-icon:hover{
    transform:scale(1.1);
    opacity:0.85;
}

/* Badge jumlah cart */
.cart-badge {
    position: absolute;
    top: -5px;
    right: -10px;
    background: white;
    color: #FFA4A4;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 50%;
}

/* Hero section */
.hero {
    min-height: 90vh;
    display: flex;
    align-items: center;
}

/* Judul hero */
.hero h1 {
    font-size: 60px;
    font-weight: 600;
}

/* Warna */
.pink { color: #FFA4A4; }
.gray { color: #444; }

/* Tombol start */
.start-btn {
    width: 300px;
    height: 80px;
    border-radius: 40px;
    font-size: 20px;
    background: #9CAFAA;
    border: none;
    color: white;
    margin-top: 50px;
}

/* Card produk */
.product-card {
    border-radius: 20px;
    overflow: hidden;   
    background: white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    cursor: pointer;
}

/* Hover card */
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
}

/* Info produk */
.product-info {
    background: #FFA4A4;
    color: white;
    padding: 10px;
    margin: 0;
}

/* Gambar produk */
.product-image {
    height: 220px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Zoom gambar saat hover */
.product-card:hover .product-image img {
    transform: scale(1.08);
}

/* Tombol add to cart */
.add-cart-btn {
    background: white;
    color: #FFA4A4;
    border: none;
    border-radius: 20px;
    padding: 5px 15px;
    float: right;
}

/* Carousel */
.carousel-img {
    height: 400px;
    object-fit: cover;
    border-radius: 20px;
}

/* Indicator carousel */
.carousel-indicators [data-bs-target] {
    background-color: #FFA4A4;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

/* Animasi carousel */
.carousel-item {
    transition: transform 0.8s ease-in-out, opacity 0.8s ease-in-out;
}

/* Footer */
.footer {
    background: #FFA4A4;
    color: white;
    margin-top: 80px;
    padding: 30px 0 20px;
}

/* Brand footer */
.footer-brand {
    font-family: Simonetta;
    font-size: 30px;
    font-weight: 700;
}

/* Text footer */
.footer-text {
    font-size: 14px;
    opacity: 0.95;
}

/* Link footer */
.footer-links a {
    color: white;
    text-decoration: none;
    margin: 0 12px;
    font-size: 15px;
    transition: 0.3s;
}

.footer-links a:hover {
    opacity: 0.8;
    text-decoration: underline;
}

/* Icon footer */
.footer-icons a {
    color: white;
    font-size: 20px;
    margin: 0 8px;
    transition: 0.3s;
}

.footer-icons a:hover {
    transform: translateY(-2px);
    opacity: 0.8;
}

/* Copyright */
.footer-bottom {
    font-size: 13px;
    margin-top: 20px;
    opacity: 0.9;
}
    /* MINI BANNER */
    .mini-banner-card {
        border-radius: 20px;
        padding: 30px 25px;
        color: white;
        height: 100%;
        transition: 0.3s ease;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .mini-banner-card:hover {
        transform: translateY(-5px);
    }

    .mini-banner-1 {
        background: linear-gradient(135deg, #f8a5c2, #fbc2d4);
    }

    .mini-banner-2 {
        background: linear-gradient(135deg, #f6b93b, #f8c291);
    }

    .mini-banner-3 {
        background: linear-gradient(135deg, #82ccdd, #b8e994);
    }

    .mini-banner-card h4 {
        font-weight: 700;
        margin-bottom: 10px;
    }

    .mini-banner-card p {
        font-size: 14px;
        margin-bottom: 18px;
    }

    /* WHY SHOP */
    .why-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px 20px;
        text-align: center;
        height: 100%;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        transition: 0.3s ease;
    }

    .why-card:hover {
        transform: translateY(-5px);
    }

    .why-icon {
        width: 70px;
        height: 70px;
        background: #ffe6eb;
        color: #f28b9b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        border-radius: 50%;
        font-size: 28px;
    }

    .why-card h5 {
        font-weight: 700;
        margin-bottom: 10px;
    }

    .why-card p {
        font-size: 14px;
        color: #666;
        margin-bottom: 0;
    }

    /* PROMO BANNER */
    .promo-banner {
        background: linear-gradient(135deg, #ffd6de, #fff2f5);
        border-radius: 25px;
        padding: 50px 30px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }

    .promo-banner h2 {
        font-weight: 800;
        color: #333;
        margin-bottom: 15px;
    }

    .promo-banner p {
        color: #666;
        font-size: 16px;
        margin-bottom: 25px;
    }

    .btn-soft-pink {
        background-color: #f28b9b;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 10px 24px;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn-soft-pink:hover {
        background-color: #e9788b;
        color: white;
    }

    .section-title {
        font-weight: 700;
        color: #222;
    }

    .section-subtitle {
        color: #777;
        font-size: 15px;
        margin-bottom: 0;
    }

    /* JARAK UMUM SECTION */
    .section-space {
        padding-top: 60px;
        padding-bottom: 60px;
    }

    .section-space-sm {
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .section-space-top {
        padding-top: 40px;
    }

    .section-space-bottom {
        padding-bottom: 40px;
    }

    /* KHUSUS HERO / WHAT'S NEW AGAR TIDAK TERLALU JAUH DARI NAVBAR */
    .hero-section {
        padding-top: 35px;
        padding-bottom: 40px;
    }

    /* JUDUL SECTION */
    .section-title {
        font-weight: 700;
        color: #222;
        margin-bottom: 10px;
    }

    .section-subtitle {
        color: #777;
        font-size: 15px;
        margin-bottom: 0;
    }
     /* HERO SECTION AGAR NAIK */
    .hero-section {
        padding-top: 25px;
        padding-bottom: 35px;
    }

    .hero-title-wrap {
        margin-bottom: 30px;
    }

    .section-title {
        font-weight: 700;
        color: #222;
        margin-bottom: 10px;
        font-size: 2.5rem;
    }

    .section-subtitle {
        color: #777;
        font-size: 15px;
        margin-bottom: 0;
    }

    .hero-banner-img {
        height: 400px;
        object-fit: cover;
        border-radius: 24px;
    }

    /* JARAK UMUM SECTION */
    .section-space {
        padding-top: 90px;
        padding-bottom: 70px;
    }

    .section-space-sm {
        padding-top: 60px;
        padding-bottom: 40px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .hero-section {
            padding-top: 20px;
            padding-bottom: 25px;
        }

        .hero-title-wrap {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 2rem;
        }

        .hero-banner-img {
            height: 220px;
        }
    }
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-5">
    <div class="d-flex w-100 align-items-center justify-content-between">
        
        <!-- KIRI: Logo -->
        <div class="d-flex align-items-center">
            <a class="navbar-brand" href="index.php">BuyBuy</a>
        </div>

        <!-- KANAN -->
        <div class="d-flex align-items-center gap-4">

            <!-- Filter kategori -->
            <a href="index.php?category=boy" class="nav-link">Boy</a>
            <a href="index.php?category=girl" class="nav-link">Girl</a>

            <?php if($is_login): ?>
                <!-- CART -->
                <a href="cart.php" class="nav-icon position-relative">
                    <i class="bi bi-cart"></i>

                    <!-- Badge jumlah item -->
                    <?php if($cart_count > 0): ?>
                        <span class="cart-badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>

                <!-- PROFILE -->
                <a href="profile.php" class="nav-icon">
                    <i class="bi bi-person-circle"></i>
                </a>

                <!-- LOGOUT -->
                <a href="../auth/logout.php" class="nav-icon" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </a>

            <?php else: ?>
                <!-- Jika belum login -->
                <a href="../auth/login-customer.php" class="nav-icon position-relative" title="Login to shop">
                    <i class="bi bi-cart"></i>
                </a>

                <a href="../auth/login-customer.php" class="nav-icon" title="Login first">
                    <i class="bi bi-person-circle"></i>
                </a>

                <a href="../auth/login-customer.php" class="nav-icon" title="Login">
                    <i class="bi bi-box-arrow-in-right"></i>
                </a>
            <?php endif; ?>
        </div>

    </div>
</nav>

<!-- HERO / BANNER UTAMA -->
<section class="hero-section">
    <div class="container">

        <!-- Judul -->
        <div class="text-center hero-title-wrap">
            <h2 class="section-title">What’s New?</h2>
            <p class="section-subtitle">Check out our latest collection for kids.</p>
        </div>

        <!-- Banner -->
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner rounded-4 overflow-hidden shadow-sm">
                <div class="carousel-item active">
                    <img src="../assets/uploads/carousel1.png" class="d-block w-100 hero-banner-img" alt="Banner 1">
                </div>
                <div class="carousel-item">
                    <img src="../assets/uploads/carousel2.png" class="d-block w-100 hero-banner-img" alt="Banner 2">
                </div>
                <div class="carousel-item">
                    <img src="../assets/uploads/carousel3.png" class="d-block w-100 hero-banner-img" alt="Banner 3">
                </div>
            </div>
        </div>

    </div>
</section>


<!-- WHY SHOP WITH US -->
<section class="section-space" style="background-color: #fff8fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Why Shop With Us</h2>
            <p class="section-subtitle">Everything your little one needs, all in one place.</p>
        </div>
        <div class="row g-4">

            <div class="col-md-3 col-sm-6">
                <div class="why-card">
                    <div class="why-icon">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <h5>Soft & Comfortable</h5>
                    <p>Made with comfy materials for active little kids.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="why-card">
                    <div class="why-icon">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h5>Cute & Trendy</h5>
                    <p>Stylish outfits for every happy moment.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="why-card">
                    <div class="why-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h5>Affordable Price</h5>
                    <p>Lovely fashion at prices parents will love.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="why-card">
                    <div class="why-icon">
                        <i class="bi bi-bag-check-fill"></i>
                    </div>
                    <h5>Easy Shopping</h5>
                    <p>Simple ordering and secure checkout process.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- INFO GUEST -->
<?php if(!$is_login): ?>
<div class="container mb-4">
    <div class="alert alert-light border text-center shadow-sm rounded-4">
        <i class="bi bi-eye me-2"></i>
        You are browsing as a guest. Please 
        <a href="../auth/login-customer.php" class="fw-semibold text-decoration-none" style="color:#FFA4A4;">
            login
        </a> 
        to shop.
    </div>
</div>
<?php endif; ?>

<!-- LIST PRODUK -->
<section class="section-space-sm" id="products">
    <div class="container">

        <div class="row g-4">
            <?php while($row = mysqli_fetch_assoc($products)) : ?>
                <div class="col-6 col-md-4 col-lg-3">

                    <!-- Card Produk -->
                    <div class="product-card h-100"
                         onclick="window.location='product-detail.php?id=<?= $row['id'] ?>'">

                        <!-- Gambar Produk -->
                        <div class="product-image">
                            <?php if (!empty($row['image'])) : ?>
                                <img src="../assets/uploads/<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                            <?php else : ?>
                                <img src="../assets/uploads/no-image.png" alt="No Image">
                            <?php endif; ?>
                        </div>

                        <!-- Info Produk -->
                        <div class="product-info">
                            <h6 class="product-title mb-1"><?= $row['name'] ?></h6>

                            <!-- Harga -->
                            <p class="product-price mb-2">
                                Rp <?= number_format($row['price'], 0, ',', '.') ?>
                            </p>

                            <!-- Bagian bawah card -->
                            <div class="d-flex justify-content-between align-items-center mt-auto">

                                <?php if ($row['stock'] <= 0): ?>
                                    <span class="badge bg-danger">Out Of Stock</span>

                                    <button class="add-cart-btn" disabled onclick="event.stopPropagation();">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>

                                <?php else: ?>
                                    <span class="badge bg-success">
                                        Stock: <?= $row['stock'] ?>
                                    </span>

                                    <?php if ($is_login): ?>
                                        <!-- Form Add to Cart -->
                                        <form method="POST" action="../process/add-to-cart.php" class="m-0">
                                            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">

                                            <button type="submit" class="add-cart-btn"
                                                onclick="event.stopPropagation();">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Jika belum login -->
                                        <a href="../auth/login-customer.php"
                                           class="add-cart-btn text-decoration-none d-inline-flex justify-content-center align-items-center"
                                           onclick="event.stopPropagation();">
                                            <i class="bi bi-cart-plus"></i>
                                        </a>
                                    <?php endif; ?>

                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>

    </div>
</section>

    </div>
</div>
<!-- PROMO BANNER -->
<section class="section-space">
    <div class="container">
        <div class="promo-banner">
            <h2>Little Fashion, Big Smiles</h2>
            <p>Made for comfort, style, and fun. Discover adorable outfits for every little adventure.</p>
            <a href="#products" class="btn btn-soft-pink">
                Start Shopping
            </a>
        </div>
    </div>
</section>

<!-- TOAST SUCCESS -->
<?php if(isset($_SESSION['success'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-4">
    <div id="cartToast" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>
                <?= $_SESSION['success']; ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['success']); endif; ?>

<!-- TOAST ERROR -->
<?php if(isset($_SESSION['error'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-4">
    <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?= $_SESSION['error']; ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['error']); endif; ?>

<!-- FOOTER -->
<footer class="footer">
    <div class="container text-center">

        <!-- Brand -->
        <div class="footer-brand mb-2">BuyBuy</div>

        <!-- Deskripsi -->
        <p class="footer-text mb-3">Cute kids fashion for everyday happiness.</p>

        <!-- Copyright -->
        <div class="footer-bottom">
            © 2026 BuyBuy. All rights reserved.
        </div>

    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Jalankan saat halaman selesai load
document.addEventListener("DOMContentLoaded", function () {

    // Toast success
    var successToast = document.getElementById('cartToast');
    if(successToast){
        var toast = new bootstrap.Toast(successToast, { delay: 2000 });
        toast.show();
    }

    // Toast error
    var errorToast = document.getElementById('errorToast');
    if(errorToast){
        var toast2 = new bootstrap.Toast(errorToast, { delay: 2000 });
        toast2.show();
    }

});
</script>
</body>
</html>
