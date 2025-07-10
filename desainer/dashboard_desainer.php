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

$id_desainer = $_SESSION['id_user'] ?? null;
$nama = "";
$gambar_profile = ""; // default jika tidak ada gambar

if ($id_desainer) {
    $query = "SELECT username, gambar_profile FROM desainer WHERE id_desainer = $id_desainer";
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
    <title>Dashboard Desainer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .card-link {
            transition: all 0.3s ease;
        }
        .card-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gray-100 font-poppins">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <div class="w-72 min-w-[18rem] bg-zinc-800 text-white flex flex-col justify-between">
        <div>
            <div class="flex flex-col items-center py-8">
                <img src="../admin/admin/assets/logo-nobg.png" class="w-32 h-32 mb-4 object-contain" alt="Logo">
                <h1 class="text-2xl font-semibold mb-2 whitespace-nowrap">Desainer</h1>
                <hr class="border-white border-opacity-50 w-3/4 mb-4">
            </div>
            <nav class="px-6">
                <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer hover:bg-white/20" onclick="window.location.href='dashboard_desainer.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12L11.204 3.045c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="text-sm">Dashboard</span>
                </div>
                <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer hover:bg-white/20" onclick="window.location.href='data_desain.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z" clip-rule="evenodd" />
                        <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                    </svg>
                    <span class="text-sm">Data Desain</span>
                </div>
                <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer hover:bg-white/20" onclick="window.location.href='upload_desain.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="text-sm">Upload Desain</span>
                </div>
            </nav>
        </div>
        <div class="px-6 pb-6">
            <div class="flex items-center space-x-2 cursor-pointer hover:bg-white/20 rounded px-2 py-1" onclick="logout()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
                <span class="text-sm font-medium">Logout</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white shadow px-10 h-24">
     <img src="../admin/admin/assets/Vector.png" class="w-10 h-8" alt="Menu">
            <div class="flex items-center space-x-4 cursor-pointer" onclick="window.location.href='profiledesainer.php'">
                <div class="w-14 h-14 rounded-full overflow-hidden bg-stone-500">
                    <img class="w-14 h-14 object-cover" src="../<?= htmlspecialchars($gambar_profile); ?>" alt="Profile" />
                </div>
                <span class="text-xl font-medium text-black"><?= htmlspecialchars($nama); ?></span>
            </div>
        </div>

        <!-- Dashboard Title -->
        <div class="px-10 py-6">
            <h2 class="text-3xl font-semibold text-zinc-800 mb-1">Dashboard</h2>
            <p class="text-gray-500 text-lg">Silahkan pilih salah satu menu di bawah ini</p>
        </div>

        <!-- Cards -->
        <div class="px-10 flex space-x-8">
            <div class="w-80 h-44 relative card-link cursor-pointer" onclick="window.location.href='data_desain.php'">
                <div class="w-4 h-44 absolute left-0 top-0 bg-zinc-500 rounded-[5px]"></div>
                <div class="w-72 h-44 absolute left-[12.95px] top-0 bg-white shadow rounded-lg flex flex-col justify-center pl-8 pr-4">
                    <div class="flex justify-between items-center">
                        <div class="text-stone-500 text-xl font-semibold font-['Poppins']">Data Desain</div>
                        <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-gray-600">
                                <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z" clip-rule="evenodd" />
                                <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-80 h-44 relative card-link cursor-pointer" onclick="window.location.href='upload_desain.php'">
                <div class="w-4 h-44 absolute left-0 top-0 bg-zinc-500 rounded-[5px]"></div>
                <div class="w-72 h-44 absolute left-[12.95px] top-0 bg-white shadow rounded-lg flex flex-col justify-center pl-8 pr-4">
                    <div class="flex justify-between items-center">
                        <div class="text-stone-500 text-xl font-semibold font-['Poppins']">Upload Desain</div>
                        <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function logout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = '../logout.php';
    }
}
</script>
</body>
</html>