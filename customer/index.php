<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Status login customer (true/false)
$is_login = isset($_SESSION['user_id']);

// Ambil kategori dari URL (query string)
$category = $_GET['category'] ?? '';

// Filter produk berdasarkan kategori jika ada
if($category == 'boy' || $category == 'girl'){
    $products = mysqli_query($conn, "SELECT * FROM products WHERE category='$category' ORDER BY id DESC");
} else {
    $products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
}

// Hitung jumlah item dalam cart hanya jika user login
$cart_count = 0;
if($is_login && isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $qty){
        $cart_count += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>BuyBuy - Home</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Jersey+20&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Simonetta:ital,wght@0,400;0,900;1,400;1,900&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    background:#fff;
    color:#333;
}

/* NAVBAR */
.navbar{
    background:#FFA4A4;
    padding:18px 0;
}

.navbar-brand{
    font-weight:700;
    color:white !important;
    font-size:35px;
    font-family:Simonetta;
}

.nav-link{
    color:white !important;
    font-weight:500;
    text-decoration:none;
    margin:0 8px;
    transition:0.2s;
}

.nav-link:hover{
    opacity:0.85;
}

.nav-icon{
    color:white !important;
    font-size:22px;
    margin-left:20px;
    position:relative;
    transition:0.2s;
    text-decoration:none;
}

.nav-icon:hover{
    transform:scale(1.08);
    opacity:0.85;
}

.cart-badge{
    position:absolute;
    top:-7px;
    right:-10px;
    background:white;
    color:#FFA4A4;
    font-size:11px;
    padding:2px 6px;
    border-radius:50%;
    font-weight:600;
    min-width:20px;
    text-align:center;
}

/* SECTION SPACING */
.hero-section{
    padding:35px 0 25px;
}

.section-space{
    padding:70px 0;
}

.section-space-sm{
    padding:45px 0;
}

/* TITLE */
.section-title{
    font-weight:700;
    color:#222;
    margin-bottom:10px;
    font-size:2.3rem;
}

.section-subtitle{
    color:#777;
    font-size:15px;
    margin-bottom:0;
}

/* HERO */
.hero-title-wrap{
    margin-bottom:30px;
}

.hero-banner-img{
    height:420px;
    object-fit:cover;
    border-radius:24px;
}

.carousel-inner{
    border-radius:24px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
}

/* WHY CARD */
.why-card{
    background:#fff;
    border-radius:24px;
    padding:30px 22px;
    text-align:center;
    height:100%;
    box-shadow:0 8px 24px rgba(0,0,0,0.05);
    transition:0.25s ease;
    border:1px solid #f5f5f5;
}

.why-card:hover{
    transform:translateY(-6px);
}

.why-icon{
    width:72px;
    height:72px;
    background:#ffe6eb;
    color:#f28b9b;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 18px;
    border-radius:50%;
    font-size:28px;
}

.why-card h5{
    font-weight:700;
    margin-bottom:10px;
}

.why-card p{
    font-size:14px;
    color:#666;
    margin-bottom:0;
}

/* PRODUCT CARD */
.product-card{
    border-radius:22px;
    overflow:hidden;
    background:white;
    box-shadow:0 8px 24px rgba(0,0,0,0.05);
    display:flex;
    flex-direction:column;
    transition:0.3s ease;
    cursor:pointer;
    height:100%;
    border:1px solid #f6f6f6;
}

.product-card:hover{
    transform:translateY(-8px);
    box-shadow:0 14px 30px rgba(0,0,0,0.08);
}

.product-image{
    height:250px;
    overflow:hidden;
    background:#fafafa;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:0.35s ease;
}

.product-card:hover .product-image img{
    transform:scale(1.06);
}

