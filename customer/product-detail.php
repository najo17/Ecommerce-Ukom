<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Ambil id produk dari URL
$id = $_GET['id'];

// Ambil data produk berdasarkan id
$product = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$data = mysqli_fetch_assoc($product); // Ambil hasil query jadi array
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $data['name']; ?> - BuyBuy</title>

<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Simonetta:wght@400;900&display=swap" rel="stylesheet">

<style>
/* =========================
   GLOBAL
========================= */
body{
    font-family:'Poppins', sans-serif;
    background:linear-gradient(to bottom,#fff7f8,#ffffff);
    min-height:100vh;
}

/* =========================
   NAVBAR
========================= */
.navbar{
    background:#FFA4A4;
    padding-top:16px;
    padding-bottom:16px;
    box-shadow:0 8px 18px rgba(255,164,164,0.18);
}

.navbar-brand{
    font-weight:700;
    color:white !important;
    font-size:35px;
    font-family:'Simonetta', serif;
}

/* =========================
   BACK BUTTON
========================= */
.back-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:#9CAFAA;
    color:white;
    border:none;
    padding:11px 22px;
    border-radius:16px;
    text-decoration:none;
    font-weight:500;
    transition:0.25s ease;
    box-shadow:0 10px 18px rgba(156,175,170,0.18);
}

.back-btn:hover{
    background:#8c9d98;
    color:white;
    transform:translateY(-2px);
}

/* =========================
   DETAIL CARD
========================= */
.detail-card{
    background:white;
    border-radius:30px;
    padding:40px;
    box-shadow:0 20px 45px rgba(0,0,0,0.06);
    border:1px solid #f6e3e6;
    overflow:hidden;
}

/* =========================
   IMAGE BOX
========================= */
.product-img{
    background:linear-gradient(145deg,#fff8fa,#fff0f3);
    border-radius:28px;
    padding:28px;
    text-align:center;
    min-height:460px;
    display:flex;
    align-items:center;
    justify-content:center;
    border:1px solid #f7e1e5;
}

.product-img img{
    max-width:100%;
    max-height:380px;
    object-fit:contain;
    transition:0.35s ease;
}

.product-img:hover img{
    transform:scale(1.04);
}

/* =========================
   PRODUCT INFO
========================= */
.product-category{
    display:inline-block;
    background:#fff1f4;
    color:#ff8d9e;
    font-size:13px;
    font-weight:600;
    padding:8px 14px;
    border-radius:50px;
    margin-bottom:14px;
}

.product-title{
    font-size:36px;
    font-weight:700;
    color:#2f2f2f;
    margin-bottom:12px;
}

.price{
    font-size:32px;
    font-weight:700;
    color:#FFA4A4;
    margin-bottom:18px;
}

.stock-badge{
    font-size:14px;
    padding:10px 16px;
    border-radius:50px;
    margin-bottom:22px;
}

.product-description{
    color:#777;
    font-size:15px;
    line-height:1.9;
    margin-top:10px;
    margin-bottom:26px;
}

/* =========================
   BUTTONS
========================= */
.add-btn{
    background:#FFA4A4;
    color:white;
    border:none;
    padding:14px 28px;
    border-radius:50px;
    font-weight:600;
    font-size:15px;
    transition:0.25s ease;
    box-shadow:0 12px 24px rgba(255,164,164,0.24);
}

.add-btn:hover{
    background:#ff8f8f;
    transform:translateY(-2px);
    color:white;
}

/* =========================
   EXTRA INFO BOX
========================= */
.info-mini{
    margin-top:30px;
    display:flex;
    gap:14px;
    flex-wrap:wrap;
}

.info-chip{
    background:#fff6f8;
    border:1px solid #f5dde1;
    color:#666;
    padding:10px 16px;
    border-radius:16px;
    font-size:14px;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width:768px){
    .detail-card{
        padding:24px;
        border-radius:22px;
    }

    .product-title{
        font-size:28px;
    }

    .price{
        font-size:26px;
    }

    .product-img{
        min-height:300px;
        margin-bottom:25px;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-4 px-md-5">
    <a class="navbar-brand" href="index.php">BuyBuy</a>
</nav>

<div class="container py-5">

    <!-- Tombol kembali -->
    <div class="mb-4">
        <a href="index.php" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="detail-card">
        <div class="row align-items-center g-4">

            <!-- IMAGE -->
            <div class="col-lg-6">
                <div class="product-img">
                    <img src="../assets/uploads/<?= $data['image']; ?>" alt="<?= htmlspecialchars($data['name']); ?>">
                </div>
            </div>

            <!-- INFO -->
            <div class="col-lg-6">

                <span class="product-category">
                    <i class="bi bi-stars me-1"></i> Kids Collection
                </span>

                <!-- Nama produk -->
                <h2 class="product-title"><?= $data['name']; ?></h2>

                <!-- Harga -->
                <div class="price">
                    Rp <?= number_format($data['price'],0,',','.') ?>
                </div>

                <!-- Status stok -->
                <?php if($data['stock'] > 0): ?>
                    <span class="badge bg-success stock-badge">
                        <i class="bi bi-check-circle me-1"></i>
                        In Stock (<?= $data['stock']; ?>)
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger stock-badge">
                        <i class="bi bi-x-circle me-1"></i>
                        Out Of Stock
                    </span>
                <?php endif; ?>

                <!-- Deskripsi -->
                <p class="product-description">
                    <?= $data['description'] ?? 'No description available.' ?>
                </p>

                <!-- Add to cart -->
                <?php if($data['stock'] > 0): ?>
                    <form method="POST" action="../process/add-to-cart.php">
                        <input type="hidden" name="product_id" value="<?= $data['id']; ?>">

                        <button type="submit" class="add-btn">
                            <i class="bi bi-cart-plus me-2"></i>
                            Add To Cart
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Info tambahan visual -->
                <div class="info-mini">
                    <div class="info-chip">
                        <i class="bi bi-bag-heart me-1"></i> Cute Design
                    </div>
                    <div class="info-chip">
                        <i class="bi bi-patch-check me-1"></i> Quality Product
                    </div>
                    <div class="info-chip">
                        <i class="bi bi-truck me-1"></i> Ready to Order
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include 'notif.php'; ?>
</body>
</html>