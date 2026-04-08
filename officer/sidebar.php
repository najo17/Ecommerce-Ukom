<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
    /* Styling dasar sidebar */
    .sidebar {
        width: 320px;
        min-height: 100vh;
        background: linear-gradient(180deg, #FFA4A4 0%, #FF8E8E 100%);
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 4px 0 20px rgba(0,0,0,0.05);
    }

    /* Bagian Branding / Judul */
    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 50px;
        color: #fff;
    }
    .sidebar-brand i {
        font-size: 32px;
    }
    .sidebar-brand h2 {
        font-weight: 600;
        line-height: 1.2;
        margin: 0;
        font-size: 24px;
        letter-spacing: 0.5px;
    }

    /* Container menu */
    .menu {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Gaya dasar link menu */
    .menu a {
        color: #fff;
        text-decoration: none;
        font-size: 15px;
        padding: 16px 20px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.3s ease;
        background: transparent;
    }

    .menu a i {
        font-size: 20px;
        width: 24px;
        text-align: center;
    }

    /* Tampilan menu yang sedang aktif */
    .menu a.active {
        background: #ffffff;
        color: #FF5C5C;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    /* Tampilan menu saat di hover kursor */
    .menu a:hover:not(.active) {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
    }

    /* Bagian tombol Logout */
    .logout {
        margin-top: 40px;
    }

    .logout a {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #FF5C5C;
        color: #fff;
        padding: 16px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 500;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .logout a:hover {
        background: #E54B4B;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(229, 75, 75, 0.3);
    }
</style>

<div class="sidebar">
    <div>
        <!-- Judul Admin Panel -->
        <div class="sidebar-brand">
            <i class="bi bi-person-badge"></i>
            <h2>Dashboard<br>Officer</h2>
        </div>

        <div class="menu">
            <a href="index.php" 
               class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
               <i class="bi bi-grid-1x2-fill"></i> Home
            </a>

            <a href="product-management.php" 
               class="<?= ($current_page == 'product-management.php') ? 'active' : '' ?>">
               <i class="bi bi-box-seam-fill"></i> Product Management
            </a>

            <a href="generate-reports.php" 
               class="<?= ($current_page == 'generate-reports.php') ? 'active' : '' ?>">
               <i class="bi bi-file-earmark-bar-graph-fill"></i> Generate Report
            </a>

            <a href="transactions.php" 
               class="<?= ($current_page == 'transactions.php') ? 'active' : '' ?>">
               <i class="bi bi-cart-check-fill"></i> Transaction Management
            </a>
        </div>
    </div>

    <!-- Tombol Logout di paling bawah mendasar pada align-items stretch/flex-column-between -->
    <div class="logout">
        <a href="../auth/logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>