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

$query = "SELECT 
            d.id_desain,
            d.nama_desain,
            ds.username AS nama_desainer,
            d.tanggal_dibuat,
            d.status_desain,
            d.gambar_desain
          FROM data_desain d
          JOIN desainer ds ON d.id_desainer = ds.id_desainer
          ORDER BY d.tanggal_dibuat DESC
              LIMIT $entriesPerPage";
$result = mysqli_query($koneksi, $query);

// Ambil semua data feedback dan simpan dalam array
$feedback_data = [];
$feedback_query = "SELECT id_desain, feedback_admin, tanggal_upload_revisi, deadline_revisi 
                   FROM revisi_desain 
                   ORDER BY tanggal_upload_revisi DESC";
$feedback_result = mysqli_query($koneksi, $feedback_query);
while ($row = mysqli_fetch_assoc($feedback_result)) {
    $feedback_data[$row['id_desain']][] = $row;
}

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
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 700px;
            max-height: 70vh;
            overflow-y: auto;
            position: relative;
        }
        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
        }
        .close-btn:hover {
            color: #000;
        }
    </style>
</head>
<body class="bg-neutral-200 font-poppins">
<div class="flex min-h-screen overflow-auto flex-col md:flex-row">
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
            <div class="flex items-center space-x-2 cursor-pointer hover:bg-white/10 p-2 rounded" onclick="logout()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                </svg>
                <span class="text-xl font-medium">Logout</span>
            </div>
        </div>
    </div>
  
    <!-- Main Content -->
<div class="flex-1 flex flex-col overflow-x-auto">
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
        <div class="px-10 py-6">
            <h2 class="text-4xl font-medium text-zinc-800 mb-1 text-left">Data Desain</h2>
            <p class="text-neutral-500 text-xl">Berikut Seluruh Data Desain yang Tercatat</p>
        </div>

        <!-- Controls -->
        <div class="px-10 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
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
                    <button class="bg-white border border-black rounded px-4 py-1 flex items-center space-x-2">
                        <span class="text-zinc-800 text-base">Filter</span>
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-zinc-800 text-xl font-medium">Cari:</span>
                    <input type="text" class="bg-white border border-black rounded px-3 py-1 w-80">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="px-10 flex-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Table Header -->
                <div class="bg-zinc-500 text-white">
                    <div class="grid grid-cols-7 gap-4 p-4 font-semibold">
                        <div class="text-center">ID Desain</div>
                        <div class="text-center">Nama Desain</div>
                        <div class="text-center">Desainer</div>
                        <div class="text-center">Tanggal Unggah</div>
                        <div class="text-center">Status Review</div>
                        <div class="text-center">Preview</div>
                        <div class="text-center">Aksi</div>
                    </div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-gray-200">
                    <?php 
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $badgeColor = 'bg-gray-300';
                            if ($row['status_desain'] == 'Disetujui') $badgeColor = 'bg-green-500';
                            elseif ($row['status_desain'] == 'Revisi') $badgeColor = 'bg-red-500';
                            elseif ($row['status_desain'] == 'Menunggu') $badgeColor = 'bg-yellow-400';

                            // Logika untuk menampilkan gambar terbaru
                            $imagePath = "../desainer/" . htmlspecialchars($row['gambar_desain']);
                            
                            // Cek apakah ada revisi terbaru dengan file_revisi untuk desain ini
                            if (isset($revision_data[$row['id_desain']]) && !empty($revision_data[$row['id_desain']])) {
                                $imagePath = "../desainer/" . htmlspecialchars($revision_data[$row['id_desain']]);
                            }
                            
                            echo "<div class='grid grid-cols-7 gap-4 p-4 items-center'>";
                            echo "<div class='text-center'>" . htmlspecialchars($row['id_desain']) . "</div>";
                            echo "<div class='text-center'>" . htmlspecialchars($row['nama_desain']) . "</div>";
                            echo "<div class='text-center'>" . htmlspecialchars($row['nama_desainer']) . "</div>";
                            echo "<div class='text-center'>" . htmlspecialchars($row['tanggal_dibuat']) . "</div>";
                            echo "<div class='text-center'><span class='text-white px-2 py-1 rounded text-xs $badgeColor'>" . htmlspecialchars($row['status_desain']) . "</span></div>";
                            echo "<div class='flex justify-center'><img class='w-24 h-16 rounded-lg border border-black object-cover' src='$imagePath' alt='Preview' /></div>";
                            echo "<div class='flex justify-center space-x-2'>";
                            echo "<button class='bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded' onclick=\"window.location.href='detaildesain_admin.php?id=" . urlencode($row['id_desain']) . "'\">Detail</button>";
                            echo "<button class='bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded' onclick=\"showFeedback(" . $row['id_desain'] . ")\">Feedback</button>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='p-4 text-center text-gray-500'>Tidak ada data desain</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Feedback -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeFeedback()">&times;</span>
        <h2 class="text-2xl font-bold mb-6 text-zinc-800">Feedback Revisi</h2>
        <div id="feedbackContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- JavaScript untuk menyimpan data feedback -->
<script>
// Simpan data feedback dalam JavaScript
const feedbackData = <?php echo json_encode($feedback_data); ?>;

function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('active');
}

function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

// Navigasi kartu
document.querySelectorAll('.card-link').forEach(card => {
    card.addEventListener('click', function () {
        const target = this.getAttribute('data-target');
        if (target) window.location.href = target;
    });
});

// Fungsi untuk menampilkan feedback (versi sederhana)
function showFeedback(idDesain) {
    const modal = document.getElementById('feedbackModal');
    const content = document.getElementById('feedbackContent');
    
    // Tampilkan modal
    modal.style.display = 'block';
    
    // Cek apakah ada feedback untuk desain ini
    const feedback = feedbackData[idDesain];
    
    if (!feedback || feedback.length === 0) {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-info-circle text-4xl text-gray-400 mb-4"></i>
                <p class="text-lg text-gray-500">Belum ada feedback untuk desain ini.</p>
            </div>
        `;
    } else {
        let html = '';
        feedback.forEach((item, index) => {
            html += `
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold text-lg text-zinc-800">Feedback #${index + 1}</h4>
                        <span class="text-sm text-gray-500">${item.tanggal_upload_revisi}</span>
                    </div>
                    ${item.deadline_revisi ? `
                        <div class="mb-2">
                            <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
                                Deadline: ${item.deadline_revisi}
                            </span>
                        </div>
                    ` : ''}
                    <p class="text-gray-700 leading-relaxed">${item.feedback_admin}</p>
                </div>
            `;
        });
        content.innerHTML = html;
    }
}

// Fungsi untuk menutup modal
function closeFeedback() {
    document.getElementById('feedbackModal').style.display = 'none';
}

// Tutup modal ketika klik di luar modal
window.onclick = function(event) {
    const modal = document.getElementById('feedbackModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>
</body>
</html>