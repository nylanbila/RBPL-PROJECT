<?php
session_start();
// Cek apakah sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "inerior");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}

$id_admin = $_SESSION['id_user'] ?? null;
$nama = "";
$gambar_profile = ""; // default jika tidak ada gambar

if ($id_admin) {
    $query = "SELECT username, gambar_profile FROM admin WHERE id_admin = $id_admin";
    $result = $mysqli->query($query);
    if ($result && $data = $result->fetch_assoc()) {
        $nama = $data['username'];
       $gambar_profile = $data['gambar_profile'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data RAB</title>
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
    </style>
</head>
<body class="bg-neutral-200 font-poppins">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <div class="w-96 bg-zinc-800 text-white flex flex-col justify-between relative">
        <div>
            <div class="flex flex-col items-center py-6">
                <img src="admin/assets/logo-nobg.png" class="w-48 h-56 object-contain" alt="Logo">
                <h1 class="text-4xl font-medium">Admin</h1>
                <hr class="border-white border-opacity-50 w-3/4 my-4">
            </div>
            <nav class="px-6 space-y-2">
                <div class="relative bg-neutral-300/70 rounded py-2 pl-2 flex items-center space-x-3 cursor-pointer hover:bg-white/10" onclick="window.location.href='dashboard_admin.php'">
                    <img src="admin/assets/Frame.png" class="w-6 h-7" alt="Dashboard">
                    <span class="text-xl font-medium">Dashboard</span>
                </div>

                <!-- RAB -->
                <div class="flex items-center space-x-3 cursor-pointer" onclick="toggleDropdown('rab-dropdown')">
                    <img src="admin/assets/Frame(1).png" class="w-6 h-7 ml-2" alt="RAB">
                    <span class="text-2xl font-medium">RAB</span>
                    <img src="admin/assets/Frame5.png" class="w-5 h-5 ml-auto" alt="Toggle">
                </div>
                <div id="rab-dropdown" class="dropdown-content pl-12 space-y-1">
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='data-rab.php'">Data RAB</div>
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='buat-rab.php'">Buat RAB</div>
                </div>

                <!-- Bahan -->
                <div class="flex items-center space-x-3 cursor-pointer" onclick="toggleDropdown('bahan-dropdown')">
                    <img src="admin/assets/Frame(2).png" class="w-6 h-6 ml-2" alt="Bahan">
                    <span class="text-2xl font-medium">Bahan</span>
                    <img src="admin/assets/Frame5.png" class="w-5 h-5 ml-auto" alt="Toggle">
                </div>
                <div id="bahan-dropdown" class="dropdown-content pl-12 space-y-1">
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='data-bahan.php'">Data Bahan</div>
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='pengajuan-bahan.php'">Pengajuan Bahan</div>
                </div>

                <!-- Desain -->
                <div class="flex items-center space-x-3 cursor-pointer" onclick="toggleDropdown('desain-dropdown')">
                    <img src="admin/assets/Frame(3).png" class="w-6 h-6 ml-2" alt="Desain">
                    <span class="text-2xl font-medium">Desain</span>
                    <img src="admin/assets/Frame5.png" class="w-5 h-5 ml-auto" alt="Toggle">
                </div>
                <div id="desain-dropdown" class="dropdown-content pl-12 space-y-1">
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='data-desain.php'">Data Desain</div>
                </div>
            </nav>
        </div>
        <div class="px-6 pb-6">
            <div class="flex items-center space-x-2 cursor-pointer" onclick="window.location.href='logout.php'">
                <img src="admin/assets/logout.png" class="w-4 h-4" alt="Logout">
                <span class="text-xl font-medium">Logout</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white shadow px-10 h-24">
            <img src="admin/assets/Vector.png" class="w-10 h-8" alt="Menu">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-full overflow-hidden bg-stone-500">
                    <img src="../<?= htmlspecialchars($gambar_profile); ?>" class="w-14 h-14 object-cover" alt="Profile">
                </div>
                <span class="text-2xl font-medium text-black"><?= htmlspecialchars($nama); ?></span>
            </div>
        </div>
        <!-- Dashboard Title -->
        <div class="pl-10 pt-4">
            <h2 class="text-4xl font-medium text-zinc-800 mb-1">Data RAB</h2>
            <p class="text-neutral-500 text-xl">Berikut Seluruh Data RAB yang Tercatat</p>
        </div>
        
<!-- Kontrol Atas Tabel -->
<div class="absolute left-[465px] top-[290px] w-[1396px] flex justify-between items-center">
  <!-- Show entries + Filter -->
  <div class="flex items-center gap-4">
    <label for="show-entries" class="text-zinc-800 text-xl font-medium font-['Poppins']">Show</label>
    <select id="show-entries" class="w-16 h-8 bg-white border border-black rounded-md text-center">
      <option value="5">5</option>
      <option value="10">10</option>
      <option value="25">25</option>
    </select>
    <span class="text-zinc-800 text-xl font-normal font-['Poppins']">entries</span>

    <!-- Filter -->
    <button class="flex items-center gap-1 px-3 py-1 bg-white border border-black rounded-md hover:bg-gray-100">
      <img src="admin/assets/Frame_filter.png" class="w-4 h-4" />
      <span class="text-zinc-800 text-base font-normal font-['Poppins']">Filter</span>
    </button>
  </div>

  <!-- Search + Tambah Data -->
  <div class="flex items-center gap-4">
    <label for="search" class="text-zinc-800 text-xl font-medium font-['Poppins']">Cari :</label>
    <input type="text" id="search" class="w-64 h-8 bg-white border border-black rounded-md px-2" placeholder="Cari...">

    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-md font-semibold text-sm">
      + Tambah Data
    </button>
  </div>
</div>

<!-- Tabel -->
<main class="absolute left-[465px] top-[345px] w-[1396px] bg-white rounded-lg shadow-md overflow-auto">
  <!-- Header -->
  <div class="grid grid-cols-8 bg-zinc-500 text-white p-4 font-semibold text-center">
    <div>No.</div>
    <div>No. RAB</div>
    <div>Nama Proyek</div>
    <div>Nama Client</div>
    <div>Tanggal Kesepakatan</div>
    <div>Jumlah</div>
    <div>Status</div>
    <div>Aksi</div>
  </div>

  <!-- Data Baris -->
<div class="grid grid-cols-8 items-center p-4 border-t text-center">
  <div>1</div>
  <div>RAB-001</div>
  <div>Interior Rumah</div>
  <div>Budi Santoso</div>
  <div>01/03/2025</div>
  <div>Rp150.000.000</div>
  <div class="text-green-600 font-semibold">Disetujui</div>
  <div>
    <a href="detail-rab.php?no_rab=RAB-001" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded">detail</a>
  </div>
</div>

<div class="grid grid-cols-8 items-center p-4 border-t text-center">
  <div>2</div>
  <div>RAB-002</div>
  <div>Desain Café Cozy</div>
  <div>Siti Rahma</div>
  <div>10/03/2025</div>
  <div>Rp180.000.000</div>
  <div class="text-yellow-600 font-semibold">Menunggu</div>
  <div>
    <a href="detail-rab.php?no_rab=RAB-002" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded">detail</a>
  </div>
</div>

<div class="grid grid-cols-8 items-center p-4 border-t text-center">
  <div>3</div>
  <div>RAB-003</div>
  <div>Konstruksi Ruang Meeting</div>
  <div>CV. Sukses</div>
  <div>20/02/2025</div>
  <div>Rp210.000.000</div>
  <div class="text-red-600 font-semibold">Revisi</div>
  <div>
    <a href="detail-rab.php?no_rab=RAB-003" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded">detail</a>
  </div>
</div>

<div class="grid grid-cols-8 items-center p-4 border-t text-center">
  <div>4</div>
  <div>RAB-004</div>
  <div>Renovasi Kantor ABC</div>
  <div>PT. ABC</div>
  <div>15/02/2025</div>
  <div>Rp275.000.000</div>
  <div class="text-orange-500 font-semibold">Proses</div>
  <div>
    <a href="detail-rab.php?no_rab=RAB-004" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded">detail</a>
  </div>
</div>

<div class="grid grid-cols-8 items-center p-4 border-t text-center">
  <div>5</div>
  <div>RAB-005</div>
  <div>Pembangunan Showroom</div>
  <div>PT. Maju Jaya</div>
  <div>05/03/2025</div>
  <div>Rp320.000.000</div>
  <div class="text-green-600 font-semibold">Disetujui</div>
  <div>
    <a href="detail-rab.php?no_rab=RAB-005" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 text-sm rounded">detail</a>
  </div>
</div>

</main>
<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('active');
}

// Navigasi kartu
    document.querySelectorAll('.card-link').forEach(card => {
        card.addEventListener('click', function () {
            const target = this.getAttribute('data-target');
            if (target) window.location.href = target;
        });
    });
</script>
</body>
</html>