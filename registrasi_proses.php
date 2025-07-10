<?php
// Koneksi ke database
$mysqli = new mysqli("localhost", "root", "", "inerior");
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

// Ambil data dari form
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password']; // untuk keamanan, bisa dienkripsi nanti
$level = $_POST['level'];

// Tentukan nama tabel berdasarkan level
switch ($level) {
    case 'admin':
        $tabel = 'admin';
        break;
    case 'mandor':
        $tabel = 'mandor';
        break;
    case 'desainer':
        $tabel = 'desainer';
        break;
    default:
        header("Location: registrasi.php?pesan=gagal");
        exit();
}

// Cek email duplikat
$cek = $mysqli->query("SELECT * FROM $tabel WHERE email = '$email'");
if ($cek->num_rows > 0) {
    header("Location: registrasi.php?pesan=gagal");
    exit();
}

// Upload gambar ke folder img/
$target_dir = "img/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0755, true); // buat folder jika belum ada
}
$nama_file = basename($_FILES["gambar_profile"]["name"]);
$unique_name = time() . "_" . $nama_file;
$target_file = $target_dir . $unique_name;

// Validasi ekstensi
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
if (!in_array($imageFileType, $allowed_types)) {
    header("Location: registrasi.php?pesan=gagal");
    exit();
}

// Pindahkan file
if (!move_uploaded_file($_FILES["gambar_profile"]["tmp_name"], $target_file)) {
    header("Location: registrasi.php?pesan=gagal");
    exit();
}

// Simpan ke database
$query = "INSERT INTO $tabel (username, password, email, gambar_profile)
          VALUES ('$username', '$password', '$email', '$target_file')";

if ($mysqli->query($query)) {
    header("Location: registrasi.php?pesan=berhasil");
} else {
    header("Location: registrasi.php?pesan=gagal");
}
?>
