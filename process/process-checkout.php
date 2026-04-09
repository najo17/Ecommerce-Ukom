<?php
session_start();
include '../config/database.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login-customer.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? '');
$address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
$phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
$courier = mysqli_real_escape_string($conn, trim($_POST['courier'] ?? ''));
$shipping_service = mysqli_real_escape_string($conn, trim($_POST['shipping_service'] ?? ''));
$shipping_cost = (int)($_POST['shipping_cost'] ?? 0);
$cart = $_SESSION['cart'] ?? [];

/* ================= AMBIL DATA USER ================= */
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$customer_id'");
$user = mysqli_fetch_assoc($user_query);

$customer_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];

if(empty($cart)){
    header("Location: ../customer/cart.php");
    exit();
}

/* ================= VALIDASI ADDRESS ================= */
if(empty($address)){
    $_SESSION['notif'] = ['type' => 'error', 'message' => 'Please fill your address first!'];
    header("Location: ../customer/profile.php");
    exit();
}

/* ================= VALIDASI PHONE ================= */
if(empty($phone)){
    $_SESSION['notif'] = ['type' => 'error', 'message' => 'Please fill your phone number first!'];
    header("Location: ../customer/profile.php");
    exit();
}

/* ================= VALIDASI KURIR ================= */
if(empty($courier) || empty($shipping_service)){
    $_SESSION['notif'] = ['type' => 'error', 'message' => 'Please select courier and shipping service!'];
    header("Location: ../customer/checkout.php");
    exit();
}

/* ================= VALIDASI ONGKIR SESUAI PILIHAN ================= */
$valid_shipping_cost = 0;

if ($courier == "JNE" && $shipping_service == "Regular") {
    $valid_shipping_cost = 10000;
} elseif ($courier == "JNE" && $shipping_service == "Express") {
    $valid_shipping_cost = 18000;
} elseif ($courier == "J&T" && $shipping_service == "Regular") {
    $valid_shipping_cost = 12000;
} elseif ($courier == "J&T" && $shipping_service == "Express") {
    $valid_shipping_cost = 20000;
} elseif ($courier == "SiCepat" && $shipping_service == "Regular") {
    $valid_shipping_cost = 11000;
} elseif ($courier == "SiCepat" && $shipping_service == "Express") {
    $valid_shipping_cost = 19000;
} elseif ($courier == "AnterAja" && $shipping_service == "Regular") {
    $valid_shipping_cost = 9000;
} elseif ($courier == "AnterAja" && $shipping_service == "Express") {
    $valid_shipping_cost = 17000;
} elseif ($courier == "Ninja Xpress" && $shipping_service == "Regular") {
    $valid_shipping_cost = 13000;
} elseif ($courier == "Ninja Xpress" && $shipping_service == "Express") {
    $valid_shipping_cost = 21000;
}

$shipping_cost = $valid_shipping_cost;

/* ================= VALIDASI PAYMENT PROOF ================= */
if($payment_method === 'transfer'){
    if(!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== 0){
        $_SESSION['notif'] = ['type' => 'error', 'message' => 'Please upload payment proof for transfer!'];
        header("Location: ../customer/checkout.php");
        exit();
    }
}

/* ================= VALIDASI STOK DULU ================= */
foreach($cart as $id => $qty){

    $id = (int)$id;
    $qty = (int)$qty;

    $query = mysqli_query($conn,"SELECT stock FROM products WHERE id=$id");
    $product = mysqli_fetch_assoc($query);

    if(!$product || $qty > $product['stock']){
        $_SESSION['notif'] = ['type' => 'error', 'message' => 'Stock not enough!'];
        header("Location: ../customer/cart.php");
        exit();
    }
}

/* ================= HITUNG SUBTOTAL ================= */
$subtotal_total = 0;

foreach($cart as $id => $qty){
    $id = (int)$id;
    $qty = (int)$qty;

    $query = mysqli_query($conn,"SELECT price FROM products WHERE id=$id");
    $product = mysqli_fetch_assoc($query);

    $subtotal_total += $product['price'] * $qty;
}

/* ================= TOTAL AKHIR ================= */
$total = $subtotal_total + $shipping_cost;

/* ================= STATUS ================= */
$status = ($payment_method == 'transfer') ? 'pending' : 'approved';

/* ================= MULAI TRANSACTION DATABASE ================= */
mysqli_begin_transaction($conn);

try {

    /* SIMPAN KE TRANSACTIONS */
    mysqli_query($conn,"INSERT INTO transactions 
    (
        customer_id, 
        customer_name, 
        total, 
        payment_method, 
        courier,
        shipping_service,
        shipping_cost,
        status, 
        shipping_address, 
        shipping_phone,
        created_at
    )
    VALUES
    (
        '$customer_id',
        '$customer_name',
        '$total',
        '$payment_method',
        '$courier',
        '$shipping_service',
        '$shipping_cost',
        '$status',
        '$address',
        '$phone',
        NOW()
    )
    ");

    $transaction_id = mysqli_insert_id($conn);

    /* UPLOAD PAYMENT PROOF */
    if($payment_method === 'transfer' 
       && isset($_FILES['payment_proof']) 
       && $_FILES['payment_proof']['error'] === 0){

        $filename = time() . '_' . $_FILES['payment_proof']['name'];
        $target = '../assets/payment_proof/' . $filename;

        if(move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target)){
            mysqli_query($conn,"UPDATE transactions 
            SET payment_proof='$filename' 
            WHERE id=$transaction_id");
        }
    }

    /* ================= LOOP PRODUK ================= */
    foreach($cart as $id => $qty){

        $id = (int)$id;
        $qty = (int)$qty;

        $query = mysqli_query($conn,"SELECT * FROM products WHERE id=$id");
        $product = mysqli_fetch_assoc($query);

        $subtotal = $product['price'] * $qty;

        /* SIMPAN KE SALES */
        mysqli_query($conn,"INSERT INTO sales 
        (transaction_id, product_id, product_name, quantity, subtotal, created_at)
        VALUES
        ('$transaction_id','$id','{$product['name']}','$qty','$subtotal', NOW())
        ");

        /* KURANGI STOCK */
        mysqli_query($conn,"
            UPDATE products 
            SET stock = stock - $qty 
            WHERE id=$id
        ");
    }

    /* COMMIT */
    mysqli_commit($conn);

} catch (Exception $e) {

    mysqli_rollback($conn);
    $_SESSION['notif'] = ['type' => 'error', 'message' => 'Transaction failed!'];
    header("Location: ../customer/cart.php");
    exit();
}

/* ================= KOSONGKAN CART ================= */
unset($_SESSION['cart']);

$_SESSION['notif'] = ['type' => 'success', 'message' => 'Order placed successfully! 🎉'];
header("Location: ../customer/history.php");
exit();
?>