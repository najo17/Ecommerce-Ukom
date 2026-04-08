    <?php
    // Mengambil nama file halaman yang sedang dibuka
    // Contoh: jika URL adalah product-management.php, maka hasilnya "product-management.php"
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <style>
        /* Styling utama sidebar */
        .sidebar {
            width: 350px;
            min-height: 100vh;
            background-color: #FFA4A4;
            padding: 30px 25px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Styling judul sidebar */
        .sidebar h2 {
            color: #fff;
            font-weight: 600;
            line-height: 1.2;
        }

        /* Container menu sidebar */
        .menu {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        /* Styling link menu */
        .menu a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            padding: 16px 25px;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        /* Styling menu yang sedang aktif */
        .menu a.active {
            background: #fff;
            color: #FFA4A4;
            font-weight: 500;
        }

        /* Efek hover saat cursor diarahkan ke menu */
        .menu a:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Jarak atas untuk area logout */
        .logout {
            margin-top: 40px;
        }

        /* Styling tombol logout */
        .logout a {
            display: block;
            background: #FF5C5C;
            color: #fff;
            text-align: center;
            padding: 14px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
        }

        /* Efek hover tombol logout */
        .logout a:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
    </style>

    <!-- Container sidebar -->
    <div class="sidebar">
        <div>
            <!-- Judul sidebar -->
            <div class="d-flex align-items-center mb-5">
                <i class="bi bi-person-circle fs-1 text-white me-3 opacity-75"></i>
                <h2 class="m-0 fs-4">Dashboard<br><span class="fs-6 fw-normal text-white-50">Admin Panel</span></h2>
            </div>

            <!-- Menu navigasi sidebar -->
            <div class="menu">

                <!-- Link menuju halaman Home -->
                <a href="index.php" 
                class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
                <i class="bi bi-house-door me-2"></i> Home
                </a>

                <!-- Link menuju halaman User Management -->
                <a href="user-management.php" 
                class="<?= ($current_page == 'user-management.php') ? 'active' : '' ?>">
                <i class="bi bi-people me-2"></i> User Management
                </a>

                <!-- Link menuju halaman Product Management -->
                <a href="product-management.php" 
                class="<?= ($current_page == 'product-management.php') ? 'active' : '' ?>">
                <i class="bi bi-box-seam me-2"></i> Product Management
                </a>

                <!-- Link menuju halaman Generate Report -->
                <a href="generate-reports.php" 
                class="<?= ($current_page == 'generate-reports.php') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Generate Report
                </a>

                <!-- Link menuju halaman Transaction Management -->
                <a href="transactions.php" 
                class="<?= ($current_page == 'transactions.php') ? 'active' : '' ?>">
                <i class="bi bi-cart-check me-2"></i> Transaction Management
                </a>

                <!-- Link menuju halaman Backup & Restore -->
                <a href="backup_restore.php" 
                class="<?= ($current_page == 'backup_restore.php') ? 'active' : '' ?>">
                <i class="bi bi-hdd-network me-2"></i> Data Backup/Restore
                </a>
            </div>
        </div>

        <!-- Tombol logout -->
        <div class="logout">
            <a href="../auth/logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>
    </div>