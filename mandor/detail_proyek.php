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

// Ambil data user untuk profil
$id_mandor = $_SESSION['id_user'];
$nama = "";
$gambar_profile = "";

$query_user = "SELECT username, gambar_profile FROM mandor WHERE id_mandor = $id_mandor";
$result_user = mysqli_query($koneksi, $query_user);
if ($result_user && $data = mysqli_fetch_assoc($result_user)) {
    $nama = $data['username'];
    $gambar_profile = $data['gambar_profile'];
}

// Ambil ID proyek dari parameter URL
$id_proyek = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query untuk mengambil detail proyek
$query_proyek = "SELECT * FROM data_proyek WHERE id_proyek = $id_proyek";
$result_proyek = mysqli_query($koneksi, $query_proyek);

if (!$result_proyek || mysqli_num_rows($result_proyek) == 0) {
    header("Location: dataproject.php");
    exit();
}
$proyek = mysqli_fetch_assoc($result_proyek);

//mengedit data proyek
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_proyek'])) {
    $id_proyek = intval($_POST['id_proyek']);
    $nama_proyek = mysqli_real_escape_string($koneksi, $_POST['nama_proyek']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $nama_klien = mysqli_real_escape_string($koneksi, $_POST['nama_klien']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    $query = "UPDATE data_proyek SET 
                nama_proyek = '$nama_proyek',
                tanggal_mulai = '$tanggal_mulai',
                tanggal_selesai = '$tanggal_selesai',
                nama_klien = '$nama_klien',
                deskripsi = '$deskripsi',
                status = '$status'
              WHERE id_proyek = $id_proyek";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data proyek berhasil diperbarui!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data proyek.');</script>";
    }
}

// Ambil data tugas dari database
$query_tugas = "SELECT * FROM tugas_proyek WHERE id_proyek = $id_proyek ORDER BY tanggal_mulai ASC";
$result_tugas = mysqli_query($koneksi, $query_tugas);
$data_tugas = [];
if ($result_tugas) {
    while ($row = mysqli_fetch_assoc($result_tugas)) {
        $data_tugas[] = $row;
    }
}

// Ambil desain terbaru (revisi terakhir jika ada)
$id_desain = $proyek['id_desain'];

// Cek apakah ada revisi untuk desain ini
$query_revisi = "
    SELECT file_revisi, tanggal_upload_revisi 
    FROM revisi_desain 
    WHERE id_desain = $id_desain 
    ORDER BY tanggal_upload_revisi DESC 
    LIMIT 1
";

$result_revisi = mysqli_query($koneksi, $query_revisi);

//mengambil gambar desain terbaru
if ($result_revisi && mysqli_num_rows($result_revisi) > 0) {
    // Ada revisi, pakai file revisi terakhir
    $data_revisi = mysqli_fetch_assoc($result_revisi);
    $desain_terbaru = $data_revisi['file_revisi'];
    $tanggal_desain = $data_revisi['tanggal_upload_revisi'];
    $sumber_desain = 'revisi'; // penanda
} else {
    // Tidak ada revisi, pakai desain asli
    $query_desain = "SELECT gambar_desain, tanggal_dibuat FROM data_desain WHERE id_desain = $id_desain";
    $result_desain = mysqli_query($koneksi, $query_desain);
    $data_desain = mysqli_fetch_assoc($result_desain);

    $desain_terbaru = $data_desain['gambar_desain'];
    $tanggal_desain = $data_desain['tanggal_dibuat'];
    $sumber_desain = 'asli';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Proyek</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #e5e5e5;
    }
    .clickable:hover {
      background-color: rgba(255, 255, 255, 0.1);
      cursor: pointer;
      border-radius: 5px;
    }
    .menu-item-clickable:hover {
      background-color: rgba(255, 255, 255, 0.1);
      cursor: pointer;
      border-radius: 5px;
    }
  </style>
</head>
<body>

  <div class="flex flex-col lg:flex-row min-h-screen">
    <!-- Sidebar - Desktop -->
