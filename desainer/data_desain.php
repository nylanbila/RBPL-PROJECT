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

// Ambil nilai limit dari GET (default 5 jika tidak ada)
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;

// Pastikan limit minimal 1
$limit = max(1, $limit);

// Ambil halaman saat ini dari GET, default halaman 1
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page);

// Hitung offset
$offset = ($page - 1) * $limit;

// Ambil data user untuk profil
$id_desainer = $_SESSION['id_user'];
$nama = "";
$gambar_profile = "";

$query_user = "SELECT username, gambar_profile FROM desainer WHERE id_desainer = $id_desainer";
$result_user = mysqli_query($koneksi, $query_user);
if ($result_user && $data = mysqli_fetch_assoc($result_user)) {
    $nama = $data['username'];
    $gambar_profile = $data['gambar_profile'];
}

// Hitung total data untuk pagination
$sql_count = "SELECT COUNT(*) as total FROM data_desain WHERE id_desainer = $id_desainer";
$result_count = mysqli_query($koneksi, $sql_count);
$total_data = mysqli_fetch_assoc($result_count)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data desain untuk desainer terkait dengan pagination
$sql = "
SELECT 
    dd.id_desain, 
    dd.nama_desain, 
    dd.tanggal_dibuat, 
    dd.status_desain, 
    dd.gambar_desain,
    (
        SELECT file_revisi 
        FROM revisi_desain 
        WHERE id_desain = dd.id_desain 
            AND file_revisi IS NOT NULL AND file_revisi != '' 
        ORDER BY id_revisi DESC 
        LIMIT 1
    ) AS gambar_revisi
FROM data_desain dd 
WHERE dd.id_desainer = $id_desainer
ORDER BY dd.tanggal_dibuat DESC
LIMIT $limit OFFSET $offset";

