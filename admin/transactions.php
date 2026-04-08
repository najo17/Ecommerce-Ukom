<?php
// Memanggil file autentikasi untuk memastikan user sudah login
require_once '../auth/auth.php';

// Memanggil file koneksi database
require_once '../config/database.php';

// Mengecek apakah role user bukan admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, redirect ke halaman login admin
    header("Location: ../auth/login-admin.php");
    exit;
}

// Mengambil semua data transaksi dari tabel transactions
$transactions = mysqli_query($conn, "
    SELECT t.*
    FROM transactions t
    LEFT JOIN users u ON t.customer_id = u.id
    ORDER BY t.id DESC
");

/* ===============================
   AMBIL DATA SALES (DETAIL PRODUK)
================================ */

// Membuat array kosong untuk menyimpan detail produk dari tiap transaksi
$salesData = [];

// Mengambil data penjualan dari tabel sales
$salesQuery = mysqli_query($conn, "
    SELECT transaction_id, product_name, quantity, subtotal
    FROM sales
");

// Melakukan perulangan untuk setiap data sales
while($sale = mysqli_fetch_assoc($salesQuery)){
    $salesData[$sale['transaction_id']][] = $sale;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Transaction Management</title>

<!-- Icon logo pada tab browser -->
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">

<!-- Import Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Import Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Import font Poppins -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f4;
}

.content {
    padding: 40px;
    flex: 1;
}

.page-title {
    color: #FFA4A4;
    font-weight: 600;
}

/* Wrapper tabel modern */
.card.shadow-sm {
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: transparent !important;
    color: #888 !important;
    text-align: center;
    vertical-align: middle;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #eee !important;
    padding-bottom: 15px;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f8f8f8;
    padding: 15px 10px;
    text-align: center;
    color: #555;
}

.table tbody tr {
    transition: background 0.2s;
}

.table tbody tr:hover {
    background-color: #fcfcfc;
}

/* Action buttons */
.action-box {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 16px;
}

.action-proof {
    background-color: rgba(99, 199, 138, 0.15);
    color: #409960;
}
.action-proof:hover {
    background-color: #63C78A;
    color: #fff;
    transform: scale(1.05);
}

.action-receipt {
    background-color: rgba(108, 92, 231, 0.15);
    color: #6c5ce7;
}
.action-receipt:hover {
    background-color: #6c5ce7;
    color: #fff;
    transform: scale(1.05);
}

/* ========== MODAL STYLING ========== */
.modal-content {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    overflow: hidden;
}

.modal-header {
    background: #fff;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    padding: 20px 25px;
}

.modal-title {
    font-weight: 600;
    font-size: 18px;
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    border-top: none;
    background: #fdfdfd;
    padding: 20px 25px;
}

/* Status Badges */
.status-badge {
    padding: 6px 14px;
    border-radius: 50px;
    font-weight: 500;
    font-size: 13px;
    display: inline-block;
}

.status-approved {
    background-color: rgba(99, 199, 138, 0.15);
    color: #409960;
}

.status-pending {
    background-color: rgba(253, 203, 110, 0.2);
    color: #e1b12c;
}

.status-cancelled {
    background-color: rgba(235, 76, 76, 0.15);
    color: #EB4C4C;
}

.receipt-box{
    background:#fff;
}

.receipt-box hr{
    border-top:1px dashed #ccc;
}

.receipt-item{
    display:flex;
    justify-content:space-between;
    margin-bottom:6px;
    font-size:14px;
}

.gradient-header{
    background:linear-gradient(135deg,#FFA4A4,#FF7E7E);
}

#r-address{
    line-height:1.6;
    word-break:break-word;
}
</style>
</head>

<body>

<div class="d-flex min-vh-100">

<?php include 'sidebar.php'; ?>

<div class="content">

<h4 class="page-title mb-4">Transaction Management</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
<tr>
    <th>Customer</th>
    <th>Total</th>
    <th>Payment</th>
    <th>Courier</th>
    <th>Status</th>
    <th>Proof</th>
    <th>Details</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($transactions)) : ?>
<tr>

<td><?= htmlspecialchars($row['customer_name']) ?></td>

<td>Rp <?= number_format($row['total']) ?></td>

<td><?= ucfirst($row['payment_method']) ?></td>

<!-- COURIER -->
<td>
    <?= !empty($row['courier']) ? strtoupper(htmlspecialchars($row['courier'])) : '-' ?>
</td>

<td>
<?php
$status = strtolower($row['status']);

if($status == 'approved'){
    echo '<span class="status-badge status-approved">Approved</span>';
} elseif($status == 'pending'){
    echo '<span class="status-badge status-pending">Pending</span>';
} else {
    echo '<span class="status-badge status-cancelled">Cancelled</span>';
}
?>
</td>

<!-- PROOF BUTTON -->
<td>
<?php if($row['payment_method'] == 'transfer' && !empty($row['payment_proof'])): ?>
    <div class="action-box action-proof"
        data-bs-toggle="modal"
        data-bs-target="#proofModal"
        data-img="../assets/payment_proof/<?= htmlspecialchars($row['payment_proof']) ?>"
        title="View Proof">
        <i class="bi bi-image"></i>
    </div>
<?php else: ?>
    <span class="badge bg-secondary">No Proof (COD)</span>
<?php endif; ?>
</td>

<!-- RECEIPT BUTTON -->
<td>
<div class="action-box action-receipt"
    data-bs-toggle="modal"
    data-bs-target="#receiptModal"
    data-id="<?= $row['id'] ?>"
    data-customer="<?= htmlspecialchars($row['customer_name']) ?>"
    data-total="<?= $row['total'] ?>"
    data-payment="<?= htmlspecialchars($row['payment_method']) ?>"
    data-status="<?= htmlspecialchars($row['status']) ?>"
    data-date="<?= htmlspecialchars($row['created_at']) ?>"
    data-address="<?= htmlspecialchars($row['shipping_address']) ?>"
    data-products='<?= htmlspecialchars(json_encode($salesData[$row["id"]] ?? []), ENT_QUOTES, "UTF-8") ?>'
    data-proof="<?= htmlspecialchars($row['payment_proof']) ?>"
    data-courier="<?= htmlspecialchars($row['courier']) ?>"
    data-service="<?= htmlspecialchars($row['shipping_service']) ?>"
    data-shipping="<?= htmlspecialchars($row['shipping_cost']) ?>"
    title="View Receipt">
    <i class="bi bi-receipt"></i>
</div>
</td>

</tr>
<?php endwhile; ?>

            </table>
        </div>
    </div>
</div>
</div>
</div>


<!-- ================= PROOF MODAL ================= -->
<div class="modal fade" id="proofModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header">
    <h5 class="modal-title text-success">Proof of Payment</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body text-center">
    <img id="proofImage" src="" class="img-fluid rounded">
</div>

</div>
</div>
</div>


<!-- ================= RECEIPT MODAL ================= -->
<div class="modal fade" id="receiptModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content shadow-lg border-0">

<div class="modal-header">
    <h5 class="modal-title" style="color: #FFA4A4;">
        <i class="bi bi-receipt-cutoff me-2"></i>
        Transaction Receipt
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body p-4">

<div class="receipt-box p-4 rounded-4 border">

<h6 class="text-center fw-bold mb-3">BUYBUY STORE</h6>
<hr>

<div class="d-flex justify-content-between mb-2">
    <span>ID</span>
    <span id="r-id"></span>
</div>

<div class="d-flex justify-content-between mb-2">
    <span>Customer</span>
    <span id="r-customer"></span>
</div>

<div class="d-flex justify-content-between mb-2">
    <span>Date</span>
    <span id="r-date"></span>
</div>

<div class="mb-3 mt-2">
    <span class="fw-semibold">Address</span>
    <div id="r-address" class="small text-muted mt-1"></div>
</div>



<hr>

<strong>Products:</strong>
<div id="r-products" class="mt-2 small"></div>

<hr>


<hr>

<div class="d-flex justify-content-between mb-2">
    <span>Payment</span>
    <span id="r-payment"></span>
</div>

<div class="d-flex justify-content-between mb-2">
    <span>Status</span>
    <span id="r-status"></span>
</div>

<div class="d-flex justify-content-between mt-3">
    <span>Subtotal Products</span>
    <span>Rp <span id="r-subtotal-products"></span></span>
</div>

<div class="d-flex justify-content-between mt-3 fs-5 fw-bold">
    <span>Total</span>
    <span>Rp <span id="r-total"></span></span>
</div>

<!-- SHIPPING INFO -->
<div class="d-flex justify-content-between mb-2">
    <span>Courier</span>
    <span id="r-courier"></span>
</div>

<div class="d-flex justify-content-between mb-2">
    <span>Service</span>
    <span id="r-service"></span>
</div>

<div class="d-flex justify-content-between mb-2">
    <span>Shipping Cost</span>
    <span>Rp <span id="r-shipping"></span></span>
</div>

<div id="proofArea" class="text-center mt-4"></div>

<p class="text-center small mt-4 text-muted">
Thank you for shopping 
</p>

</div>

</div>
</div>
</div>
</div>

<!-- Import Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ================= PROOF IMAGE =================
var proofModal = document.getElementById('proofModal');

proofModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var img = button.getAttribute('data-img');
    document.getElementById('proofImage').src = img;
});