<aside class="hidden lg:flex fixed left-0 top-0 h-screen w-96 overflow-y-auto bg-zinc-800 text-white flex-col justify-between z-50">
        <div>
            <div class="flex flex-col items-center py-6">
                <img src="../admin/admin/assets/logo-nobg.png" class="w-48 h-56 object-contain" alt="Logo">
                <h1 class="text-4xl font-medium">Mandor</h1>
                <hr class="border-white border-opacity-50 w-3/4 my-4">
            </div>
            <nav class="px-6 space-y-2">
                <div class="flex items-center space-x-3 cursor-pointer hover:bg-white/10 p-2 rounded" onclick="window.location.href='dashboard_mandor.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-7 h-7">
                        <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                        <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                    </svg>
                    <span class="text-xl font-medium">Dashboard</span>
                </div>

                <!-- Projek Dropdown -->
                <div class="cursor-pointer" onclick="toggleDropdown('projek-dropdown')">
                    <div class="flex items-center space-x-3 hover:bg-white/10 p-2 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="text-2xl font-medium">Projek</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-auto">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                        </svg>
                    </div>
                    <div id="projek-dropdown" class="dropdown-content active pl-10 mt-2 space-y-1">
                        <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='buatproject.php'">Buat Projek</div>
                        <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer bg-zinc-700" onclick="window.location.href='dataproject.php'">Data Projek</div>
                    </div>
                </div>

                <!-- Desain Dropdown -->
                <div class="cursor-pointer" onclick="toggleDropdown('desain-dropdown')">
                    <div class="flex items-center space-x-3 hover:bg-white/10 p-2 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span class="text-2xl font-medium">Desain</span>
                    </div>
                    <div id="desain-dropdown" class="dropdown-content pl-10 mt-2 space-y-1">
                        <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='desain.php'">Desain Projek</div>
                    </div>
                </div>
            </nav>
        </div>
        <div class="px-6 pb-6">
            <div class="flex items-center space-x-2 cursor-pointer hover:bg-white/10 p-2 rounded" onclick="logout()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
                <span class="text-xl font-medium">Logout</span>
            </div>
        </div>
</aside>

    <!-- Mobile Sidebar -->
    <div id="mobileSidebar" class="lg:hidden fixed inset-0 z-50 mobile-menu">
        <div class="flex h-full">
            <div class="w-80 bg-zinc-800 text-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between p-4">
                        <h1 class="text-2xl font-medium">Mandor</h1>
                        <button onclick="toggleMobileMenu()" class="text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <nav class="px-4 space-y-2">
                        <div class="flex items-center space-x-3 cursor-pointer hover:bg-white/10 p-2 rounded" onclick="window.location.href='dashboard_mandor.php'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-6 h-6">
                                <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                                <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                            </svg>
                            <span class="text-lg font-medium">Dashboard</span>
                        </div>
                        <div class="cursor-pointer" onclick="toggleDropdown('mobile-projek-dropdown')">
                            <div class="flex items-center space-x-3 hover:bg-white/10 p-2 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <span class="text-lg font-medium">Projek</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-auto">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                                </svg>
                            </div>
                            <div id="mobile-projek-dropdown" class="dropdown-content active pl-8 mt-2 space-y-1">
                                <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='buatproject.php'">Buat Projek</div>
                                <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer bg-zinc-700" onclick="window.location.href='dataproject.php'">Data Projek</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 cursor-pointer hover:bg-white/10 p-2 rounded" onclick="window.location.href='desain.php'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <span class="text-lg font-medium">Desain</span>
                        </div>
                    </nav>
                </div>
                <div class="px-4 pb-6">
                    <div class="flex items-center space-x-2 cursor-pointer hover:bg-white/10 p-2 rounded" onclick="logout()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                        </svg>
                        <span class="text-lg font-medium">Logout</span>
                    </div>
                </div>
            </div>
            <div class="flex-1 bg-black bg-opacity-50" onclick="toggleMobileMenu()"></div>
        </div>
    </div>

    <!-- Main Content -->
