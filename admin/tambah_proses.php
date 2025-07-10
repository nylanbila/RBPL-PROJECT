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
    $nama_bahan = $_POST['nama_bahan'];
    $jenis_bahan = $_POST['jenis_bahan'];
    $harga = $_POST['harga'];
    $satuan = $_POST['satuan'];
    $stok = $_POST['stok'];


    // Simpan data ke database
    $query = "INSERT INTO data_bahan (
                nama_bahan, jenis_bahan, harga, satuan,
                stok
            ) VALUES (
                '$nama_bahan', '$jenis_bahan', '$harga',
                '$satuan', '$stok'
            )";

    if ($mysqli->query($query)) {
        header("Location: tambahbahan.php?pesan=berhasil");
        exit();
    } else {
        header("Location: tambahbahan.php?pesan=gagal");
        exit();
    }
} else {
    header("Location: tambahbahan.php?pesan=gagal");
    exit();
}
?>
