<?php
session_start();
// Cek apakah sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}
// Koneksi database
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mandor</title>
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

        <!-- Dashboard Title -->
        <div class="pl-10 pt-4">
            <h2 class="text-4xl font-medium text-zinc-800 mb-1 text-left">Dashboard</h2>
            <p class="text-neutral-500 text-xl">Silahkan pilih salah satu menu di bawah ini</p>
        </div>

        <!-- Cards -->
    <div class="px-10 pt-8 flex flex-wrap gap-8">
            <!-- Buat Projek Card -->
            <div class="w-80 h-44 relative card-link cursor-pointer clickable" onclick="window.location.href='buatproject.php'">
                <div class="w-4 h-44 absolute left-0 top-0 bg-zinc-500 rounded-[5px]"></div>
                <div class="w-72 h-44 absolute left-[12.95px] top-0 bg-white shadow rounded-lg flex flex-col justify-center pl-8 pr-4">
                    <div class="flex justify-between items-center">
                        <div class="text-stone-500 text-2xl font-semibold">Buat Projek</div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-14 h-14 text-stone-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Data Projek Card -->
            <div class="w-80 h-44 relative card-link cursor-pointer clickable" onclick="window.location.href='dataproject.php'">
                <div class="w-4 h-44 absolute left-0 top-0 bg-zinc-500 rounded-[5px]"></div>
                <div class="w-72 h-44 absolute left-[12.95px] top-0 bg-white shadow rounded-lg flex flex-col justify-center pl-8 pr-4">
                    <div class="flex justify-between items-center">
                        <div class="text-stone-500 text-2xl font-semibold">Data Projek</div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-14 h-14 text-stone-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125M3.375 19.5a1.125 1.125 0 0 0 1.125 1.125m0 0h7.5c.621 0 1.125-.504 1.125-1.125m0 0V10.5c0-.621-.504-1.125-1.125-1.125H4.5c-.621 0-1.125.504-1.125 1.125v7.875m0 0a1.125 1.125 0 0 0 1.125 1.125h7.5c.621 0 1.125-.504 1.125-1.125m0 0V12a1.125 1.125 0 0 1 1.125-1.125h6.75c.621 0 1.125.504 1.125 1.125v7.875m0 0a1.125 1.125 0 0 0 1.125 1.125h7.5c.621 0 1.125-.504 1.125-1.125M7.5 12a1.125 1.125 0 0 1 1.125-1.125h6.75c.621 0 1.125.504 1.125 1.125v7.875m0 0a1.125 1.125 0 0 0 1.125 1.125h7.5c.621 0 1.125-.504 1.125-1.125M14.25 12a1.125 1.125 0 0 1 1.125-1.125h6.75c.621 0 1.125.504 1.125 1.125v7.875m0 0a1.125 1.125 0 0 0 1.125 1.125h7.5c.621 0 1.125-.504 1.125-1.125" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Desain Projek Card -->
            <div class="w-80 h-44 relative card-link cursor-pointer clickable" onclick="window.location.href='desain.php'">
                <div class="w-4 h-44 absolute left-0 top-0 bg-zinc-500 rounded-[5px]"></div>
                <div class="w-72 h-44 absolute left-[12.95px] top-0 bg-white shadow rounded-lg flex flex-col justify-center pl-8 pr-4">
                    <div class="flex justify-between items-center">
                        <div class="text-stone-500 text-2xl font-semibold">Desain Projek</div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-14 h-14 text-stone-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
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

    function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

    // Add some interactive feedback
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