<div class="flex-1 flex flex-col ml-0 lg:ml-96 p-4">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white shadow px-4 lg:px-10 h-16 lg:h-24 relative z-50">
            <div class="flex items-center">
                <button onclick="toggleMobileMenu()" class="lg:hidden mr-4 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <img src="../admin/admin/assets/Vector.png" class="w-8 h-6 lg:w-10 lg:h-8" alt="Menu">
            </div>
            <div class="flex items-center space-x-2 lg:space-x-4 cursor-pointer" onclick="window.location.href='profilemandor.php'">
                <div class="w-10 h-10 lg:w-14 lg:h-14 rounded-full overflow-hidden bg-stone-500">
                    <img src="../<?= htmlspecialchars($gambar_profile); ?>" class="w-10 h-10 lg:w-14 lg:h-14 object-cover" alt="Profile">
                </div>
                <span class="text-lg lg:text-2xl font-medium text-black hidden sm:block"><?= htmlspecialchars($nama); ?></span>
            </div>
        </div>

    

    <!-- Page Title -->
<div class="px-6 py-4 lg:px-12 lg:py-8">
  <h1 class="text-3xl font-medium text-zinc-800">Detail Projek</h1>
  <p class="text-neutral-500 text-lg mt-2">Berikut detail projek</p>
</div>

    <!-- Main Content Container -->
    <div class="w-[1252px] h-[1131px] left-[506px] top-[270px] absolute bg-white"></div>

    <!-- Project Image -->
 <?php
$nama_file = basename($desain_terbaru); // contoh: 1750515780_IMG_1005.JPG

$path_revisi = "desainer/img/revisi/" . $nama_file;
$path_awal = "desainer/img/" . $nama_file;

if (file_exists("../" . $path_revisi)) {
    $gambar = $path_revisi;
    $lokasi = "Revisi";
} else {
    $gambar = $path_awal;
    $lokasi = "Awal";
}

// echo "<p>Nama file desain terbaru: $desain_terbaru</p>";
// echo "<p>Gambar ditampilkan dari: $lokasi</p>";
// echo "<p>Path final src: ../$gambar</p>";
?>

