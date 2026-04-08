<?php
require_once '../auth/auth.php';
require_once '../config/database.php';

// Cek role officer
if ($_SESSION['role'] !== 'officer') {
    header("Location: ../auth/login-officer.php");
    exit;
}

/* ===============================
   APPROVE / REJECT CANCEL REQUEST
================================ */
if (isset($_POST['approve_cancel'])) {
    $request_id = (int) $_POST['request_id'];
    $transaction_id = (int) $_POST['transaction_id'];

    // Cek apakah request masih pending
    $check_request = mysqli_query($conn, "
        SELECT * FROM cancel_requests 
        WHERE id = $request_id AND status = 'pending'
    ");

    if(mysqli_num_rows($check_request) > 0){

        // Ambil semua item dari transaksi ini
        $sales_items = mysqli_query($conn, "
            SELECT product_id, quantity 
            FROM sales 
            WHERE transaction_id = $transaction_id
        ");

        // Kembalikan stok ke tabel products
        while($item = mysqli_fetch_assoc($sales_items)){
            $product_id = (int) $item['product_id'];
            $qty = (int) $item['quantity'];

            mysqli_query($conn, "
                UPDATE products 
                SET stock = stock + $qty
                WHERE id = $product_id
            ");
        }

        // Update status cancel request
        mysqli_query($conn, "
            UPDATE cancel_requests 
            SET status='approved', admin_note='Approved by officer'
            WHERE id=$request_id
        ");

        // Update status transaksi
        mysqli_query($conn, "
            UPDATE transactions 
            SET status='cancelled'
            WHERE id=$transaction_id
        ");
    }
}

if (isset($_POST['reject_cancel'])) {
    $request_id = (int) $_POST['request_id'];
    $transaction_id = (int) $_POST['transaction_id'];

    // Tolak request cancel
    mysqli_query($conn, "
        UPDATE cancel_requests 
        SET status='rejected', admin_note='Rejected by officer'
        WHERE id=$request_id
    ");

    // Kembalikan status transaksi ke approved
    mysqli_query($conn, "
        UPDATE transactions 
        SET status='approved'
        WHERE id=$transaction_id
    ");
}

/* ===============================
   APPROVE / REJECT REFUND REQUEST
================================ */
if (isset($_POST['approve_refund'])) {
    $request_id = (int) $_POST['request_id'];
    $transaction_id = (int) $_POST['transaction_id'];

    // Approve refund request
    mysqli_query($conn, "
        UPDATE refund_requests 
        SET status='approved', admin_note='Approved by officer'
        WHERE id=$request_id
    ");

    // Update status transaksi jadi refunded
    mysqli_query($conn, "
        UPDATE transactions 
        SET status='refunded'
        WHERE id=$transaction_id
    ");
}

if (isset($_POST['reject_refund'])) {
    $request_id = (int) $_POST['request_id'];
    $transaction_id = (int) $_POST['transaction_id'];

    // Tolak refund request
    mysqli_query($conn, "
        UPDATE refund_requests 
        SET status='rejected', admin_note='Rejected by officer'
        WHERE id=$request_id
    ");

    // Kembalikan status transaksi ke approved
    mysqli_query($conn, "
        UPDATE transactions 
        SET status='approved'
        WHERE id=$transaction_id
    ");
}

/* ===============================
   AMBIL DATA CANCEL REQUEST
================================ */
$cancel_requests = mysqli_query($conn, "
    SELECT cr.*, t.customer_name, t.total, t.payment_method, t.created_at
    FROM cancel_requests cr
    JOIN transactions t ON cr.transaction_id = t.id
    ORDER BY cr.id DESC
");

/* ===============================
   AMBIL DATA REFUND REQUEST
================================ */
$refund_requests = mysqli_query($conn, "
    SELECT rr.*, t.customer_name, t.total, t.payment_method, t.created_at
    FROM refund_requests rr
    JOIN transactions t ON rr.transaction_id = t.id
    ORDER BY rr.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund & Cancel Management - BuyBuy</title>

    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Simonetta:wght@400;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body{
            background:#f8f9fa;
            font-family:'Poppins', sans-serif;
        }

        .navbar{
            background:#FFA4A4;
        }

        .navbar-brand{
            font-weight:700;
            color:white !important;
            font-size:35px;
            font-family:Simonetta;
        }

        .section-title{
            font-weight:700;
            margin-bottom:20px;
            color:#333;
        }

        .card-box{
            background:#fff;
            border-radius:20px;
            padding:25px;
            box-shadow:0 8px 25px rgba(0,0,0,0.06);
            margin-bottom:40px;
        }

        .table thead th{
            background:#FFA4A4 !important;
            color:#fff !important;
            text-align:center;
            vertical-align:middle;
        }

        .table td{
            text-align:center;
            vertical-align:middle;
        }

        .reason-box{
            text-align:left;
            max-width:250px;
            white-space:normal;
        }

        .btn-sm{
            min-width:90px;
        }

        .back-btn{
            display:inline-block;
            margin-bottom:25px;
            text-decoration:none;
            color:#555;
            font-weight:500;
        }

        .back-btn:hover{
            color:#000;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-5">
    <a class="navbar-brand" href="dashboard-officer.php">BuyBuy</a>
</nav>

<div class="container py-5">

    <a href="index.php" class="back-btn">← Back to Dashboard</a>

    <!-- CANCEL REQUEST -->
    <div class="card-box">
        <h3 class="section-title">Cancel Requests</h3>

        <?php if(mysqli_num_rows($cancel_requests) == 0): ?>
            <div class="alert alert-warning">No cancel requests found.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Transaction</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = mysqli_fetch_assoc($cancel_requests)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>#<?= $row['transaction_id'] ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td>Rp <?= number_format($row['total']) ?></td>
                        <td class="reason-box"><?= htmlspecialchars($row['reason']) ?></td>
                        <td>
                            <?php
                            if($row['status'] == 'pending'){
                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                            } elseif($row['status'] == 'approved'){
                                echo '<span class="badge bg-success">Approved</span>';
                            } else {
                                echo '<span class="badge bg-danger">Rejected</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'pending'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                    <button type="submit" name="approve_cancel" class="btn btn-success btn-sm">Approve</button>
                                </form>

                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                    <button type="submit" name="reject_cancel" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Done</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- REFUND REQUEST -->
    <div class="card-box">
        <h3 class="section-title">Refund Requests</h3>

        <?php if(mysqli_num_rows($refund_requests) == 0): ?>
            <div class="alert alert-warning">No refund requests found.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Transaction</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Refund Amount</th>
                        <th>Bank / E-Wallet</th>
                        <th>Account Number</th>
                        <th>Account Holder</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = mysqli_fetch_assoc($refund_requests)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>#<?= $row['transaction_id'] ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td>Rp <?= number_format($row['total']) ?></td>
                        <td>Rp <?= number_format($row['refund_amount']) ?></td>
                        <td><?= htmlspecialchars($row['bank_name']) ?></td>
                        <td><?= htmlspecialchars($row['account_number']) ?></td>
                        <td><?= htmlspecialchars($row['account_holder']) ?></td>
                        <td class="reason-box"><?= htmlspecialchars($row['reason']) ?></td>
                        <td>
                            <?php
                            if($row['status'] == 'pending'){
                                echo '<span class="badge bg-warning text-dark">Pending</span>';
                            } elseif($row['status'] == 'approved'){
                                echo '<span class="badge bg-success">Approved</span>';
                            } elseif($row['status'] == 'refunded'){
                                echo '<span class="badge bg-primary">Refunded</span>';
                            } else {
                                echo '<span class="badge bg-danger">Rejected</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'pending'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                    <button type="submit" name="approve_refund" class="btn btn-success btn-sm">Approve</button>
                                </form>

                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                    <button type="submit" name="reject_refund" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">Done</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>