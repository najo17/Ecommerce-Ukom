<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php"); // Redirect ke login
    exit(); // Hentikan program
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Ambil data user dari database
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Ambil data cart dari session
$cart = $_SESSION['cart'] ?? [];

/* ===============================
   HITUNG TOTAL
================================ */
$total = 0; // Inisialisasi total harga

// Loop setiap item di cart
foreach($cart as $id => $qty){

    // Ambil harga produk berdasarkan id
    $query = mysqli_query($conn,"SELECT price FROM products WHERE id=$id");
    $product = mysqli_fetch_assoc($query);

    // Hitung subtotal (harga x qty)
    $subtotal = $product['price'] * $qty;

    // Tambahkan ke total
    $total += $subtotal;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - BuyBuy</title>
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
    padding-bottom:120px;
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
}

.nav-icon:hover{
    transform:translateY(-2px);
    background:rgba(255,255,255,0.25);
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
   MAIN LAYOUT
================================ */
.checkout-layout{
    display:grid;
    grid-template-columns: 1.4fr 0.9fr;
    gap:28px;
    align-items:start;
}

.left-panel,
.right-panel{
    background:rgba(255,255,255,0.92);
    border:1px solid rgba(255,255,255,0.7);
    backdrop-filter:blur(10px);
    border-radius:28px;
    padding:28px;
    box-shadow:var(--shadow);
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
   CART ITEM
================================ */
.checkout-card{
    background:linear-gradient(to right, #fff, #fff9f9);
    border:1px solid #f8e2e2;
    border-radius:22px;
    padding:18px;
    margin-bottom:18px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    transition:0.25s ease;
}

.checkout-card:hover{
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
    font-size:16px;
    font-weight:700;
    margin-bottom:5px;
}

.product-qty{
    font-size:14px;
    color:var(--muted);
}

.product-price{
    font-size:18px;
    font-weight:700;
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
   PAYMENT METHOD
================================ */
.payment-methods{
    display:flex;
    gap:12px;
    margin-bottom:18px;
}

.method-btn{
    border:none;
    padding:12px 22px;
    border-radius:14px;
    background:#ffe0e0;
    color:#b65c5c;
    font-weight:600;
    transition:0.25s ease;
}

.method-btn:hover{
    transform:translateY(-2px);
}

.method-active{
    background:linear-gradient(90deg, #FFA4A4, #ffb7b7);
    color:white;
    box-shadow:0 8px 18px rgba(255,164,164,0.28);
}

.transfer-box{
    background:#fff5f5;
    border:1px dashed #ffb3b3;
    border-radius:18px;
    padding:16px;
    font-size:14px;
    color:#666;
    margin-bottom:24px;
}

/* ===============================
   ADDRESS BOX
================================ */
.address-box{
    background:#fffaf9;
    border:1px solid #f4dddd;
    border-radius:20px;
    padding:18px;
    min-height:110px;
    font-size:14px;
    line-height:1.7;
    color:#444;
}

.edit-address-link{
    display:inline-flex;
    align-items:center;
    gap:8px;
    margin-top:12px;
    color:var(--primary-dark);
    font-weight:600;
    font-size:14px;
}

/* ===============================
   FORM ELEMENT
================================ */
.form-label{
    font-size:14px;
    font-weight:600;
    margin-bottom:8px;
    color:#444;
}

.form-select,
.form-control{
    border-radius:14px;
    min-height:48px;
    border:1px solid #ead6d6;
    box-shadow:none !important;
}

.form-select:focus,
.form-control:focus{
    border-color:#FFA4A4;
}

/* ===============================
   SUMMARY
================================ */
.summary-card{
    background:linear-gradient(180deg, #fff8f8, #ffffff);
    border:1px solid #f3dede;
    border-radius:24px;
    padding:24px;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:16px;
    font-size:15px;
}

.summary-row span:last-child{
    font-weight:600;
}

.summary-total{
    border-top:1px dashed #e5caca;
    margin-top:16px;
    padding-top:18px;
    font-size:20px;
    font-weight:700;
}

.order-btn{
    width:100%;
    background:linear-gradient(90deg, #FFA4A4, #ff8f8f);
    color:white;
    border:none;
    padding:14px 22px;
    border-radius:16px;
    font-weight:700;
    transition:0.25s ease;
    box-shadow:0 12px 24px rgba(255,164,164,0.28);
}

.order-btn:hover{
    transform:translateY(-2px);
    color:white;
}

.order-btn-outline{
    width:100%;
    background:white;
    color:var(--primary-dark);
    border:1px solid #f3d0d0;
    padding:14px 22px;
    border-radius:16px;
    font-weight:700;
    transition:0.25s ease;
    display:inline-flex;
    justify-content:center;
    align-items:center;
}

.order-btn-outline:hover{
    color:var(--primary-dark);
    transform:translateY(-2px);
}

/* ===============================
   MODAL
================================ */
.modal-content{
    border:none;
    border-radius:24px;
    overflow:hidden;
}

.modal-header{
    background:#fff7f7;
    border-bottom:1px solid #f1d9d9;
}

.modal-title{
    font-weight:700;
}

.modal-body p{
    margin-bottom:12px;
    line-height:1.7;
}

/* ===============================
   RESPONSIVE
================================ */
@media(max-width: 992px){
    .checkout-layout{
        grid-template-columns:1fr;
    }

    .navbar{
        padding:16px 20px;
    }

    .navbar-brand{
        font-size:32px;
    }
}

@media(max-width: 576px){
    .left-panel,
    .right-panel{
        padding:20px;
        border-radius:22px;
    }

    .checkout-card{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .product-price{
        align-self:flex-end;
    }

    .payment-methods{
        flex-wrap:wrap;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="index.php">BuyBuy</a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Icon cart -->
        <a href="cart.php" class="nav-icon">
            <i class="bi bi-cart"></i>
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
                <div class="page-title">Checkout</div>
                <div class="page-subtitle">Review your items and complete your order</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="cart-tabs">
            <a href="cart.php">Cart</a>
            <a href="checkout.php" class="active">Checkout</a>
            <a href="history.php">History Transaction</a>
        </div>
    </div>

    <!-- FORM START -->
    <form method="POST" action="../process/process-checkout.php" enctype="multipart/form-data">

        <div class="checkout-layout">

            <!-- LEFT SIDE -->
            <div class="left-panel">

                <div class="section-title">
                    <i class="bi bi-bag-check-fill"></i> Order Items
                </div>

                <!-- Jika cart kosong -->
                <?php if(empty($cart)): ?>

                    <div class="empty-box">
                        Cart Is Empty
                    </div>

                <?php else: ?>

                    <!-- Loop semua item cart -->
                    <?php foreach($cart as $id => $qty): 

                        // Ambil data produk lengkap
                        $query = mysqli_query($conn,"SELECT * FROM products WHERE id=$id");
                        $product = mysqli_fetch_assoc($query);

                        // Hitung subtotal per item
                        $subtotal = $product['price'] * $qty;
                    ?>

                    <div class="checkout-card">
                        <div class="d-flex align-items-center gap-3">

                            <!-- Gambar produk -->
                            <img src="../assets/uploads/<?= $product['image']; ?>" class="product-img">

                            <!-- Info produk -->
                            <div>
                                <div class="product-name"><?= $product['name']; ?></div>
                                <div class="product-qty">Qty: <?= $qty; ?></div>
                            </div>
                        </div>

                        <!-- Subtotal -->
                        <div class="product-price">
                            Rp <?= number_format($subtotal,0,',','.'); ?>
                        </div>
                    </div>

                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <!-- RIGHT SIDE -->
            <div class="right-panel">

                <div class="section-title">
                    <i class="bi bi-credit-card-2-front-fill"></i> Payment & Shipping
                </div>

                <!-- Pilih metode pembayaran -->
                <div class="payment-methods">
                    <button type="button" id="btnTransfer" class="method-btn method-active">
                        <i class="bi bi-bank2 me-1"></i> Transfer
                    </button>

                    <button type="button" id="btnCOD" class="method-btn">
                        <i class="bi bi-truck me-1"></i> COD
                    </button>

                    <!-- Hidden input untuk kirim metode pembayaran -->
                    <input type="hidden" name="payment_method" id="paymentMethod" value="transfer">
                </div>

                <!-- Info transfer -->
                <div id="transferSection" class="transfer-box">
                    <em>Transfer Here : 087864200621 ( Via Dana )</em>
                </div>

                <!-- PHONE NUMBER -->
                <div class="mb-4">
                    <label class="form-label">Phone Number</label>
                    <div class="address-box">

                        <!-- Jika nomor telepon ada -->
                        <?php if(!empty($user['phone'])): ?>

                            <i class="bi bi-telephone-fill me-2" style="color:var(--primary-dark)"></i>
                            <strong><?= htmlspecialchars($user['phone']); ?></strong>

                        <?php else: ?>

                            <!-- Jika belum isi nomor telepon -->
                            <span class="text-danger">Fill Phone Number First</span>

                        <?php endif; ?>

                    </div>

                    <!-- Link edit phone -->
                    <a href="profile.php" class="edit-address-link">
                        <i class="bi bi-pencil-square"></i> Edit Phone
                    </a>
                </div>

                <!-- Hidden phone untuk dikirim ke server -->
                <input type="hidden" name="phone" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>">

                <!-- ADDRESS -->
                <div class="mb-4">
                    <label class="form-label">Shipping Address</label>
                    <div class="address-box">

                        <!-- Jika alamat ada -->
                        <?php if(!empty($user['address'])): ?>

                            <strong><?= htmlspecialchars(!empty($user['full_name']) ? $user['full_name'] : $user['username']); ?></strong><br>
                            <?= nl2br(htmlspecialchars($user['address'])); ?>

                        <?php else: ?>

                            <!-- Jika belum isi alamat -->
                            <span class="text-danger">Fill Address First</span>

                        <?php endif; ?>

                    </div>

                    <!-- Link edit alamat -->
                    <a href="profile.php" class="edit-address-link">
                        <i class="bi bi-pencil-square"></i> Edit Address
                    </a>
                </div>

                <!-- Hidden alamat untuk dikirim ke server -->
                <input type="hidden" name="address" value="<?= htmlspecialchars($user['address'] ?? ''); ?>">

                <!-- KURIR -->
                <div class="mb-3">
                    <label for="courier" class="form-label">Courier</label>
                    <select name="courier" id="courier" class="form-select" required>
                        <option value="">Select Courier</option>
                        <option value="JNE">JNE</option>
                        <option value="J&T">J&T</option>
                        <option value="SiCepat">SiCepat</option>
                        <option value="AnterAja">AnterAja</option>
                        <option value="Ninja Xpress">Ninja Xpress</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="shipping_service" class="form-label">Shipping Service</label>
                    <select name="shipping_service" id="shipping_service" class="form-select" required>
                        <option value="">Select Service</option>
                        <option value="Regular">Regular</option>
                        <option value="Express">Express</option>
                    </select>
                </div>

                <!-- Upload bukti pembayaran -->
                <div id="uploadSection" class="mb-4">
                    <label class="form-label">Payment Proof</label>
                    <input type="file"
                           name="payment_proof"
                           class="form-control">
                </div>

                <!-- SUMMARY -->
                <div class="summary-card">
                    <div class="section-title mb-3">
                        <i class="bi bi-receipt-cutoff"></i> Order Summary
                    </div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?= "Rp " . number_format($total,0,',','.'); ?></span>
                    </div>

                    <div class="summary-row">
                        <span>Shipping Cost</span>
                        <span id="shippingCostText">Rp 0</span>
                    </div>

                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span id="finalTotalText">Rp <?= number_format($total,0,',','.'); ?></span>
                    </div>

                    <!-- Hidden subtotal -->
                    <input type="hidden" id="subtotalValue" value="<?= $total ?>">

                    <!-- Hidden shipping cost -->
                    <input type="hidden" name="shipping_cost" id="shippingCostInput" value="0">

                    <!-- Jika alamat kosong -->
                    <?php if(empty($user['address']) || empty($user['phone'])): ?>

                        <a href="profile.php" class="order-btn-outline mt-3">
                            Fill Profile First
                        </a>

                    <?php else: ?>

                        <!-- Tombol order (trigger modal) -->
                        <button type="button" 
                                class="order-btn mt-3"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmModal">
                            Confirm Order
                        </button>

                    <?php endif; ?>
                </div>

            </div>

        </div>

        <!-- MODAL KONFIRMASI -->
        <div class="modal fade" id="confirmModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">Confirm Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">

                <!-- Tampilkan total -->
                <p><strong>Total:</strong> <span id="modalTotalText">Rp <?= number_format($total,0,',','.'); ?></span></p>

                <!-- Tampilkan metode pembayaran -->
                <p><strong>Payment Method:</strong> 
                    <span id="modalPaymentMethod">Transfer</span>
                </p>

                <!-- Tampilkan kurir -->
                <p><strong>Courier:</strong> <span id="modalCourier">-</span></p>
                <p><strong>Service:</strong> <span id="modalService">-</span></p>
                <p><strong>Shipping Cost:</strong> <span id="modalShippingCost">Rp 0</span></p>

                <!-- Tampilkan nomor telepon -->
                <p><strong>Phone:</strong><br>
                    <?= !empty($user['phone']) ? htmlspecialchars($user['phone']) : '-' ?>
                </p>

                <!-- Tampilkan alamat -->
                <p><strong>Address:</strong><br>
                    <?= !empty($user['address']) ? nl2br(htmlspecialchars($user['address'])) : '-' ?>
                </p>

              </div>

              <div class="modal-footer">

                <!-- Cancel -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <!-- Submit form -->
                <button type="submit" class="btn btn-danger">
                    Confirm Order
                </button>

              </div>

            </div>
          </div>
        </div>

    </form>
    <!-- FORM END -->

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Ambil elemen DOM
const btnTransfer = document.getElementById("btnTransfer");
const btnCOD = document.getElementById("btnCOD");
const transferSection = document.getElementById("transferSection");
const uploadSection = document.getElementById("uploadSection");
const paymentInput = document.getElementById("paymentMethod");
const modalPaymentText = document.getElementById("modalPaymentMethod");

// Kurir
const courierSelect = document.getElementById("courier");
const serviceSelect = document.getElementById("shipping_service");
const shippingCostText = document.getElementById("shippingCostText");
const shippingCostInput = document.getElementById("shippingCostInput");
const finalTotalText = document.getElementById("finalTotalText");
const modalTotalText = document.getElementById("modalTotalText");
const subtotalValue = parseInt(document.getElementById("subtotalValue").value);

// Modal info
const modalCourier = document.getElementById("modalCourier");
const modalService = document.getElementById("modalService");
const modalShippingCost = document.getElementById("modalShippingCost");

// Format rupiah
function formatRupiah(number) {
    return "Rp " + number.toLocaleString("id-ID");
}

// Hitung ongkir
function calculateShipping() {
    let courier = courierSelect.value;
    let service = serviceSelect.value;
    let shippingCost = 0;

    if (courier === "JNE" && service === "Regular") {
        shippingCost = 3000;
    } else if (courier === "JNE" && service === "Express") {
        shippingCost = 18000;
    } else if (courier === "J&T" && service === "Regular") {
        shippingCost = 4000;
    } else if (courier === "J&T" && service === "Express") {
        shippingCost = 19000;
    } else if (courier === "SiCepat" && service === "Regular") {
        shippingCost = 3000;
    } else if (courier === "SiCepat" && service === "Express") {
        shippingCost = 19000;
    } else if (courier === "AnterAja" && service === "Regular") {
        shippingCost = 4000;
    } else if (courier === "AnterAja" && service === "Express") {
        shippingCost = 17000;
    } else if (courier === "Ninja Xpress" && service === "Regular") {
        shippingCost = 7000;
    } else if (courier === "Ninja Xpress" && service === "Express") {
        shippingCost = 21000;
    }

    let finalTotal = subtotalValue + shippingCost;

    shippingCostText.innerText = formatRupiah(shippingCost);
    shippingCostInput.value = shippingCost;
    finalTotalText.innerText = formatRupiah(finalTotal);
    modalTotalText.innerText = formatRupiah(finalTotal);

    modalCourier.innerText = courier || "-";
    modalService.innerText = service || "-";
    modalShippingCost.innerText = formatRupiah(shippingCost);
}

// Saat tombol Transfer diklik
btnTransfer.addEventListener("click", function(){
    btnTransfer.classList.add("method-active");
    btnCOD.classList.remove("method-active");

    transferSection.style.display = "block";
    uploadSection.style.display = "block";

    paymentInput.value = "transfer";
    modalPaymentText.innerText = "Transfer";
});

// Saat tombol COD diklik
btnCOD.addEventListener("click", function(){
    btnCOD.classList.add("method-active");
    btnTransfer.classList.remove("method-active");

    transferSection.style.display = "none";
    uploadSection.style.display = "none";

    paymentInput.value = "cod";
    modalPaymentText.innerText = "COD";
});

// Event kurir
courierSelect.addEventListener("change", calculateShipping);
serviceSelect.addEventListener("change", calculateShipping);
</script>

<?php include 'notif.php'; ?>
</body>
</html>