<img class="w-[541px] h-96 left-[680px] top-[373px] absolute rounded-xl border border-black" 
     src="../<?= $gambar ?>" alt="Desain Terbaru" />

        
    <!-- Data Projek Section -->
    <div class="left-[1028px] top-[287px] absolute justify-start text-zinc-800 text-4xl font-medium font-['Poppins']">Data Projek</div>
    
    <!-- Project Details -->
    <div class="left-[1262px] top-[395px] absolute justify-start text-black text-xl font-normal font-['Poppins']">Nama Projek : <?= htmlspecialchars($proyek['nama_proyek']) ?></div>
    <div class="left-[1262px] top-[441px] absolute justify-start text-black text-xl font-normal font-['Poppins']">Nama Client : <?= htmlspecialchars($proyek['nama_klien']) ?></div>
    <div class="left-[1262px] top-[486px] absolute justify-start text-black text-xl font-normal font-['Poppins']">Nama Mandor : <?= htmlspecialchars($nama) ?></div>
    <div class="left-[1262px] top-[531px] absolute justify-start text-black text-xl font-normal font-['Poppins']">Tanggal mulai : <?= htmlspecialchars($proyek['tanggal_mulai']) ?></div>
    <div class="left-[1262px] top-[574px] absolute justify-start text-black text-xl font-normal font-['Poppins']">Tanggal selesai : <?= htmlspecialchars($proyek['tanggal_selesai']) ?></div>
    <div class="left-[1263px] top-[620px] absolute justify-start text-black text-xl font-normal font-['Poppins']">Deskripsi : <?= !empty($proyek['deskripsi']) ? htmlspecialchars($proyek['deskripsi']) : '-' ?></div>

   <?php
        $status_color = '';
        $status_text = $proyek['status'];
        switch($status_text) {
            case 'Selesai':
                $status_color = 'bg-green-500';
                break;
            case 'Dalam Proses':
                $status_color = 'bg-yellow-500';
                break;
            case 'Belum dimulai':
                $status_color = 'bg-gray-500';
                break;
            default:
                $status_color = 'bg-gray-400';
        }
        ?>
    <div class="left-[1262px] top-[666px] absolute justify-start text-black text-xl font-normal font-['Poppins']">Status :
    <span class="px-3 py-1 rounded-full text-white text-sm font-medium <?= $status_color ?>"><?= htmlspecialchars($status_text) ?></span>
    </div>
    
    <!-- Progress Bar
    <div class="w-52 h-4 left-[867px] top-[758px] absolute bg-zinc-100"></div>
    <div class="w-24 h-4 left-[867px] top-[757px] absolute bg-green-400"></div>
    <div class="w-9 left-[1078px] top-[754px] absolute justify-start text-black text-base font-normal font-['Poppins']">35%</div> -->


         <?php
            if ($proyek['status'] == 'Selesai') {
                $completion = 100;
                $barWidth = 208;
            } else {
                $total_tugas_query = "SELECT COUNT(*) as total FROM tugas_proyek WHERE id_proyek = $id_proyek";
                $total_tugas = mysqli_fetch_assoc(mysqli_query($koneksi, $total_tugas_query))['total'];

                $selesai_query = "SELECT COUNT(*) as selesai FROM tugas_proyek WHERE id_proyek = $id_proyek AND status_tugas = 'Selesai'";
                $selesai = mysqli_fetch_assoc(mysqli_query($koneksi, $selesai_query))['selesai'];

                $completion = ($total_tugas > 0) ? round(($selesai / $total_tugas) * 100) : 0;
                $barWidth = 208 * ($completion / 100);
            }
            ?>

        <!-- Progress Bar Dinamis -->
        <div class="w-52 h-4 left-[867px] top-[758px] absolute bg-zinc-100 rounded"></div>
        <div class="h-4 left-[867px] top-[758px] absolute bg-green-400 rounded" style="width: <?= $barWidth ?>px;"></div>
        <div class="w-9 left-[1078px] top-[754px] absolute justify-start text-black text-base font-normal font-['Poppins']">
            <?= $completion ?>%
        </div>


    <!-- Edit Button -->
    <div class="w-10 h-8 left-[1643px] top-[396px] absolute rounded-[10px] outline outline-[0.50px] outline-offset-[-0.25px] outline-black overflow-hidden clickable bg-gray-200 hover:bg-gray-300" onclick="showEditPopup()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-700 absolute left-1 top-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
    </div>

    <!-- Popup Form Edit Proyek -->
    <div id="popupForm" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-[450px] p-6 rounded-md shadow-md font-['Poppins']">
            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg relative font-['Poppins'] animate-fade-in">
                <button onclick="document.getElementById('popupForm').style.display='none';" 
                        class="absolute top-2 right-3 text-gray-600 hover:text-black text-2xl font-bold">&times;</button>
                
                <h2 class="text-xl font-semibold mb-4 text-center">Edit Data Proyek</h2>
                
                <form action="" method="POST" class="space-y-3" enctype="multipart/form-data">
                    <div>
                        <input type="hidden" name="id_proyek" value="<?= $id_proyek ?>">
                        <label class="text-sm font-medium block mb-1">Nama Proyek</label>
                        <input type="text" name="nama_proyek" value="<?= htmlspecialchars($proyek['nama_proyek']) ?>" 
                               class="w-full border border-gray-300 px-3 py-2 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium block mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="<?= htmlspecialchars($proyek['tanggal_mulai']) ?>" 
                               class="w-full border border-gray-300 px-3 py-2 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium block mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="<?= htmlspecialchars($proyek['tanggal_selesai']) ?>" 
                               class="w-full border border-gray-300 px-3 py-2 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium block mb-1">Nama Client</label>
                        <input type="text" name="nama_klien" value="<?= htmlspecialchars($proyek['nama_klien']) ?>" 
                               class="w-full border border-gray-300 px-3 py-2 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                
                    <div>
                        <label class="text-sm font-medium block mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" 
                                  class="w-full border border-gray-300 px-3 py-2 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                  placeholder="Deskripsi proyek..."><?= htmlspecialchars($proyek['deskripsi']) ?></textarea>
                    </div>

                        <div>
                        <label class="text-sm font-medium block mb-1">Status</label>
                            <select name="status" id="edit_status" required class="w-full border px-3 py-2 rounded text-sm">
                        <option value="Belum dimulai" <?= $proyek['status'] == 'Belum dimulai' ? 'selected' : '' ?>>Belum dimulai</option>
                        <option value="Dalam Proses" <?= $proyek['status'] == 'Dalam Proses' ? 'selected' : '' ?>>Dalam Proses</option>
                        <option value="Selesai" <?= $proyek['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                    </div>
                    <div class="text-right pt-4">
                        <button type="button" onclick="document.getElementById('popupForm').style.display='none';" 
                                class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-full text-sm mr-2">
                            Batal
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-full text-sm">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Tugas Section -->
    <div class="left-[1028px] top-[859px] absolute justify-start text-zinc-800 text-4xl font-medium font-['Poppins']">Daftar Tugas</div>
    
    <!-- Add Task Button -->
    <div class="w-48 h-11 left-[1485px] top-[878px] absolute bg-green-400 rounded-xl clickable hover:bg-green-500" onclick="addTask()">
        <div class="w-40 h-7 left-[19px] top-[8px] absolute justify-start text-white text-lg font-medium font-['Poppins']">Tambah Tugas</div>
        <div class="w-7 h-7 left-[164px] top-[8px] absolute overflow-hidden">
        </div>
    </div>
        <!-- Popup Tambah Tugas -->
<div id="popupAddTask" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-[450px] p-6 rounded-md shadow-md font-['Poppins']">
    <button onclick="document.getElementById('popupAddTask').style.display='none';" 
            class="absolute top-2 right-3 text-gray-600 hover:text-black text-2xl font-bold">&times;</button>
    <h2 class="text-xl font-semibold mb-4 text-center">Tambah Tugas</h2>
    <form action="proses_tugas.php" method="POST" class="space-y-3">
      <input type="hidden" name="id_proyek" value="<?= $id_proyek ?>">
      <div>
        <label class="text-sm font-medium block mb-1">Nama Tugas</label>
        <input type="text" name="nama_tugas" required class="w-full border px-3 py-2 rounded text-sm">
      </div>
      <div>
        <label class="text-sm font-medium block mb-1">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" required class="w-full border px-3 py-2 rounded text-sm">
      </div>
      <div>
        <label class="text-sm font-medium block mb-1">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" required class="w-full border px-3 py-2 rounded text-sm">
      </div>
      <div>
        <label class="text-sm font-medium block mb-1">Status</label>
        <select name="status" required class="w-full border px-3 py-2 rounded text-sm">
          <option value="Belum dimulai">Belum dimulai</option>
          <option value="Dalam Proses">Dalam Proses</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>
      <div class="text-right pt-4">
        <button type="button" onclick="document.getElementById('popupAddTask').style.display='none';" class="bg-gray-500 text-white py-2 px-4 rounded-full text-sm mr-2">Batal</button>
        <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-full text-sm">Tambah</button>
      </div>
    </form>
  </div>
</div>

<!-- Popup Edit Tugas -->
<div id="popupEditTask" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white w-[450px] p-6 rounded-md shadow-md font-['Poppins']">
    <button onclick="document.getElementById('popupEditTask').style.display='none';" 
            class="absolute top-2 right-3 text-gray-600 hover:text-black text-2xl font-bold">&times;</button>
    <h2 class="text-xl font-semibold mb-4 text-center">Edit Tugas</h2>
<form action="proses_tugas.php" method="POST" class="space-y-3" enctype="multipart/form-data">
      <input type="hidden" name="id_proyek" value="<?= $id_proyek ?>">
      <input type="hidden" name="id_tugas" id="edit_id_tugas">
      <div>
        <label class="text-sm font-medium block mb-1">Nama Tugas</label>
        <input type="text" name="nama_tugas" id="edit_nama_tugas" required class="w-full border px-3 py-2 rounded text-sm">
      </div>
      <div>
        <label class="text-sm font-medium block mb-1">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" required class="w-full border px-3 py-2 rounded text-sm">
      </div>
      <div>
        <label class="text-sm font-medium block mb-1">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" required class="w-full border px-3 py-2 rounded text-sm">
      </div>
      <div>
        <label class="text-sm font-medium block mb-1">Status</label>
        <select name="status" id="edit_status" required class="w-full border px-3 py-2 rounded text-sm">
          <option value="Belum dimulai">Belum dimulai</option>
          <option value="Dalam Proses">Dalam Proses</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>


      
<div class="mb-4">
  <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Bukti</label>
  <label for="bukti_tugas"
    class="flex flex-col items-center justify-center w-full h-40 border-2 mb-4 border-dashed border-gray-400 rounded-lg cursor-pointer hover:border-gray-600 transition relative">
    
    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24"
      stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M7 16V4m0 0L3 8m4-4l4 4m5 0a5 5 0 015 5v3a5 5 0 01-5 5H6a5 5 0 01-5-5v-3a5 5 0 015-5h.28" />
    </svg>
    <p class="text-sm text-gray-600">Drop file here</p>
    <p class="text-sm text-indigo-600 underline">or browse</p>

    <input id="bukti_tugas" type="file" name="bukti_tugas"
           class="hidden"
           accept="image/*"
           required
           onchange="tampilkanNamaFile(this)">

    <p class="text-sm text-green-600 mt-2 hidden namaFile">✔ Gambar telah dipilih: <span></span></p>
  </label>
</div>


      <div class="text-right pt-4">
        <button type="button" onclick="document.getElementById('popupEditTask').style.display='none';" class="bg-gray-500 text-white py-2 px-4 rounded-full text-sm mr-2">Batal</button>
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-full text-sm">Simpan</button>
      </div>
    </form>
  </div>
</div>


    <!-- Task Table Header -->
    <div class="w-[1100px] h-12 left-[577px] top-[954px] absolute bg-zinc-500"></div>
    <div class="left-[600px] top-[962px] absolute justify-start text-white text-xl font-medium font-['Poppins']">No.</div>
    <div class="left-[661px] top-[962px] absolute justify-start text-white text-xl font-medium font-['Poppins']">Nama Tugas</div>
    <div class="left-[914px] top-[963px] absolute justify-start text-white text-xl font-medium font-['Poppins']">Mulai</div>
    <div class="left-[1070px] top-[962px] absolute justify-start text-white text-xl font-medium font-['Poppins']">Selesai</div>
    <div class="left-[1252px] top-[962px] absolute justify-start text-white text-xl font-medium font-['Poppins']">Status</div>
    <div class="left-[1406px] top-[962px] absolute justify-start text-white text-xl font-medium font-['Poppins']">Bukti</div>
    <div class="left-[1561px] top-[963px] absolute justify-start text-white text-xl font-medium font-['Poppins']">Aksi</div>

  <!-- Task Rows dengan Data Database -->
<?php 
$y_position = 1006; // Start position untuk row pertama
foreach ($data_tugas as $index => $tugas) {
    $no = $index + 1;
    $row_color = ($index % 2 == 0) ? 'bg-white' : 'bg-gray-50'; // Alternating row colors
    ?>
    
    <!-- Row Background -->
    <div class="w-[1100px] h-16 left-[577px] top-[<?= $y_position ?>px] absolute <?= $row_color ?> border-b border-gray-200"></div>
    
    <!-- No -->
    <div class="left-[600px] top-[<?= $y_position + 20 ?>px] absolute justify-start text-black text-lg font-normal font-['Poppins']"><?= $no ?></div>
    
    <!-- Nama Tugas -->
    <div class="w-52 left-[661px] top-[<?= $y_position + 20 ?>px] absolute justify-start text-black text-lg font-normal font-['Poppins'] truncate"><?= htmlspecialchars($tugas['nama_tugas']) ?></div>
    
    <!-- Tanggal Mulai -->
    <div class="left-[914px] top-[<?= $y_position + 20 ?>px] absolute justify-start text-black text-lg font-normal font-['Poppins']"><?= date('d/m/Y', strtotime($tugas['tanggal_mulai'])) ?></div>
    
    <!-- Tanggal Selesai -->
    <div class="left-[1070px] top-[<?= $y_position + 20 ?>px] absolute justify-start text-black text-lg font-normal font-['Poppins']"><?= date('d/m/Y', strtotime($tugas['tanggal_selesai'])) ?></div>
    
    <!-- Status -->
    <div class="left-[1252px] top-[<?= $y_position + 15 ?>px] absolute">
        <?php
        $status_color = '';
        $status_text = $tugas['status_tugas'];
        switch($status_text) {
            case 'Selesai':
                $status_color = 'bg-green-500';
                break;
            case 'Dalam Proses':
                $status_color = 'bg-yellow-500';
                break;
            case 'Belum dimulai':
                $status_color = 'bg-gray-500';
                break;
            default:
                $status_color = 'bg-gray-400';
        }
        ?>
        <span class="px-3 py-1 rounded-full text-white text-sm font-medium <?= $status_color ?>"><?= htmlspecialchars($status_text) ?></span>
    </div>
    
    <!-- Bukti -->
    <div class="left-[1406px] top-[<?= $y_position + 18 ?>px] absolute">
        <?php if (!empty($tugas['bukti_tugas'])): ?>
            <div class="w-8 h-8 bg-blue-500 rounded clickable hover:bg-blue-600 flex items-center justify-center" onclick="viewEvidence('<?= $tugas['bukti_tugas'] ?>')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </div>
        <?php else: ?>
            <span class="text-gray-400 text-lg">-</span>
        <?php endif; ?>
    </div>
    
    <!-- Aksi Buttons -->
    <div class="left-[1561px] top-[<?= $y_position + 15 ?>px] absolute flex gap-2">
        <!-- Edit Button -->
        <button class="bg-sky-500 hover:bg-sky-600 text-white px-3 py-1 rounded text-sm font-medium" 
                onclick="editTask(<?= $tugas['id_tugas'] ?>)">
            Edit
        </button>
        
        <!-- Delete Button -->
        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-medium" 
                onclick="deleteTask(<?= $tugas['id_tugas'] ?>)">
            Hapus
        </button>
    </div>
    
    <?php
    $y_position += 64; // Spacing antara rows (64px)
}
?>

<!-- Jika tidak ada data tugas -->
<?php if (empty($data_tugas)): ?>
    <div class="w-[1100px] h-16 left-[577px] top-[1006px] absolute bg-white border-b border-gray-200 flex items-center justify-center">
        <span class="text-gray-500 text-lg font-normal font-['Poppins']">Belum ada tugas untuk proyek ini</span>
    </div>
<?php endif; ?>

</div>
<script>
    function showEditPopup() {
        document.getElementById("popupForm").style.display = "block";
    }

    function addTask() {
        document.getElementById("popupAddTask").style.display = "block";
    }

function editTask(id_tugas) {
    // Ambil data dari PHP ke JavaScript
    const taskData = <?= json_encode(array_column($data_tugas, null, 'id_tugas')) ?>;

function tampilkanNamaFile(input) {
  const fileName = input.files[0]?.name;
  const label = input.closest('label');
  const textDisplay = label.querySelector('.namaFile');
  const fileSpan = textDisplay.querySelector('span');

  if (fileName) {
    fileSpan.textContent = fileName;
    textDisplay.classList.remove("hidden");
  } else {
    fileSpan.textContent = "";
    textDisplay.classList.add("hidden");
  }
}

    const task = taskData[id_tugas];
    if (task) {
        document.getElementById('edit_id_tugas').value = id_tugas;
        document.getElementById('edit_nama_tugas').value = task.nama_tugas;
        document.getElementById('edit_tanggal_mulai').value = task.tanggal_mulai;
        document.getElementById('edit_tanggal_selesai').value = task.tanggal_selesai;
        document.getElementById('edit_status').value = task.status_tugas;
        document.getElementById("popupEditTask").style.display = "block";
    } else {
        alert("Data tugas tidak ditemukan.");
    }
}

function deleteTask(id_tugas) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        window.location.href = 'proses_tugas.php?delete=1&id_tugas=' + id_tugas + '&id_proyek=<?= $id_proyek ?>';
    }
}

    function toggleProjectSubmenu() {
        const submenu = document.getElementById('projectSubmenu');
        submenu.style.display = (submenu.style.display === 'none' || submenu.style.display === '') ? 'block' : 'none';
    }

    function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

    function viewEvidence(bukti_foto) {
        window.open(bukti_foto, '_blank');
    }
</script>


</body>
</html>