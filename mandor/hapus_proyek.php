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
if (isset($_GET['id_proyek'])) {
    $id_proyek = intval($_GET['id_proyek']);

    // Query untuk menghapus data berdasarkan ID
    $sql = "DELETE FROM data_proyek WHERE id_proyek = $id_proyek";

    if (mysqli_query($koneksi, $sql)) {
        // Jika berhasil, kembali ke halaman data proyek
        header("Location: dataproject.php?status=sukses");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
       header("Location: dataproject.php?status=gagal");
    }
} else {
    echo "ID proyek tidak ditemukan.";
}
?>
