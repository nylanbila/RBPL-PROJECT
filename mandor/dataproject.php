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

// Ambil jumlah entri per halaman dari GET, default 5
$entriesPerPage = isset($_GET['entries']) ? intval($_GET['entries']) : 5;
$entriesPerPage = ($entriesPerPage > 0) ? $entriesPerPage : 5; // fallback jika nilai tidak valid


$sql = "SELECT * FROM data_proyek     
        LIMIT $entriesPerPage";
$result = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Project - Dashboard Mandor</title>
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
        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .mobile-menu.active {
            transform: translateX(0);
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table-responsive table {
            min-width: 800px;
        }
        @media (max-width: 768px) {
            .table-responsive table {
                font-size: 0.875rem;
            }
            .table-responsive th,
            .table-responsive td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body class="bg-neutral-200 font-poppins">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar - Desktop -->
    <div class="hidden lg:flex w-96 bg-zinc-800 text-white flex-col justify-between">
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
    </div>

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
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white shadow px-4 lg:px-10 h-16 lg:h-24">
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

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto">
            <!-- Page Title -->
            <div class="px-4 lg:px-10 pt-6 lg:pt-8 mb-4 lg:mb-6">
                <h2 class="text-2xl lg:text-4xl font-medium text-zinc-800 mb-2">Data Projek</h2>
                <p class="text-neutral-500 text-base lg:text-xl">Berikut data projek yang tersedia</p>
            </div>

            <!-- Content Container -->
            <div class="px-4 lg:px-10 pb-6 lg:pb-10">
                <div class="bg-white rounded-lg shadow-lg p-4 lg:p-8">
                    <!-- Top Controls -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <form method="GET" id="entriesForm" class="flex items-center space-x-2">
                            <span class="text-zinc-800 text-xl font-medium">Show</span>
                            <select name="entries" onchange="document.getElementById('entriesForm').submit()" class="bg-white border border-black rounded-sm px-2 py-1 text-lg">
                            <option value="5" <?= (!isset($_GET['entries']) || $_GET['entries'] == 5) ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= (isset($_GET['entries']) && $_GET['entries'] == 10) ? 'selected' : '' ?>>10</option>
                            <option value="15" <?= (isset($_GET['entries']) && $_GET['entries'] == 15) ? 'selected' : '' ?>>15</option>

                            </select>
                            <span class="text-zinc-800 text-xl font-normal">entries</span>
                        </form>
                        </div>
                    </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                            <input type="text" placeholder="Search..." class="border rounded px-3 py-2 text-sm w-full sm:w-auto"/>
                            <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors text-sm font-medium" onclick="window.location.href='buatproject.php'">
                                <span class="hidden sm:inline">Tambah Data</span>
                                <span class="sm:hidden">+ Tambah</span>
                            </button>
                        </div>
                    </div>

                    <!-- Table Container -->
                    <div class="table-responsive">
                        <table class="w-full text-sm border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-2 lg:px-4 py-2 text-left">ID</th>
                                    <th class="border px-2 lg:px-4 py-2 text-left">Nama Proyek</th>
                                    <th class="border px-2 lg:px-4 py-2 text-left">Client</th>
                                    <th class="border px-2 lg:px-4 py-2 text-left">Mulai</th>
                                    <th class="border px-2 lg:px-4 py-2 text-left">Selesai</th>
                                    <th class="border px-2 lg:px-4 py-2 text-left">Progress</th>
                                    <th class="border px-2 lg:px-4 py-2 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $id_proyek = $row['id_proyek'];
                                        $query_total = "SELECT COUNT(*) as total FROM tugas_proyek WHERE id_proyek = $id_proyek";
                                        $query_selesai = "SELECT COUNT(*) as selesai FROM tugas_proyek WHERE id_proyek = $id_proyek AND status_tugas = 'Selesai'";
                                        $total_tugas = mysqli_fetch_assoc(mysqli_query($koneksi, $query_total))['total'];
                                        $selesai = mysqli_fetch_assoc(mysqli_query($koneksi, $query_selesai))['selesai'];
                                        if ($row['status'] == 'Selesai') {
                                            $progress = 100;
                                        } else {
                                            $progress = ($total_tugas > 0) ? round(($selesai / $total_tugas) * 100) : 0;
                                        }

                                        echo "<tr class='hover:bg-gray-50'>";
                                        echo "<td class='border px-2 lg:px-4 py-2 font-medium'>" . htmlspecialchars($row['id_proyek']) . "</td>";
                                        echo "<td class='border px-2 lg:px-4 py-2'>" . htmlspecialchars($row['nama_proyek']) . "</td>";
                                        echo "<td class='border px-2 lg:px-4 py-2'>" . htmlspecialchars($row['nama_klien']) . "</td>";
                                        echo "<td class='border px-2 lg:px-4 py-2'>" . htmlspecialchars($row['tanggal_mulai']) . "</td>";
                                        echo "<td class='border px-2 lg:px-4 py-2'>" . htmlspecialchars($row['tanggal_selesai']) . "</td>";
                                        echo "<td class='border px-2 lg:px-4 py-2'>
                                                <div class='w-full bg-gray-200 rounded-full h-3 lg:h-4'>
                                                    <div class='bg-green-500 h-3 lg:h-4 rounded-full flex items-center justify-center text-white text-xs' style='width: {$progress}%;'>
                                                        <span class='text-xs'>" . $progress . "%</span>
                                                    </div>
                                                </div>
                                              </td>";
                                        echo "<td class='border px-2 lg:px-4 py-2'>
                                                <div class='flex flex-col sm:flex-row gap-1'>
                                                    <a href='detail_proyek.php?id=" . $id_proyek . "' class='bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 transition-colors text-center'>Detail</a>
                                                    <a href='hapus_proyek.php?id_proyek=" . $id_proyek . "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus proyek ini?');\" class='bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600 transition-colors text-center'>Hapus</a>
                                                </div>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center text-gray-500 py-8'>
                                            <div class='flex flex-col items-center'>
                                                <svg class='w-16 h-16 text-gray-300 mb-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'></path>
                                                </svg>
                                                <p class='text-lg font-medium'>Tidak ada data proyek</p>
                                                <p class='text-sm text-gray-400 mt-1'>Mulai dengan membuat proyek baru</p>
                                            </div>
                                          </td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                        <div class="text-sm text-gray-600">
                            Showing 1 to <?= mysqli_num_rows($result) ?> of <?= mysqli_num_rows($result) ?> entries
                        </div>
                        <div class="flex space-x-1">
                            <button class="px-3 py-1 border rounded text-sm hover:bg-gray-100 transition-colors">Previous</button>
                            <button class="px-3 py-1 border rounded text-sm bg-blue-500 text-white">1</button>
                            <button class="px-3 py-1 border rounded text-sm hover:bg-gray-100 transition-colors">Next</button>
                        </div>
                    </div>
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

    function toggleMobileMenu() {
        const mobileSidebar = document.getElementById('mobileSidebar');
        mobileSidebar.classList.toggle('active');
    }

    function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

    // Close mobile menu when clicking outside