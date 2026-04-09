<?php
session_start(); // Memulai session
include '../config/database.php'; // Koneksi ke database

// Cek apakah user sudah login
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php"); // Redirect ke login
    exit(); // Hentikan eksekusi
}

// Ambil id customer dari session
$customer_id = $_SESSION['user_id'];

/* AMBIL HISTORY TRANSAKSI CUSTOMER */
$transactions = mysqli_query($conn, "
    SELECT 
        t.*, 
        GROUP_CONCAT(
            CONCAT(
                COALESCE(s.product_name, 'Produk'),
                ' (x', COALESCE(s.quantity, 0), ')~~',
                COALESCE(s.subtotal, 0)
            ) SEPARATOR '##'
        ) AS products
    FROM transactions t
    LEFT JOIN sales s ON t.id = s.transaction_id
    WHERE t.customer_id = $customer_id
    GROUP BY t.id
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - BuyBuy</title>
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
            padding-bottom:60px;
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
           HISTORY WRAPPER
        ================================ */
        .history-wrapper{
            background:rgba(255,255,255,0.92);
            border:1px solid rgba(255,255,255,0.7);
            backdrop-filter:blur(10px);
            border-radius:28px;
            padding:28px;
            box-shadow:var(--shadow);
            margin-bottom:30px;
        }

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
           EMPTY
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
           TABLE
        ================================ */
        .table-card{
            background:#fff;
            border-radius:24px;
            overflow:hidden;
            border:1px solid #f2dede;
        }

        .table{
            margin-bottom:0;
        }

        .table thead th{
            background:linear-gradient(90deg, #FFA4A4, #ffbebe) !important;
            color:white !important;
            text-align:center;
            vertical-align:middle;
            border:none !important;
            padding:18px 14px;
            font-size:14px;
            font-weight:600;
            white-space:nowrap;
        }

        .table tbody td{
            text-align:center;
            vertical-align:middle;
            border-color:#f5e2e2 !important;
            padding:18px 14px;
            font-size:14px;
        }

        .table tbody tr:hover{
            background:#fff9f9;
        }

        .transaction-id{
            font-weight:700;
            color:var(--primary-dark);
        }

        .product-list{
            text-align:left;
            font-size:14px;
            line-height:1.8;
            min-width:220px;
        }

        .product-item{
            background:#fff6f6;
            border:1px solid #f6dfdf;
            padding:6px 10px;
            border-radius:12px;
            margin-bottom:8px;
            display:inline-block;
            width:100%;
        }

        .date-badge{
            background:#fff4f4;
            color:#cc6e6e;
            padding:8px 12px;
            border-radius:999px;
            font-weight:600;
            font-size:13px;
            display:inline-block;
        }

        .total-text{
            font-weight:700;
            color:#222;
        }

        .payment-pill{
            background:#fff2f2;
            color:#d76f6f;
            padding:8px 14px;
            border-radius:999px;
            font-weight:600;
            display:inline-block;
            font-size:13px;
        }

        /* ===============================
           ACTION BUTTON
        ================================ */
        .action-box{
            width:45px;
            height:45px;
            border-radius:14px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            cursor:pointer;
            transition:0.25s ease;
            box-shadow:0 8px 18px rgba(99,199,138,0.18);
        }

        .action-box:hover{
            transform:translateY(-2px) scale(1.03);
        }

        .action-view{
            background:linear-gradient(90deg, #63C78A, #4fb777);
        }

        /* ===============================
           MODAL
        ================================ */
        .modal-content{
            border:none;
            border-radius:28px;
            overflow:hidden;
        }

        .modal-header{
            background:linear-gradient(135deg,#FFA4A4,#FF7E7E);
            color:white;
            border:none;
            padding:20px 24px;
        }

        .modal-title{
            font-weight:700;
        }

        .receipt-box{
            background:linear-gradient(to bottom, #ffffff, #fff9f9);
            border:1px solid #f0dddd !important;
            border-radius:24px !important;
        }

        .receipt-store{
            font-size:22px;
            font-weight:800;
            color:var(--primary-dark);
            font-family:'Simonetta', serif;
        }

        .receipt-row{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:12px;
            gap:15px;
            font-size:14px;
        }

        .receipt-row span:first-child{
            color:#666;
            font-weight:500;
        }

        .receipt-row span:last-child{
            text-align:right;
            font-weight:600;
        }

        .receipt-product-item{
            background:#fff7f7;
            border:1px solid #f1dede;
            border-radius:14px;
            padding:10px 12px;
            margin-bottom:10px;
        }

        .receipt-footer-note{
            color:#999;
            font-size:13px;
        }

        #proofArea img{
            max-height:300px;
            object-fit:contain;
        }

        #customerActionArea .btn{
            border-radius:14px;
            padding:10px 18px;
            font-weight:600;
        }

        /* ===============================
           RESPONSIVE
        ================================ */
        @media(max-width: 992px){
            .navbar{
                padding:16px 20px;
            }

            .navbar-brand{
                font-size:32px;
            }
        }

        @media(max-width: 768px){
            .history-wrapper{
                padding:20px;
                border-radius:22px;
            }

            .table thead th,
            .table tbody td{
                font-size:13px;
                padding:14px 10px;
            }

            .product-list{
                min-width:180px;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="index.php">BuyBuy</a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Cart -->
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
                <div class="page-title">Transaction History</div>
                <div class="page-subtitle">Track your orders and view your transaction receipts</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="cart-tabs">
            <a href="cart.php">Cart</a>
            <a href="checkout.php">Checkout</a>
            <a href="history.php" class="active">History Transaction</a>
        </div>
    </div>

    <div class="history-wrapper">

        <div class="section-title">
            <i class="bi bi-clock-history"></i> Order History
        </div>

        <!-- Jika tidak ada transaksi -->
        <?php if(mysqli_num_rows($transactions) == 0): ?>
            <div class="empty-box">No Transactions Yet</div>
        <?php else: ?>

        <!-- Tabel transaksi -->
        <div class="table-responsive table-card">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Products</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>

                <tbody>
                <!-- Loop semua transaksi -->
                <?php while($row = mysqli_fetch_assoc($transactions)) : ?>
                    <tr>

                        <!-- ID transaksi -->
                        <td>
                            <span class="transaction-id">#<?= $row['id'] ?></span>
                        </td>

                        <!-- List produk -->
                        <td class="product-list">
                            <?php
                            if(!empty($row['products'])){
                                $items = explode('##', $row['products']);

                                foreach($items as $item){
                                    $parts = explode('~~', $item);
                                    $nameQty = $parts[0];
                                    echo '<div class="product-item">• ' . htmlspecialchars($nameQty) . '</div>';
                                }
                            } else {
                                echo '<span class="text-muted">No products</span>';
                            }
                            ?>
                        </td>

                        <!-- Tanggal -->
                        <td>
                            <span class="date-badge"><?= date('d/m/Y', strtotime($row['created_at'])) ?></span>
                        </td>

                        <!-- Total -->
                        <td class="total-text">Rp <?= number_format($row['total']) ?></td>

                        <!-- Metode pembayaran -->
                        <td>
                            <span class="payment-pill"><?= ucfirst($row['payment_method']) ?></span>
                        </td>

                        <!-- Status -->
                        <td>
                            <?php
                            $status = strtolower($row['status']);

                            if($status == 'approved'){
                                echo '<span class="badge bg-success px-3 py-2 rounded-pill">Approved</span>';
                            } elseif($status == 'pending'){
                                echo '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>';
                            } elseif($status == 'cancel_requested'){
                                echo '<span class="badge bg-secondary px-3 py-2 rounded-pill">Cancel Requested</span>';
                            } elseif($status == 'cancelled'){
                                echo '<span class="badge bg-danger px-3 py-2 rounded-pill">Cancelled</span>';
                            } elseif($status == 'refund_requested'){
                                echo '<span class="badge bg-info text-dark px-3 py-2 rounded-pill">Refund Requested</span>';
                            } elseif($status == 'refunded'){
                                echo '<span class="badge bg-primary px-3 py-2 rounded-pill">Refunded</span>';
                            } else {
                                echo '<span class="badge bg-dark px-3 py-2 rounded-pill">Unknown</span>';
                            }
                            ?>
                        </td>

                        <!-- Tombol lihat detail -->
                        <td>
                            <button type="button" class="action-box action-view border-0"
                                data-bs-toggle="modal"
                                data-bs-target="#receiptModal"
                                data-id="<?= htmlspecialchars($row['id']) ?>"
                                data-customer="<?= htmlspecialchars($row['customer_name']) ?>"
                                data-total="<?= htmlspecialchars($row['total']) ?>"
                                data-payment="<?= htmlspecialchars($row['payment_method']) ?>"
                                data-status="<?= htmlspecialchars($row['status']) ?>"
                                data-proof="<?= htmlspecialchars($row['payment_proof']) ?>"
                                data-date="<?= htmlspecialchars($row['created_at']) ?>"
                                data-address="<?= htmlspecialchars($row['shipping_address']) ?>"
                                data-products="<?= htmlspecialchars($row['products']) ?>"
                                data-courier="<?= htmlspecialchars($row['courier'] ?? '') ?>"
                                data-service="<?= htmlspecialchars($row['shipping_service'] ?? '') ?>"
                                data-shipping="<?= htmlspecialchars($row['shipping_cost'] ?? 0) ?>"
                                data-phone="<?= htmlspecialchars($row['shipping_phone'] ?? '') ?>">

                                <i class="bi bi-eye"></i>
                            </button>
                        </td>

                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>
    </div>
</div>

<!-- MODAL DETAIL TRANSAKSI -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header modal -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt-cutoff me-2"></i>Transaction Receipt
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body modal -->
            <div class="modal-body p-4">
                <div class="receipt-box p-4">

                    <!-- Judul -->
                    <h6 class="text-center receipt-store mb-3">BuyBuy Store</h6>
                    <hr>

                    <!-- Data transaksi -->
                    <div class="receipt-row">
                        <span>ID</span><span id="r-id"></span>
                    </div>

                    <div class="receipt-row">
                        <span>Customer</span><span id="r-customer"></span>
                    </div>

                    <div class="receipt-row">
                        <span>Date</span><span id="r-date"></span>
                    </div>

                    <!-- Phone -->
                    <div class="mb-3 mt-2">
                        <span class="fw-semibold">Phone</span>
                        <div id="r-phone" class="small text-muted mt-2"></div>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3 mt-2">
                        <span class="fw-semibold">Address</span>
                        <div id="r-address" class="small text-muted mt-2"></div>
                    </div>

                    <hr>

                    <!-- Produk -->
                    <div>
                        <strong>Products:</strong>
                        <div id="r-products" class="mt-3 small"></div>
                    </div>

                    <hr>

                    <!-- Shipping Info -->
                    <div class="receipt-row">
                        <span>Courier</span><span id="r-courier"></span>
                    </div>

                    <div class="receipt-row">
                        <span>Service</span><span id="r-service"></span>
                    </div>

                    <div class="receipt-row">
                        <span>Shipping Cost</span><span>Rp <span id="r-shipping"></span></span>
                    </div>

                    <hr>

                    <!-- Payment & Status -->
                    <div class="receipt-row">
                        <span>Payment</span><span id="r-payment"></span>
                    </div>

                    <div class="receipt-row">
                        <span>Status</span><span id="r-status"></span>
                    </div>

                    <!-- Subtotal Barang -->
                    <div class="receipt-row mt-3">
                        <span>Subtotal Products</span>
                        <span>Rp <span id="r-subtotal-products"></span></span>
                    </div>

                    <!-- Total -->
                    <div class="receipt-row mt-2 fs-5 fw-bold">
                        <span>Total</span>
                        <span>Rp <span id="r-total"></span></span>
                    </div>

                    <!-- Bukti pembayaran -->
                    <div id="proofArea" class="text-center mt-4"></div>

                    <!-- Tombol aksi customer -->
                    <div id="customerActionArea" class="text-center mt-4"></div>

                    <p class="text-center receipt-footer-note mt-4">
                        Thank you for shopping
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
var receiptModal = document.getElementById('receiptModal');

receiptModal.addEventListener('show.bs.modal', function (event) {

    var button = event.relatedTarget;

    // Isi data dasar
    document.getElementById('r-id').innerText = button.getAttribute('data-id') || '-';
    document.getElementById('r-customer').innerText = button.getAttribute('data-customer') || '-';

    // Format total
    const total = parseInt(button.getAttribute('data-total')) || 0;
    const shipping = parseInt(button.getAttribute('data-shipping') || 0) || 0;
    const subtotalProducts = total - shipping;

    document.getElementById('r-total').innerText =
        new Intl.NumberFormat('id-ID').format(total);

    document.getElementById('r-shipping').innerText =
        new Intl.NumberFormat('id-ID').format(shipping);

    document.getElementById('r-subtotal-products').innerText =
        new Intl.NumberFormat('id-ID').format(subtotalProducts);

    // Payment
    let payment = button.getAttribute('data-payment') || '-';
    document.getElementById('r-payment').innerText = payment.toUpperCase();

    // Courier & Service
    let courier = button.getAttribute('data-courier') || '-';
    let service = button.getAttribute('data-service') || '-';

    document.getElementById('r-courier').innerText = courier;
    document.getElementById('r-service').innerText = service;

    // Date
    let rawDate = button.getAttribute('data-date');
    document.getElementById('r-date').innerText =
        rawDate ? new Date(rawDate).toLocaleDateString('id-ID') : '-';

    // Phone
    document.getElementById('r-phone').innerText =
        button.getAttribute('data-phone') || '-';

    // Address
    document.getElementById('r-address').innerText =
        button.getAttribute('data-address') || '-';

    // Products
    let products = button.getAttribute('data-products');
    let productArea = document.getElementById('r-products');

    if(products){
        let list = products.split('##');

        productArea.innerHTML = list.map(item => {
            let parts = item.split('~~');
            let nameQty = parts[0];
            let subtotal = parts[1] ? parts[1] : 0;

            return `
            <div class="receipt-product-item d-flex justify-content-between">
                <span>${nameQty}</span>
                <span>Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</span>
            </div>
            `;
        }).join('');
    } else {
        productArea.innerHTML = `<span class="text-muted">No products</span>`;
    }

    // Status
    let status = (button.getAttribute('data-status') || '').toLowerCase();
    let badge = '';

    if(status === 'approved'){
        badge = '<span class="badge bg-success px-3 py-2 rounded-pill">Approved</span>';
    } else if(status === 'pending'){
        badge = '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Pending</span>';
    } else if(status === 'cancel_requested'){
        badge = '<span class="badge bg-secondary px-3 py-2 rounded-pill">Cancel Requested</span>';
    } else if(status === 'cancelled'){
        badge = '<span class="badge bg-danger px-3 py-2 rounded-pill">Cancelled</span>';
    } else if(status === 'refund_requested'){
        badge = '<span class="badge bg-info text-dark px-3 py-2 rounded-pill">Refund Requested</span>';
    } else if(status === 'refunded'){
        badge = '<span class="badge bg-primary px-3 py-2 rounded-pill">Refunded</span>';
    } else {
        badge = '<span class="badge bg-dark px-3 py-2 rounded-pill">Unknown</span>';
    }

    document.getElementById('r-status').innerHTML = badge;

    // Payment proof
    let proof = button.getAttribute('data-proof');
    let proofArea = document.getElementById('proofArea');

    if(proof && proof !== ""){
        proofArea.innerHTML = `
        <p class="fw-bold">Payment Proof</p>
        <img src="../assets/payment_proof/${proof}" 
             class="img-fluid rounded border shadow-sm">
        `;
    } else {
        proofArea.innerHTML = `<span class="badge bg-info">No Proof (COD)</span>`;
    }

    // Customer Action Buttons
    let transactionId = button.getAttribute('data-id');
    let actionArea = document.getElementById('customerActionArea');

    if(actionArea){
        if(status === 'pending' || status === 'approved'){
            actionArea.innerHTML = `
                <a href="cancel-request.php?id=${transactionId}" class="btn btn-warning me-2">
                    Ajukan Pembatalan
                </a>
            `;

            if(status === 'approved'){
                actionArea.innerHTML += `
                    <a href="refund-request.php?id=${transactionId}" class="btn btn-info text-white">
                        Ajukan Refund
                    </a>
                `;
            }
        } else {
            actionArea.innerHTML = '';
        }
    }
});
</script>

<?php include 'notif.php'; ?>
</body>
</html>