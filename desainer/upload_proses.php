<?php
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "inerior");
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $id_desainer = $_SESSION['id_user'];
    $nama_desain = $_POST['nama_desain'];
    $tanggal_dibuat = $_POST['tanggal_dibuat'];
    $ukuran = $_POST['ukuran'];
    $material = $_POST['material'];
    $status_desain = $_POST['status_desain'];
    $deskripsi_desain = $_POST['deskripsi_desain'];

    // Upload gambar ke folder img/
    $target_dir = "img/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true); // Buat folder jika belum ada
    }

    $nama_file = basename($_FILES["gambar_desain"]["name"]);
    $unique_name = time() . "_" . $nama_file;
    $target_file = $target_dir . $unique_name;

    // Validasi ekstensi gambar
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($imageFileType, $allowed_types)) {
        header("Location: upload_desain.php?pesan=gagal");
        exit();
    }

    // Cek apakah file berhasil diunggah
    if ($_FILES["gambar_desain"]["error"] !== UPLOAD_ERR_OK) {
        header("Location: upload_desain.php?pesan=gagal");
        exit();
    }

    // Pindahkan file ke folder img/
    if (!move_uploaded_file($_FILES["gambar_desain"]["tmp_name"], $target_file)) {
        header("Location: upload_desain.php?pesan=gagal");
        exit();
    }

    // Simpan data ke database
    $query = "INSERT INTO data_desain (
                id_desainer, nama_desain, tanggal_dibuat, ukuran, material,
                status_desain, deskripsi_desain, gambar_desain
            ) VALUES (
                '$id_desainer', '$nama_desain', '$tanggal_dibuat', '$ukuran',
                '$material', '$status_desain', '$deskripsi_desain', '$target_file'
            )";

    if ($mysqli->query($query)) {
        header("Location: upload_desain.php?pesan=berhasil");
        exit();
    } else {
        header("Location: upload_desain.php?pesan=gagal");
        exit();
    }
} else {
    header("Location: upload_desain.php?pesan=gagal");
    exit();
}
?>
