<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "inerior";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil ID dari parameter URL
if (isset($_GET['id_bahan'])) {
    $id_bahan = intval($_GET['id_bahan']);

    // Query untuk menghapus data berdasarkan ID
    $sql = "DELETE FROM data_bahan WHERE id_bahan = $id_bahan";

    if (mysqli_query($koneksi, $sql)) {
        // Jika berhasil, kembali ke halaman data bahan
        header("Location: data-bahan.php?status=sukses");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
       header("Location: data-bahan.php?status=gagal");
    }
} else {
    echo "ID proyek tidak ditemukan.";
}
?>
