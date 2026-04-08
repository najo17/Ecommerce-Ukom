<?php
// Menghubungkan file auth untuk mengecek login / session user
require_once '../auth/auth.php';

// Menghubungkan file koneksi database
require_once '../config/database.php';

// Mengecek apakah role user bukan admin
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, arahkan ke halaman login admin
    header("Location: ../auth/login-admin.php");
    exit;
}

// Menentukan folder penyimpanan file backup
$backupDir = "../assets/backup/";

// Jika folder backup belum ada, buat foldernya
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Variabel notifikasi
$success = "";
$error   = "";

/* ================= BACKUP PROCESS ================= */
// Mengecek apakah tombol backup_now ditekan
if (isset($_POST['backup_now'])) {

    // Membuat nama file backup berdasarkan tanggal dan jam saat ini
    $filename = "backup_" . date("Y-m-d_H-i-s") . ".sql";

    // Menggabungkan folder backup dengan nama file
    $filepath = $backupDir . $filename;

    // Menyiapkan array kosong untuk menampung nama tabel
    $tables = array();

    // Menjalankan query untuk mengambil semua nama tabel di database
    $result = mysqli_query($conn, "SHOW TABLES");

    // Mengambil nama tabel satu per satu
    while ($row = mysqli_fetch_row($result)) {
        $tables[] = $row[0];
    }

    // Variabel untuk menampung seluruh isi file SQL backup
    $return = "";

    // Menonaktifkan foreign key sementara agar restore lebih aman
    $return .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    // Melakukan perulangan untuk setiap tabel dalam database
    foreach ($tables as $table) {

        // Mengambil seluruh data dari tabel yang sedang diproses
        $result = mysqli_query($conn, "SELECT * FROM `$table`");

        // Menghitung jumlah kolom pada tabel
        $num_fields = mysqli_num_fields($result);

        // Menambahkan perintah SQL untuk menghapus tabel jika sudah ada
        $return .= "DROP TABLE IF EXISTS `$table`;\n";

        // Mengambil query pembuatan tabel (CREATE TABLE)
        $createTableResult = mysqli_query($conn, "SHOW CREATE TABLE `$table`");
        $row2 = mysqli_fetch_row($createTableResult);

        // Menambahkan struktur tabel ke isi backup
        $return .= $row2[1] . ";\n\n";

        // Mengambil data per baris dari tabel
        while ($row = mysqli_fetch_row($result)) {

            // Menambahkan awal query INSERT INTO
            $return .= "INSERT INTO `$table` VALUES(";

            // Melakukan perulangan setiap kolom dalam satu baris data
            for ($j = 0; $j < $num_fields; $j++) {

                // Jika NULL, tulis NULL tanpa tanda kutip
                if (is_null($row[$j])) {
                    $return .= "NULL";
                } else {
                    // Mengamankan karakter khusus
                    $value = mysqli_real_escape_string($conn, $row[$j]);
                    $value = str_replace("\n", "\\n", $value);
                    $return .= '"' . $value . '"';
                }

                // Jika belum kolom terakhir, tambahkan koma
                if ($j < ($num_fields - 1)) {
                    $return .= ",";
                }
            }

            // Menutup query INSERT untuk satu baris data
            $return .= ");\n";
        }

        // Memberi jarak antar tabel dalam file SQL
        $return .= "\n\n";
    }

    // Mengaktifkan lagi foreign key
    $return .= "SET FOREIGN_KEY_CHECKS=1;\n";

    // Menyimpan isi backup SQL ke file .sql
    if (file_put_contents($filepath, $return)) {
        header("Location: backup_restore.php?success=backup");
        exit;
    } else {
        $error = "Gagal membuat file backup.";
    }
}


/* ================= DELETE BACKUP ================= */
// Mengecek apakah ada parameter delete pada URL
if (isset($_GET['delete'])) {

    // Mengambil nama file saja agar lebih aman dari manipulasi path
    $file = basename($_GET['delete']);

    // Menggabungkan folder backup dengan nama file yang ingin dihapus
    $fullPath = $backupDir . $file;

    // Mengecek apakah file benar-benar ada
    if (file_exists($fullPath)) {
        unlink($fullPath);
        header("Location: backup_restore.php?success=delete");
        exit;
    } else {
        $error = "File backup tidak ditemukan.";
    }
}


