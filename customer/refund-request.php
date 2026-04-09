<?php
session_start();
include '../config/database.php';

// Cek login customer
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// Ambil id transaksi
if(!isset($_GET['id']) || empty($_GET['id'])){
    die("Transaction ID not found.");
}

$transaction_id = (int) $_GET['id'];

// Ambil data transaksi milik customer
$transaction = mysqli_query($conn, "
    SELECT * FROM transactions 
    WHERE id = $transaction_id AND customer_id = $customer_id
");

if(mysqli_num_rows($transaction) == 0){
    die("Transaction not found.");
}

$data = mysqli_fetch_assoc($transaction);

// Hanya boleh refund kalau status approved
$allowed_status = ['approved'];

if(!in_array(strtolower($data['status']), $allowed_status)){
    die("This transaction cannot be refunded.");
}

// Proses submit
if(isset($_POST['submit_refund'])){
    $reason         = mysqli_real_escape_string($conn, trim($_POST['reason']));
    $bank_name      = mysqli_real_escape_string($conn, trim($_POST['bank_name']));
    $account_number = mysqli_real_escape_string($conn, trim($_POST['account_number']));
    $account_holder = mysqli_real_escape_string($conn, trim($_POST['account_holder']));
    $refund_amount  = (int) $_POST['refund_amount'];

    if(empty($reason) || empty($bank_name) || empty($account_number) || empty($account_holder) || $refund_amount <= 0){
        $_SESSION['notif'] = ['type' => 'error', 'message' => 'All fields must be filled correctly.'];
    } else {

        // Cek apakah request refund sudah pernah dibuat
        $check = mysqli_query($conn, "
            SELECT * FROM refund_requests 
            WHERE transaction_id = $transaction_id
        ");

        if(mysqli_num_rows($check) > 0){
            $_SESSION['notif'] = ['type' => 'error', 'message' => 'Refund request has already been submitted.'];
        } else {
            // Simpan request refund
            mysqli_query($conn, "
                INSERT INTO refund_requests 
                (transaction_id, customer_id, reason, bank_name, account_number, account_holder, refund_amount, status)
                VALUES 
                ($transaction_id, $customer_id, '$reason', '$bank_name', '$account_number', '$account_holder', $refund_amount, 'pending')
            ");

            // Update status transaksi
            mysqli_query($conn, "
                UPDATE transactions 
                SET status = 'refund_requested' 
                WHERE id = $transaction_id
            ");

            header("Location: history.php?refund=success");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Request - BuyBuy</title>

    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Simonetta:wght@400;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body{
            background:#fff;
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

        .request-card{
            max-width:650px;
            margin:70px auto;
            background:#fff;
            border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.08);
            padding:35px;
        }

        .btn-main{
            background:#FFA4A4;
            color:#fff;
            border:none;
        }

        .btn-main:hover{
            background:#ff8d8d;
            color:#fff;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-5">
    <a class="navbar-brand" href="index.php">BuyBuy</a>
</nav>

<div class="container">
    <div class="request-card">
        <h3 class="fw-bold mb-3">Refund Request</h3>
        <p class="text-muted mb-4">Please complete the refund information below.</p>

        <div class="mb-3">
            <strong>Transaction ID:</strong> #<?= $data['id'] ?><br>
            <strong>Total Transaction:</strong> Rp <?= number_format($data['total']) ?><br>
            <strong>Status:</strong> <?= ucfirst($data['status']) ?>
        </div>



        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Reason for Refund</label>
                <textarea name="reason" class="form-control" rows="4" required placeholder="Example: Overpayment / Want to cancel / Wrong payment amount..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Bank Name / E-Wallet</label>
                <input type="text" name="bank_name" class="form-control" required placeholder="Example: BCA / DANA / OVO">
            </div>

            <div class="mb-3">
                <label class="form-label">Account Number / Phone Number</label>
                <input type="text" name="account_number" class="form-control" required placeholder="Example: 1234567890">
            </div>

            <div class="mb-3">
                <label class="form-label">Account Holder Name</label>
                <input type="text" name="account_holder" class="form-control" required placeholder="Example: Fauzan Anris">
            </div>

            <div class="mb-3">
                <label class="form-label">Refund Amount</label>
                <input type="number" name="refund_amount" class="form-control" required min="1" placeholder="Example: 50000">
            </div>

            <div class="d-flex gap-2">
                <a href="history.php" class="btn btn-secondary">Back</a>
                <button type="submit" name="submit_refund" class="btn btn-main">
                    Submit Refund Request
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'notif.php'; ?>
</body>
</html>