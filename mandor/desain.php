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

// Ambil nilai limit dari GET (default 5 jika tidak ada)
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;

// Pastikan limit minimal 1
$limit = max(1, $limit);

// Ambil halaman saat ini dari GET, default halaman 1
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);

// Hitung offset
$offset = ($page - 1) * $limit;

$query = "SELECT 
            d.id_desain,
            d.nama_desain,
            ds.username AS nama_desainer,
            d.tanggal_dibuat,
            d.gambar_desain
          FROM data_desain d
          JOIN desainer ds ON d.id_desainer = ds.id_desainer
          ORDER BY d.tanggal_dibuat DESC
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($koneksi, $query);

// Ambil data revisi terbaru untuk setiap desain
$revision_data = [];
$revision_query = "SELECT id_desain, file_revisi 
                   FROM revisi_desain 
                   WHERE file_revisi IS NOT NULL AND file_revisi != ''
                   ORDER BY id_revisi DESC";
$revision_result = mysqli_query($koneksi, $revision_query);
while ($row = mysqli_fetch_assoc($revision_result)) {
    // Hanya ambil revisi pertama (terbaru) untuk setiap desain
    if (!isset($revision_data[$row['id_desain']])) {
        $revision_data[$row['id_desain']] = $row['file_revisi'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Desain</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        .dropdown-content.active {
            max-height: 200px;
        }
        .clickable {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .clickable:hover {
            transform: scale(1.02);
            opacity: 0.9;
        }
    </style>
</head>
<body class="bg-neutral-200 font-poppins">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <div class="w-96 bg-zinc-800 text-white flex flex-col justify-between relative">
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
                        <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='dataproject.php'">Data Projek</div>
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
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white shadow px-10 h-24">
            <img src="../admin/admin/assets/Vector.png" class="w-10 h-8" alt="Menu">
            <div class="flex items-center space-x-4 cursor-pointer" onclick="window.location.href='profilemandor.php'">
                <div class="w-14 h-14 rounded-full overflow-hidden bg-stone-500">
                    <img src="../<?= htmlspecialchars($gambar_profile); ?>" class="w-14 h-14 object-cover" alt="Profile">
                </div>
                <span class="text-2xl font-medium text-black"><?= htmlspecialchars($nama); ?></span>
            </div>
        </div>

        <!-- Page Title -->
        <div class="pl-10 pt-6">
            <h2 class="text-4xl font-medium text-zinc-800 mb-2">Data Desain</h2>
            <p class="text-neutral-500 text-xl">Berikut seluruh data desain yang telah disetujui</p>
        </div>

        <!-- Table Controls -->
        <div class="px-10 py-6">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center space-x-4">
                    <span class="text-zinc-800 text-xl font-medium">Show</span>
                    <form method="GET" action="" class="inline-block">
                        <select name="limit" onchange="this.form.submit()" class="w-16 h-8 bg-white border border-black rounded text-center text-zinc-800">
                            <option value="5" <?= isset($_GET['limit']) && $_GET['limit'] == 5 ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= isset($_GET['limit']) && $_GET['limit'] == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?= isset($_GET['limit']) && $_GET['limit'] == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?= isset($_GET['limit']) && $_GET['limit'] == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </form>
                    <span class="text-zinc-800 text-xl font-normal">entries</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-zinc-800 text-xl font-medium">Cari:</span>
                    <input type="text" class="w-80 h-8 bg-white rounded border border-black px-3">
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Table Header -->
                <div class="flex bg-zinc-500 text-white p-4 font-semibold">
                    <div class="flex-1 text-center">ID Desain</div>
                    <div class="flex-1 text-center">Nama Desain</div>
                    <div class="flex-1 text-center">Desainer</div>
                    <div class="flex-1 text-center">Tanggal Unggah</div>
                    <div class="flex-1 text-center">Preview</div>
                    <div class="flex-1 text-center">Aksi</div>
                </div>
                
                <!-- Table Body -->
                <div class="divide-y divide-gray-200">
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Logika untuk menampilkan gambar terbaru
                            $imagePath = "../desainer/" . htmlspecialchars($row['gambar_desain']);
                            
                            // Cek apakah ada revisi terbaru dengan file_revisi untuk desain ini
                            if (isset($revision_data[$row['id_desain']]) && !empty($revision_data[$row['id_desain']])) {
                                $imagePath = "../desainer/" . htmlspecialchars($revision_data[$row['id_desain']]);
                            }
                            
                            echo "<div class='flex items-center p-4 hover:bg-gray-50'>";
                            echo "<div class='flex-1 text-center'>" . htmlspecialchars($row['id_desain']) . "</div>";
                            echo "<div class='flex-1 text-center'>" . htmlspecialchars($row['nama_desain']) . "</div>";
                            echo "<div class='flex-1 text-center'>" . htmlspecialchars($row['nama_desainer']) . "</div>";
                            echo "<div class='flex-1 text-center'>" . htmlspecialchars($row['tanggal_dibuat']) . "</div>";
                            
                            echo "<div class='flex-1 flex justify-center'>";
                            echo "<img class='w-20 h-12 rounded border border-gray-300 object-cover' src='$imagePath' alt='Preview' />";
                            echo "</div>";
                            
                            echo "<div class='flex-1 text-center'>";
                            echo "<button class='bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded transition-colors' onclick=\"window.location.href='detaildesain.php?id=" . urlencode($row['id_desain']) . "'\">Detail</button>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='p-8 text-center text-gray-500'>Tidak ada data desain</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        dropdown.classList.toggle('active');
    }

    function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

    // Add interactive feedback
    document.addEventListener('DOMContentLoaded', function() {
        const clickableElements = document.querySelectorAll('.clickable');
        clickableElements.forEach(element => {
            element.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
            });
            element.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
        });
    });
</script>

</body>
</html>