.product-info{
    background:white;
    color:#333;
    padding:16px;
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.product-title{
    font-weight:600;
    font-size:16px;
    color:#222;
}

.product-price{
    color:#FFA4A4;
    font-weight:700;
    font-size:15px;
}

.add-cart-btn{
    background:#FFA4A4;
    color:white;
    border:none;
    border-radius:12px;
    width:42px;
    height:42px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    transition:0.2s;
}

.add-cart-btn:hover{
    background:#ff8f8f;
    color:white;
}

.badge{
    border-radius:10px;
    padding:8px 10px;
    font-weight:500;
}

/* PROMO */
.promo-banner{
    background:linear-gradient(135deg,#ffd6de,#fff2f5);
    border-radius:28px;
    padding:55px 30px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.promo-banner h2{
    font-weight:800;
    color:#333;
    margin-bottom:15px;
}

.promo-banner p{
    color:#666;
    font-size:16px;
    margin-bottom:25px;
}

.btn-soft-pink{
    background:#FFA4A4;
    color:white;
    border:none;
    border-radius:50px;
    padding:12px 28px;
    font-weight:600;
    transition:0.2s;
    text-decoration:none;
    display:inline-block;
}

.btn-soft-pink:hover{
    background:#ff8f8f;
    color:white;
}

/* FOOTER */
.footer{
    background:#FFA4A4;
    color:white;
    margin-top:80px;
    padding:40px 0 24px;
}

.footer-brand{
    font-family:Simonetta;
    font-size:30px;
    font-weight:700;
}

.footer-text{
    font-size:14px;
    opacity:0.95;
}

.footer-bottom{
    font-size:13px;
    margin-top:20px;
    opacity:0.9;
}

/* RESPONSIVE */
@media (max-width:768px){
    .navbar{
        padding:14px 0;
    }

    .navbar-brand{
        font-size:30px;
    }

    .section-title{
        font-size:1.9rem;
    }

    .hero-banner-img{
        height:220px;
    }

    .product-image{
        height:190px;
    }

    .nav-link{
        margin:0 4px;
        font-size:14px;
    }

    .nav-icon{
        margin-left:12px;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-5">
    <div class="d-flex w-100 align-items-center justify-content-between">

        <!-- KIRI -->
        <div class="d-flex align-items-center">
            <a class="navbar-brand" href="index.php">BuyBuy</a>
        </div>

        <!-- KANAN -->
        <div class="d-flex align-items-center gap-2">
            <a href="index.php?category=boy" class="nav-link">Boy</a>
            <a href="index.php?category=girl" class="nav-link">Girl</a>

            <?php if($is_login): ?>
                <a href="cart.php" class="nav-icon position-relative">
                    <i class="bi bi-cart"></i>
                    <?php if($cart_count > 0): ?>
                        <span class="cart-badge"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>

                <a href="profile.php" class="nav-icon">
                    <i class="bi bi-person-circle"></i>
                </a>

                <a href="../auth/logout.php" class="nav-icon" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            <?php else: ?>
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

<!-- HERO -->
<section class="hero-section">
    <div class="container">

        <div class="text-center hero-title-wrap">
            <h2 class="section-title">What’s New?</h2>
            <p class="section-subtitle">Check out our latest collection for kids.</p>
        </div>

        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
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

<!-- WHY SHOP -->
<section class="section-space" style="background:#fff8fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Why Shop With Us</h2>
            <p class="section-subtitle">Everything your little one needs, all in one place.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-heart-fill"></i></div>
                    <h5>Soft & Comfortable</h5>
                    <p>Made with comfy materials for active little kids.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-stars"></i></div>
                    <h5>Cute & Trendy</h5>
                    <p>Stylish outfits for every happy moment.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-wallet2"></i></div>
                    <h5>Affordable Price</h5>
                    <p>Lovely fashion at prices parents will love.</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="why-card">
                    <div class="why-icon"><i class="bi bi-bag-check-fill"></i></div>
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

                    <div class="product-card h-100"
                         onclick="window.location='product-detail.php?id=<?= $row['id'] ?>'">

                        <div class="product-image">
                            <?php if (!empty($row['image'])) : ?>
                                <img src="../assets/uploads/<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                            <?php else : ?>
                                <img src="../assets/uploads/no-image.png" alt="No Image">
                            <?php endif; ?>
                        </div>

                        <div class="product-info">
                            <div>
                                <h6 class="product-title mb-1"><?= $row['name'] ?></h6>
                                <p class="product-price mb-3">
                                    Rp <?= number_format($row['price'], 0, ',', '.') ?>
                                </p>
                            </div>

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
                                        <form method="POST" action="../process/add-to-cart.php" class="m-0">
                                            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                                            <button type="submit" class="add-cart-btn" onclick="event.stopPropagation();">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="../auth/login-customer.php"
                                           class="add-cart-btn text-decoration-none"
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

<!-- PROMO -->
<section class="section-space">
    <div class="container">
        <div class="promo-banner">
            <h2>Little Fashion, Big Smiles</h2>
            <p>Made for comfort, style, and fun. Discover adorable outfits for every little adventure.</p>
            <a href="#products" class="btn-soft-pink">Start Shopping</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container text-center">
        <div class="footer-brand mb-2">BuyBuy</div>
        <p class="footer-text mb-3">Cute kids fashion for everyday happiness.</p>
        <div class="footer-bottom">© 2026 BuyBuy. All rights reserved.</div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Convert legacy session keys to new notif format
if(isset($_SESSION['success']) && !isset($_SESSION['notif'])){
    $_SESSION['notif'] = ['type' => 'success', 'message' => $_SESSION['success']];
    unset($_SESSION['success']);
}
if(isset($_SESSION['error']) && !isset($_SESSION['notif'])){
    $_SESSION['notif'] = ['type' => 'error', 'message' => $_SESSION['error']];
    unset($_SESSION['error']);
}
?>
<?php include 'notif.php'; ?>
</body>
</html>