$result = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Desain - Dashboard Desainer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .hover-effect:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-poppins">
<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <div class="w-72 min-w-[18rem] bg-zinc-800 text-white flex flex-col justify-between lg:flex hidden">
        <div>
            <div class="flex flex-col items-center py-8">
                <img src="../admin/admin/assets/logo-nobg.png" class="w-32 h-32 mb-4 object-contain" alt="Logo">
                <h1 class="text-2xl font-semibold mb-2 whitespace-nowrap">Desainer</h1>
                <hr class="border-white border-opacity-50 w-3/4 mb-4">
            </div>
            <nav class="px-6">
                <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer hover-effect" onclick="window.location.href='dashboard_desainer.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12L11.204 3.045c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="text-sm">Dashboard</span>
                </div>
                <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5">
                        <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z" clip-rule="evenodd" />
                        <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                    </svg>
                    <span class="text-sm">Data Desain</span>
                </div>
                <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer hover-effect" onclick="window.location.href='upload_desain.php'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="text-sm">Upload Desain</span>
                </div>
            </nav>
        </div>
        <div class="px-6 pb-6">
            <div class="flex items-center space-x-2 cursor-pointer hover-effect rounded px-2 py-1" onclick="logout()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
                <span class="text-sm font-medium">Logout</span>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar (Hidden by default) -->
    <div id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-zinc-800 text-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden">
        <div class="flex flex-col h-full justify-between">
            <div>
                <div class="flex justify-between items-center p-4">
                    <h1 class="text-xl font-semibold">Desainer</h1>
                    <button id="close-sidebar" class="text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <nav class="px-4">
                    <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer hover-effect" onclick="window.location.href='dashboard_desainer.php'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12L11.204 3.045c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        <span class="text-sm">Dashboard</span>
                    </div>
                    <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer bg-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z" clip-rule="evenodd" />
                            <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                        </svg>
                        <span class="text-sm">Data Desain</span>
                    </div>
                    <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer hover-effect" onclick="window.location.href='upload_desain.php'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span class="text-sm">Upload Desain</span>
                    </div>
                </nav>
            </div>
            <div class="px-4 pb-6">
                <div class="flex items-center space-x-2 cursor-pointer hover-effect rounded px-2 py-1" onclick="logout()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                    </svg>
                    <span class="text-sm font-medium">Logout</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white shadow px-4 lg:px-10 h-16 lg:h-24">
            <div class="flex items-center">
                <button id="mobile-menu-btn" class="lg:hidden mr-4 text-zinc-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                 <img src="../admin/admin/assets/Vector.png" class="w-10 h-8" alt="Menu">
            </div>
            <div class="flex items-center space-x-2 lg:space-x-4 cursor-pointer" onclick="window.location.href='profiledesainer.php'">
                <div class="w-10 h-10 lg:w-14 lg:h-14 rounded-full overflow-hidden bg-stone-500">
                    <img class="w-10 h-10 lg:w-14 lg:h-14 object-cover" src="../<?= htmlspecialchars($gambar_profile); ?>" alt="Profile" />
                </div>
                <span class="text-sm lg:text-xl font-medium text-black"><?= htmlspecialchars($nama); ?></span>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Title -->
            <div class="px-4 lg:px-10 py-4 lg:py-6">
                <h2 class="text-2xl lg:text-3xl font-semibold text-zinc-800 mb-2">Data Desain</h2>
                <p class="text-gray-500 text-base lg:text-lg">Berikut seluruh data desain anda</p>
            </div>

            <!-- Controls -->
            <div class="px-4 lg:px-10 mb-4">
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm lg:text-base text-zinc-800">Show</span>
                        <form method="GET" action="" class="inline">
                            <input type="hidden" name="page" value="<?= $page ?>">
                            <select name="limit" onchange="this.form.submit()" class="px-2 py-1 border border-black rounded text-sm lg:text-base">
                                <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
                                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                            </select>
                        </form>
                        <span class="text-sm lg:text-base text-zinc-800">entries</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm lg:text-base text-zinc-800">Search:</span>
                        <input type="text" class="px-3 py-1 border border-black rounded w-full lg:w-64" placeholder="Cari desain...">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="px-4 lg:px-10 mb-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden lg:block table-responsive">
                        <div class="bg-zinc-500 text-white p-4 font-semibold grid grid-cols-6 gap-4">
                            <div>ID Desain</div>
                            <div>Nama Desain</div>
                            <div>Tanggal Dibuat</div>
                            <div>Status Review</div>
                            <div>Preview</div>
                            <div>Aksi</div>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php 
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $badgeColor = 'bg-gray-300';
                                    if ($row['status_desain'] == 'Disetujui') $badgeColor = 'bg-green-500';
                                    elseif ($row['status_desain'] == 'Revisi') $badgeColor = 'bg-red-500';
                                    elseif ($row['status_desain'] == 'Menunggu') $badgeColor = 'bg-yellow-400';

                                    echo "<div class='grid grid-cols-6 gap-4 p-4 items-center hover:bg-gray-50'>";
                                    echo "<div class='text-sm'>" . htmlspecialchars($row['id_desain']) . "</div>";
                                    echo "<div class='text-sm'>" . htmlspecialchars($row['nama_desain']) . "</div>";
                                    echo "<div class='text-sm'>" . htmlspecialchars($row['tanggal_dibuat']) . "</div>";
                                    echo "<div><span class='text-white px-2 py-1 rounded text-xs $badgeColor'>" . htmlspecialchars($row['status_desain']) . "</span></div>";
                                 
                                    // Logika yang sama seperti detail_desain.php
                                    if (!empty($row['gambar_revisi'])) {
                                        // Jika ada file revisi, gunakan file revisi
                                        $gambar_path = "" . htmlspecialchars($row['gambar_revisi']);
                                    } else {
                                        // Jika tidak ada file revisi, gunakan gambar desain asli
                                        $gambar_path = "" . htmlspecialchars($row['gambar_desain']);
                                    }
                                    
                                    echo "<div><img src='$gambar_path' alt='Preview' class='w-20 h-12 object-cover rounded border'></div>";
                    
                                    echo "<div><a href='detail_desain.php?id=" . $row['id_desain'] . "' class='bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded'>Detail</a></div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<div class='text-center text-gray-500 py-8'>Tidak ada data desain</div>";
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="lg:hidden space-y-4 p-4">
                        <?php 
                        // Reset result pointer for mobile view
                        mysqli_data_seek($result, 0);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $badgeColor = 'bg-gray-300';
                                if ($row['status_desain'] == 'Disetujui') $badgeColor = 'bg-green-500';
                                elseif ($row['status_desain'] == 'Revisi') $badgeColor = 'bg-red-500';
                                elseif ($row['status_desain'] == 'Menunggu') $badgeColor = 'bg-yellow-400';

                                // Logika yang sama seperti detail_desain.php untuk mobile
                                if (!empty($row['gambar_revisi'])) {
                                    // Jika ada file revisi, gunakan file revisi
                                    $gambar_path = "img/revisi/" . htmlspecialchars($row['gambar_revisi']);
                                } else {
                                    // Jika tidak ada file revisi, gunakan gambar desain asli
                                    $gambar_path = "img/" . htmlspecialchars($row['gambar_desain']);
                                }

                                echo "<div class='bg-white border rounded-lg p-4 shadow-sm'>";
                                echo "<div class='flex items-start gap-3'>";
                                echo "<img src='$gambar_path' alt='Preview' class='w-16 h-16 object-cover rounded border flex-shrink-0'>";
                                echo "<div class='flex-1 min-w-0'>";
                                echo "<h3 class='font-medium text-sm text-gray-900 truncate'>" . htmlspecialchars($row['nama_desain']) . "</h3>";
                                echo "<p class='text-xs text-gray-500 mt-1'>ID: " . htmlspecialchars($row['id_desain']) . "</p>";
                                echo "<p class='text-xs text-gray-500'>" . htmlspecialchars($row['tanggal_dibuat']) . "</p>";
                                echo "<div class='flex items-center justify-between mt-2'>";
                                echo "<span class='text-white px-2 py-1 rounded text-xs $badgeColor'>" . htmlspecialchars($row['status_desain']) . "</span>";
                                echo "<a href='detail_desain.php?id=" . $row['id_desain'] . "' class='bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded'>Detail</a>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<div class='text-center text-gray-500 py-8'>Tidak ada data desain</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="px-4 lg:px-10 mb-6">
                <div class="flex justify-center">
                    <nav class="flex space-x-2">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-50">Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>&limit=<?= $limit ?>" class="px-3 py-1 border rounded <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-white hover:bg-gray-50' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>" class="px-3 py-1 bg-white border rounded hover:bg-gray-50">Next</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const overlay = document.getElementById('overlay');
    const closeSidebar = document.getElementById('close-sidebar');

    mobileMenuBtn.addEventListener('click', () => {
        mobileSidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });

    closeSidebar.addEventListener('click', () => {
        mobileSidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    overlay.addEventListener('click', () => {
        mobileSidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });

    // Logout function
    function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

    // Search functionality
    document.querySelector('input[placeholder="Cari desain..."]').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.grid.grid-cols-6.gap-4.p-4');
        const mobileCards = document.querySelectorAll('.bg-white.border.rounded-lg.p-4.shadow-sm');

        // Filter desktop table rows
        rows.forEach(row => {
            const namaDesain = row.querySelector('div:nth-child(2)')?.textContent.toLowerCase();
            const idDesain = row.querySelector('div:nth-child(1)')?.textContent.toLowerCase();
            
            if (namaDesain && idDesain) {
                if (namaDesain.includes(searchTerm) || idDesain.includes(searchTerm)) {
                    row.style.display = 'grid';
                } else {
                    row.style.display = 'none';
                }
            }
        });

        // Filter mobile cards
        mobileCards.forEach(card => {
            const namaDesain = card.querySelector('h3')?.textContent.toLowerCase();
            const idDesain = card.querySelector('p')?.textContent.toLowerCase();
            
            if (namaDesain && idDesain) {
                if (namaDesain.includes(searchTerm) || idDesain.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    });

    // Auto-refresh status (optional)
    setInterval(function() {
        // Refresh page every 30 seconds to check for status updates
        // You can remove this if not needed
        // window.location.reload();
    }, 30000);

    // Responsive table handling
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            // Desktop view
            mobileSidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    });

    // Image preview hover effect
    document.querySelectorAll('img[alt="Preview"]').forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Status badge animation
    document.querySelectorAll('.bg-yellow-400, .bg-green-500, .bg-red-500').forEach(badge => {
        if (badge.textContent.trim() === 'Menunggu') {
            badge.style.animation = 'pulse 2s infinite';
        }
    });

    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .hover\\:scale-105:hover {
            transform: scale(1.05);
        }
        
        .transition-all {
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);
</script>

</body>
</html>

<?php
// Close database connection
mysqli_close($koneksi);
?>