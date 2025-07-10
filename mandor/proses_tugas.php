<?php
session_start();

// Cek apakah sudah login
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

$id_proyek = isset($_POST['id_proyek']) ? (int)$_POST['id_proyek'] : 0;

// Fungsi untuk upload gambar bukti
function uploadGambarBukti($file) {
    $targetDir = "tugas/";
    
    // Buat folder jika belum ada
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($file["name"]);
    $targetFile = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Cek apakah file adalah gambar
    if (isset($file["tmp_name"]) && !empty($file["tmp_name"])) {
        $check = getimagesize($file["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
    }
    
    // Cek ukuran file (maksimal 5MB)
    if ($file["size"] > 5000000) {
        $uploadOk = 0;
    }
    
    // Hanya izinkan format tertentu
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadOk = 0;
    }
    
    // Upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile; // Return path relatif
        }
    }
    
    return false;
}

// TAMBAH TUGAS BARU
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['id_tugas'])) {
    $nama_tugas = mysqli_real_escape_string($koneksi, $_POST['nama_tugas']);
    $tanggal_mulai = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($koneksi, $_POST['tanggal_selesai']);
    $status_tugas = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    // Bukti tugas kosong saat tambah tugas baru
    $bukti_tugas = "";
    
    $query = "INSERT INTO tugas_proyek (id_proyek, nama_tugas, tanggal_mulai, tanggal_selesai, status_tugas, bukti_tugas) 
              VALUES ('$id_proyek', '$nama_tugas', '$tanggal_mulai', '$tanggal_selesai', '$status_tugas', '$bukti_tugas')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['message'] = "Tugas berhasil ditambahkan!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: detail_proyek.php?id=" . $id_proyek);
    exit();
}

// EDIT TUGAS
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_tugas'])) {
    $id_tugas = (int)$_POST['id_tugas'];
    $nama_tugas = mysqli_real_escape_string($koneksi, $_POST['nama_tugas']);
    $tanggal_mulai = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($koneksi, $_POST['tanggal_selesai']);
    $status_tugas = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    // Ambil data tugas lama untuk bukti gambar
    $query_old = "SELECT bukti_tugas FROM tugas_proyek WHERE id_tugas = $id_tugas";
    $result_old = mysqli_query($koneksi, $query_old);
    $old_data = mysqli_fetch_assoc($result_old);
    $bukti_tugas = $old_data['bukti_tugas']; // Gunakan gambar lama sebagai default
    
    // Handle upload gambar bukti baru (jika ada)
    if (isset($_FILES['bukti_tugas']) && $_FILES['bukti_tugas']['error'] == 0) {
        $uploadResult = uploadGambarBukti($_FILES['bukti_tugas']);
        if ($uploadResult) {
            // Hapus gambar lama jika ada
            if (!empty($old_data['bukti_tugas']) && file_exists($old_data['bukti_tugas'])) {
                unlink($old_data['bukti_tugas']);
            }
            $bukti_tugas = $uploadResult;
        }
    }
    
    $query = "UPDATE tugas_proyek SET 
              nama_tugas = '$nama_tugas',
              tanggal_mulai = '$tanggal_mulai',
              tanggal_selesai = '$tanggal_selesai',
              status_tugas = '$status_tugas',
              bukti_tugas = '$bukti_tugas'
              WHERE id_tugas = $id_tugas";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['message'] = "Tugas berhasil diupdate!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: detail_proyek.php?id=" . $id_proyek);
    exit();
}

// HAPUS TUGAS
if (isset($_GET['delete']) && isset($_GET['id_tugas'])) {
    $id_tugas = (int)$_GET['id_tugas'];
    $id_proyek = (int)$_GET['id_proyek'];
    
    // Ambil data gambar untuk dihapus
    $query_img = "SELECT bukti_tugas FROM tugas_proyek WHERE id_tugas = $id_tugas";
    $result_img = mysqli_query($koneksi, $query_img);
    $img_data = mysqli_fetch_assoc($result_img);
    
    // Hapus tugas dari database
    $query = "DELETE FROM tugas_proyek WHERE id_tugas = $id_tugas";
    
    if (mysqli_query($koneksi, $query)) {
        // Hapus file gambar jika ada
        if (!empty($img_data['bukti_tugas']) && file_exists($img_data['bukti_tugas'])) {
            unlink($img_data['bukti_tugas']);
        }
        
        $_SESSION['message'] = "Tugas berhasil dihapus!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: detail_proyek.php?id=" . $id_proyek);
    exit();
}

// Jika tidak ada aksi yang valid, redirect kembali
header("Location: detail_proyek.php?id=" . $id_proyek);
exit();
?>