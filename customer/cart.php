<?php
session_start(); // Memulai session untuk menyimpan data user (login, cart, dll)
include "../config/database.php"; // Menghubungkan ke database

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    $_SESSION['error'] = "Please log in first !"; // Simpan pesan error
    header("Location: index.php"); // Redirect ke halaman login
    exit(); // Hentikan eksekusi
}

// Ambil data cart dari session (jika tidak ada, default array kosong)
$cart = $_SESSION['cart'] ?? [];
$total = 0; // Variabel total (belum digunakan di sini)

// Hitung total item dalam cart
$total_item = 0;
foreach($cart as $qty){
    $total_item += $qty; // Menjumlahkan semua quantity produk
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart - BuyBuy</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Jersey+20&family=Poppins:wght@300;400;500;600;700;800&family=Simonetta:wght@400;900&display=swap" rel="stylesheet">

<style>
:root{
    --primary:#FFA4A4;
    --primary-dark:#ff8f8f;
    --secondary:#fff7f7;
    --text:#222;
    --muted:#777;
    --white:#fff;
    --border:#f1d9d9;
    --shadow:0 10px 30px rgba(0,0,0,0.06);
    --radius:20px;
}

/* ===============================
   GLOBAL
================================ */
body{
    font-family:'Poppins', sans-serif;
    background:linear-gradient(to bottom, #fff8f8, #ffffff);
    color:var(--text);
    min-height:100vh;
}

a{
    text-decoration:none;
}

/* ===============================
   NAVBAR
================================ */
.navbar{
    background:linear-gradient(90deg, #FFA4A4, #ffb7b7);
    padding:18px 50px;
    box-shadow:0 8px 20px rgba(255,164,164,0.18);
}

.navbar-brand{
    font-weight:700;
    color:white !important;
    font-size:38px;
    font-family:'Simonetta', serif;
    letter-spacing:1px;
}

.nav-icon{
    color:white !important;
    font-size:22px;
    margin-left:22px;
    width:45px;
    height:45px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    background:rgba(255,255,255,0.15);
    transition:0.25s ease;
    position:relative;
}

.nav-icon:hover{
    transform:translateY(-2px);
    background:rgba(255,255,255,0.25);
}

/* Badge jumlah cart */
.cart-badge{
    position:absolute;
    top:-6px;
    right:-6px;
    background:white;
    color:var(--primary-dark);
    font-size:11px;
    font-weight:700;
    border-radius:50%;
    min-width:20px;
    height:20px;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 4px 10px rgba(0,0,0,0.12);
}

/* ===============================
   HEADER PAGE
================================ */
.page-header{
    margin-top:40px;
    margin-bottom:35px;
}

.back-link{
    width:48px;
    height:48px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:14px;
    background:var(--white);
    color:var(--text);
    box-shadow:var(--shadow);
    transition:0.2s ease;
}

.back-link:hover{
    transform:translateY(-2px);
    color:var(--primary-dark);
}

.page-title{
    font-size:30px;
    font-weight:700;
    margin-bottom:6px;
}

.page-subtitle{
    color:var(--muted);
    font-size:14px;
}

/* ===============================
   TABS
================================ */
.cart-tabs{
    display:flex;
    gap:14px;
    flex-wrap:wrap;
    justify-content:center;
    margin-top:25px;
}

.cart-tabs a{
    background:var(--white);
    color:#555;
    padding:12px 24px;
    border-radius:999px;
    font-weight:500;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
    transition:0.25s ease;
}

.cart-tabs a:hover{
    transform:translateY(-2px);
    color:var(--primary-dark);
}

.cart-tabs a.active{
    background:linear-gradient(90deg, #FFA4A4, #ffbebe);
    color:white;
}

/* ===============================
   CART WRAPPER
================================ */
.cart-wrapper{
    background:rgba(255,255,255,0.92);
    border:1px solid rgba(255,255,255,0.7);
    backdrop-filter:blur(10px);
    border-radius:28px;
    padding:28px;
    box-shadow:var(--shadow);
    margin-bottom:30px;
}

/* ===============================
   SECTION TITLE
================================ */
.section-title{
    font-size:20px;
    font-weight:700;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
}

.section-title i{
    color:var(--primary-dark);
}

/* ===============================
   EMPTY CART
================================ */
.empty-box{
    background:#fff8e8;
    border:1px solid #ffe6a8;
    color:#8b6d12;
    padding:20px;
    border-radius:18px;
    text-align:center;
    font-weight:500;
}

/* ===============================
   CART ITEM
================================ */
.cart-card{
    background:linear-gradient(to right, #fff, #fff9f9);
    border:1px solid #f8e2e2;
    border-radius:22px;
    padding:18px;
    margin-bottom:18px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    transition:0.25s ease;
    box-shadow:0 8px 20px rgba(0,0,0,0.03);
}

.cart-card:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(0,0,0,0.05);
}

.product-img{
    width:95px;
    height:95px;
    object-fit:cover;
    border-radius:18px;
    border:2px solid #fff1f1;
}

.product-name{
    font-size:17px;
    font-weight:700;
    margin-bottom:5px;
}

.product-price{
    color:var(--primary-dark);
    font-weight:600;
    font-size:15px;
}

/* ===============================
   QTY BUTTON
================================ */
.qty-form{
    background:#fff;
    border:1px solid #f0dede;
    border-radius:999px;
    padding:8px 12px;
}

.qty-btn{
    border:none;
    background:linear-gradient(90deg, #FFA4A4, #ffb8b8);
    color:white;
    width:34px;
    height:34px;
    border-radius:50%;
    font-weight:700;
    transition:0.2s ease;
}

.qty-btn:hover{
    transform:scale(1.05);
}

.qty-btn:disabled{
    opacity:0.5;
    cursor:not-allowed;
}

.qty-number{
    min-width:28px;
    text-align:center;
    font-weight:700;
    font-size:15px;
}

/* ===============================
   DELETE BUTTON
================================ */
.delete-btn{
    background:linear-gradient(90deg, #ff7b7b, #ff5d5d);
    color:white;
    border:none;
    width:42px;
    height:42px;
    border-radius:50%;
    transition:0.25s ease;
    box-shadow:0 8px 18px rgba(255,93,93,0.25);
}

.delete-btn:hover{
    transform:translateY(-2px) scale(1.03);
}

/* ===============================
   SUMMARY
================================ */
.summary-box{
    background:linear-gradient(180deg, #fff8f8, #ffffff);
    border:1px solid #f3dede;
    border-radius:24px;
    padding:24px;
    box-shadow:var(--shadow);
}

.summary-number{
    font-size:30px;
    font-weight:800;
    color:var(--primary-dark);
    margin-bottom:6px;
}

.summary-label{
    color:var(--muted);
    font-size:14px;
}

.checkout-btn{
    background:linear-gradient(90deg, #FFA4A4, #ff8f8f);
    color:white;
    border:none;
    padding:14px 28px;
    border-radius:16px;
    font-weight:700;
    transition:0.25s ease;
    box-shadow:0 12px 24px rgba(255,164,164,0.28);
}

.checkout-btn:hover{
    transform:translateY(-2px);
    color:white;
}

/* ===============================
   RESPONSIVE
================================ */
@media(max-width: 768px){
    .navbar{
        padding:16px 20px;
    }

    .navbar-brand{
        font-size:32px;
    }

    .cart-card{
        flex-direction:column;
        align-items:flex-start;
        gap:16px;
    }

    .cart-actions{
        width:100%;
        justify-content:space-between;
        flex-wrap:wrap;
    }
}
</style>
</head>

<body>

<?php 
// Ambil ulang cart dari session
$cart = $_SESSION['cart'] ?? [];

// Hitung total item menggunakan array_sum
$cart_count = array_sum($cart);
?>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="index.php">BuyBuy</a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Icon cart -->
        <a href="cart.php" class="nav-icon">
            <i class="bi bi-cart"></i>
            <?php if($cart_count > 0): ?>
                <span class="cart-badge"><?= $cart_count; ?></span>
            <?php endif; ?>
        </a>

        <!-- Logout -->
        <a href="../auth/logout.php" class="nav-icon">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</nav>

<div class="container py-4">

    <!-- HEADER -->
    <div class="page-header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <a href="index.php" class="back-link">
                <i class="bi bi-arrow-left fs-5"></i>
            </a>

            <div>
                <div class="page-title">My Cart</div>
                <div class="page-subtitle">Manage your selected products before checkout</div>
            </div>
        </div>

        <!-- TABS -->
        <div class="cart-tabs">
            <a href="cart.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">Cart</a>
            <a href="checkout.php" class="<?= basename($_SERVER['PHP_SELF']) == 'checkout.php' ? 'active' : '' ?>">Checkout</a>
            <a href="history.php" class="<?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">History Transaction</a>
        </div>
    </div>

    <div class="cart-wrapper">

        <div class="section-title">
            <i class="bi bi-cart-check-fill"></i> Shopping Cart
        </div>

        <!-- Jika cart kosong -->
        <?php if(empty($cart)): ?>
            <div class="empty-box">Cart is empty :(</div>
        <?php else: ?>

        <!-- Loop semua produk dalam cart -->
        <?php foreach($cart as $id => $qty): 

            // Ambil data produk dari database berdasarkan id
            $query = mysqli_query($conn,"SELECT * FROM products WHERE id=$id");
            $product = mysqli_fetch_assoc($query);

            // Jika produk tidak ditemukan, hapus dari cart
            if(!$product){
                unset($_SESSION['cart'][$id]);
                continue;
            }

            // Ambil stock produk
            $stock = $product['stock'];

            // Cek apakah stok habis
            $isOut = $stock <= 0;
        ?>

        <div class="cart-card">

            <!-- LEFT (Gambar + info produk) -->
            <div class="d-flex align-items-center gap-3">
                <img src="../assets/uploads/<?= $product['image']; ?>" class="product-img">

                <div>
                    <div class="product-name"><?= $product['name']; ?></div>
                    <div class="product-price">
                        Rp <?= number_format($product['price'],0,',','.'); ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT (Aksi) -->
            <div class="d-flex align-items-center gap-3 cart-actions">

                <!-- FORM UPDATE QUANTITY -->
                <form method="POST" action="../process/update-cart.php" class="d-flex align-items-center gap-2 qty-form">
                    <input type="hidden" name="product_id" value="<?= $id; ?>">

                    <!-- Tombol kurangi -->
                    <button name="decrease" class="qty-btn"
                        <?= $qty <= 1 ? 'disabled' : '' ?>>
                        -
                    </button>

                    <!-- Jumlah -->
                    <span class="qty-number"><?= $qty; ?></span>

                    <!-- Tombol tambah -->
                    <button name="increase" class="qty-btn"
                        <?= $qty >= $stock ? 'disabled' : '' ?>>
                        +
                    </button>
                </form>

                <!-- Jika stok habis -->
                <?php if($isOut): ?>
                    <span class="badge bg-danger px-3 py-2 rounded-pill">Out of Stock</span>
                <?php endif; ?>

                <!-- FORM DELETE -->
                <form method="POST" action="../process/remove-cart.php">
                    <input type="hidden" name="product_id" value="<?= $id; ?>">
                    <button class="delete-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>

            </div>

        </div>

        <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <!-- TOTAL ITEM + CHECKOUT BUTTON -->
    <?php if(!empty($cart)): ?>
    <div class="summary-box d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
        <div>
            <div class="summary-number"><?= $total_item; ?></div>
            <div class="summary-label">Total Item in Your Cart</div>
        </div>

        <!-- Tombol checkout -->
        <a href="checkout.php" class="checkout-btn">
            Proceed to Checkout
        </a>
    </div>
    <?php endif; ?>

</div>

<?php include 'notif.php'; ?>
</body>
</html>