// ================= RECEIPT DATA =================
var receiptModal = document.getElementById('receiptModal');

receiptModal.addEventListener('show.bs.modal', function (event) {

    var button = event.relatedTarget;

    document.getElementById('r-id').innerText =
        button.getAttribute('data-id');

    document.getElementById('r-customer').innerText =
        button.getAttribute('data-customer');

    document.getElementById('r-date').innerText =
        new Date(button.getAttribute('data-date'))
        .toLocaleDateString('id-ID');

    document.getElementById('r-address').innerText =
        button.getAttribute('data-address') || '-';

    const total = parseInt(button.getAttribute('data-total')) || 0;
    const shipping = parseInt(button.getAttribute('data-shipping')) || 0;
    const subtotalProducts = total - shipping;

    document.getElementById('r-total').innerText =
        new Intl.NumberFormat('id-ID').format(total);

    document.getElementById('r-shipping').innerText =
        new Intl.NumberFormat('id-ID').format(shipping);

    document.getElementById('r-subtotal-products').innerText =
        new Intl.NumberFormat('id-ID').format(subtotalProducts);

    document.getElementById('r-payment').innerText =
        button.getAttribute('data-payment').toUpperCase();

    document.getElementById('r-courier').innerText =
        button.getAttribute('data-courier') || '-';

    document.getElementById('r-service').innerText =
        button.getAttribute('data-service') || '-';

    let status = button.getAttribute('data-status').toLowerCase();
    let badge = '';

    if(status === 'approved'){
        badge = '<span class="status-badge status-approved">Approved</span>';
    } else if(status === 'pending'){
        badge = '<span class="status-badge status-pending">Pending</span>';
    } else {
        badge = '<span class="status-badge status-cancelled">Cancelled</span>';
    }

    document.getElementById('r-status').innerHTML = badge;

    let products = JSON.parse(button.getAttribute('data-products') || "[]");
    let productHTML = '';

    products.forEach(item => {
        productHTML += `
            <div class="receipt-item">
                <span>${item.product_name} (x${item.quantity})</span>
                <span>Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</span>
            </div>
        `;
    });

    document.getElementById('r-products').innerHTML =
        productHTML || '<span class="text-muted">No product</span>';

    let proof = button.getAttribute('data-proof');
    let proofArea = document.getElementById('proofArea');

    if(proof && proof !== ""){
        proofArea.innerHTML = `
            <p class="fw-bold">Payment Proof</p>
            <img src="../assets/payment_proof/${proof}"
                 class="img-fluid rounded border shadow-sm">
        `;
    } else {
        proofArea.innerHTML = '';
    }
});
</script>

</body>
</html>