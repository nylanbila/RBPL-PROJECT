<?php
session_start();

// Cek apakah user desainer sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "inerior";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$id_desainer = $_SESSION['id_user'];
$id_desain = $_POST['id_desain'] ?? '';
$tanggal_upload_revisi = date('Y-m-d H:i:s');
$nama_desain = $_POST['nama_desain'] ?? '';
$ukuran = $_POST['ukuran'] ?? '';
$material = $_POST['material'] ?? '';
$deskripsi_desain = $_POST['deskripsi_desain'] ?? '';



// Proses upload file revisi
$target_dir = "img/revisi/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true); // buat folder jika belum ada
}

$nama_file = basename($_FILES["file_revisi"]["name"]);
$target_file = $target_dir . time() . "_" . $nama_file;
$uploadOk = 1;

$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'pdf', 'png'];

if (!in_array($imageFileType, $allowed)) {
    echo "Format file tidak diizinkan.";
    $uploadOk = 0;
}


if ($uploadOk && move_uploaded_file($_FILES["file_revisi"]["tmp_name"], $target_file)) {
    // Update revisi_desain
    $query = "UPDATE revisi_desain 
              SET file_revisi = '$target_file', tanggal_upload_revisi = '$tanggal_upload_revisi'
              WHERE id_desain = '$id_desain' 
              ORDER BY id_revisi DESC LIMIT 1";
    
    if (mysqli_query($koneksi, $query)) {
        // Tambahan: update data_desain
        $query_update_desain = "UPDATE data_desain 
                                SET nama_desain = '$nama_desain',
                                    ukuran = '$ukuran',
                                    material = '$material',
                                    deskripsi_desain = '$deskripsi_desain'
                                WHERE id_desain = '$id_desain'";
        mysqli_query($koneksi, $query_update_desain);

        header("Location: detail_desain.php?id=$id_desain&status=berhasil");
        exit();
    }
}

header("Location: detail_desain.php?id=$id_desain&status=gagal");
exit();