/* ================= RESTORE PROCESS ================= */
// Mengecek apakah tombol restore_now ditekan
if (isset($_POST['restore_now'])) {

    // Cek apakah file diupload
    if (!isset($_FILES['restore_file']) || $_FILES['restore_file']['error'] !== 0) {
        $error = "Silakan pilih file backup terlebih dahulu.";
    } else {

        // Ambil info file
        $fileTmp  = $_FILES['restore_file']['tmp_name'];
        $fileName = $_FILES['restore_file']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi ekstensi file
        if ($fileExt !== 'sql') {
            $error = "File restore harus berformat .sql";
        } else {

            // Membaca isi file SQL backup
            $sql = file_get_contents($fileTmp);

            if (empty(trim($sql))) {
                $error = "File SQL kosong atau tidak valid.";
            } else {

                // Reset koneksi query sebelumnya
                while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
                    if ($result = mysqli_store_result($conn)) {
                        mysqli_free_result($result);
                    }
                }

                // Jalankan restore
                if (mysqli_multi_query($conn, $sql)) {

                    $restoreError = false;

                    do {
                        // Bebaskan result jika ada
                        if ($result = mysqli_store_result($conn)) {
                            mysqli_free_result($result);
                        }

                        // Kalau query berikutnya gagal, hentikan
                        if (mysqli_more_results($conn)) {
                            if (!mysqli_next_result($conn)) {
                                $restoreError = true;
                                break;
                            }
                        } else {
                            break;
                        }

                    } while (true);

                    if ($restoreError) {
                        $error = "Restore gagal: " . mysqli_error($conn);
                    } else {
                        header("Location: backup_restore.php?success=restore");
                        exit;
                    }

                } else {
                    $error = "Restore gagal: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Notifikasi dari redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'backup') {
        $success = "Backup database berhasil dibuat.";
    } elseif ($_GET['success'] == 'restore') {
        $success = "Restore database berhasil.";
    } elseif ($_GET['success'] == 'delete') {
        $success = "File backup berhasil dihapus.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Backup & Restore</title>
<link rel="icon" type="image/png" href="../assets/uploads/logo.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f9f9f9;
}

.content {
    padding: 40px;
    flex: 1;
}

.section-title {
    color: #FFA4A4;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.table thead th {
    background: #FFA4A4 !important;
    color: white !important;
    text-align: center;
}

.btn-pink {
    background: #FFA4A4;
    color: white;
    border: none;
}

.btn-pink:hover {
    background: #ff8d8d;
    color: white;
}

.btn-download {
    background-color: #61C38D;
    border: none;
    color: #fff;
    padding: 6px 16px;
    border-radius: 6px;
    text-decoration: none;
}

.btn-download:hover {
    color: white;
    opacity: 0.9;
}

.btn-delete {
    background-color: #E54B4B;
    border: none;
    color: #fff;
    padding: 6px 16px;
    border-radius: 6px;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}
</style>
</head>

<body>

<div class="d-flex min-vh-100">

    <?php include 'sidebar.php'; ?>

    <div class="content">

        <!-- NOTIFIKASI -->
        <?php if (!empty($success)) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- BACKUP -->
        <h4 class="mb-4 section-title">
            <i class="bi bi-cloud-arrow-up-fill"></i>
            Backup
        </h4>

        <form method="POST">
            <button name="backup_now" class="btn btn-pink mb-3">
                <i class="bi bi-database-fill-add me-1"></i> Backup Now
            </button>
        </form>

        <table class="table table-bordered bg-white">
            <thead>
                <tr>
                    <th>Backup File</th>
                    <th>Date</th>
                    <th>Size</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

<?php
// Mengambil semua file .sql di folder backup
$files = glob($backupDir . "*.sql");

// Urutkan file terbaru dulu
if ($files) {
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
}

if (!empty($files)) :
    foreach ($files as $file):

        $filename = basename($file);
        $date = date("d/m/Y H:i", filemtime($file));
        $size = round(filesize($file) / 1024, 2) . " KB";
?>

<tr>
    <td><?= htmlspecialchars($filename) ?></td>
    <td><?= $date ?></td>
    <td><?= $size ?></td>

    <td>
        <div class="action-buttons">
            <a href="download.php?file=<?= urlencode($filename) ?>" class="btn-download">
                <i class="bi bi-download me-1"></i> Download
            </a>

            <button 
                class="btn-delete"
                data-bs-toggle="modal"
                data-bs-target="#deleteBackupModal"
                data-file="<?= htmlspecialchars($filename) ?>">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </div>
    </td>
</tr>

<?php 
    endforeach; 
else:
?>

<tr>
    <td colspan="4" class="text-center text-muted">Belum ada file backup.</td>
</tr>

<?php endif; ?>

            </tbody>
        </table>

        <hr class="my-5">

        <!-- RESTORE -->
        <h4 class="mb-4 section-title">
            <i class="bi bi-arrow-clockwise"></i>
            Restore
        </h4>

        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <input type="file" name="restore_file" class="form-control" accept=".sql" required>
            </div>

            <div class="col-md-3">
                <button name="restore_now" class="btn btn-pink w-100">
                    <i class="bi bi-upload me-1"></i> Restore Now
                </button>
            </div>
        </form>

    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteBackupModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 overflow-hidden">

      <div class="modal-header border-0" style="background:#E54B4B;">
        <h5 class="modal-title text-white fw-semibold">
          Delete Backup File
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center py-5">
        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:70px;"></i>

        <h5 class="mt-4">
          Are you sure you want to delete
          <strong id="delete-file-name"></strong>?
        </h5>

        <p class="text-muted mt-2">
          This action cannot be undone.
        </p>
      </div>

      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          Cancel
        </button>

        <a href="#" id="confirmDeleteBackup"
           class="btn"
           style="background:#E54B4B; color:#fff;">
          Yes, Delete
        </a>
      </div>

    </div>
  </div>
</div>

<script>
var deleteModal = document.getElementById('deleteBackupModal');

deleteModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var fileName = button.getAttribute('data-file');

    document.getElementById('delete-file-name').innerText = fileName;
    document.getElementById('confirmDeleteBackup').href =
        "backup_restore.php?delete=" + encodeURIComponent(fileName);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>