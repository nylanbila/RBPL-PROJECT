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

$id_desainer = $_SESSION['id_user'];
$nama = "";
$gambar_profile = "";

$query_user = "SELECT username, gambar_profile FROM desainer WHERE id_desainer = $id_desainer";
$result_user = mysqli_query($koneksi, $query_user);
if ($result_user && $data = mysqli_fetch_assoc($result_user)) {
    $nama = $data['username'];
    $gambar_profile = $data['gambar_profile'];
}

// Ambil parameter ID dari URL
if (isset($_GET['id'])) {
    $id_desain = mysqli_real_escape_string($koneksi, $_GET['id']);
} else {
    $id_desain = "";
}

// Query untuk mengambil data desain berdasarkan ID
if (!empty($id_desain)) {
    $sql = "SELECT * FROM data_desain WHERE id_desain = '$id_desain'";
    $result = mysqli_query($koneksi, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Assign data dari database ke variabel
        $id_desainer = $row['id_desainer'] ?? '';
        $nama_desain = $row['nama_desain'] ?? '';
        $tanggal_dibuat = $row['tanggal_dibuat'] ?? '';
        $ukuran = $row['ukuran'] ?? '';
        $material = $row['material'] ?? '';
        $status_desain = $row['status_desain'] ?? '';
        $deskripsi_desain = $row['deskripsi_desain'] ?? '';
        $gambar_desain = $row['gambar_desain'] ?? '';
    }
}

// Menggambil feedback dan deadline dari table revisi_desain
$feedback = '';
$deadline = '';

$query_feedback = "SELECT feedback_admin, deadline_revisi 
                   FROM revisi_desain 
                   WHERE id_desain = '$id_desain' 
                   ORDER BY id_revisi DESC LIMIT 1";

$result_feedback = mysqli_query($koneksi, $query_feedback);

if ($result_feedback && mysqli_num_rows($result_feedback) > 0) {
    $row_feedback = mysqli_fetch_assoc($result_feedback);
    $feedback = $row_feedback['feedback_admin'];
    $deadline = date('d/m/Y', strtotime($row_feedback['deadline_revisi']));
}

// Membuat array design untuk kompatibilitas
$design = [
    'name' => $nama_desain,
    'designer' => $id_desainer,
    'upload_date' => date('d-m-Y', strtotime($tanggal_dibuat)),
    'size' => $ukuran,
    'material' => $material,
    'image' => $gambar_desain,
    'deskripsi_desain' => $deskripsi_desain,
    'status' => $status_desain,
    'feedback' => $feedback,
    'due_date' => $deadline,
];

// Cek apakah ada revisi terbaru dengan file_revisi
$query_revisi = "SELECT file_revisi FROM revisi_desain 
                 WHERE id_desain = '$id_desain' 
                 AND file_revisi IS NOT NULL AND file_revisi != ''
                 ORDER BY id_revisi DESC LIMIT 1";

$result_revisi = mysqli_query($koneksi, $query_revisi);
if ($result_revisi && mysqli_num_rows($result_revisi) > 0) {
    $row_revisi = mysqli_fetch_assoc($result_revisi);
    $design['image'] = $row_revisi['file_revisi'];
}

// Alert handling
$status = $_GET['status'] ?? '';
$alert = '';

if ($status == 'berhasil') {
    $alert = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Berhasil!</strong> Revisi desain berhasil diunggah.
              </div>';
} elseif ($status == 'gagal') {
    $alert = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Gagal!</strong> Terjadi kesalahan saat mengunggah revisi.
              </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Desain - Dashboard Desainer</title>
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
        @keyframes fade-in {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out forwards;
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
    <div class="flex-1 flex flex-col overflow-hidden">
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

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto">
            <div class="px-10 py-6">
                <!-- Page Title -->
                <div class="mb-6">
                    <h2 class="text-3xl font-semibold text-zinc-800 mb-2">Detail Desain</h2>
                    <p class="text-gray-500 text-lg">Berikut detail desain <?php echo htmlspecialchars($design['name']); ?></p>
                </div>

                <!-- Alert -->
                <?php if (!empty($alert)): ?>
                    <div class="mb-6">
                        <?php echo $alert; ?>
                    </div>
                <?php endif; ?>

                <!-- Detail Content -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Gambar -->
                        <div class="flex justify-center">
                            <img class="w-full max-w-md h-96 rounded-lg border border-gray-200 object-cover shadow-md"
                                 src="<?php echo htmlspecialchars($design['image']); ?>"
                                 alt="<?php echo htmlspecialchars($design['name']); ?>" />
                        </div>

                        <!-- Detail Information -->
                        <div class="space-y-4">
                            <h3 class="text-2xl font-semibold text-zinc-800 mb-4"><?php echo htmlspecialchars($design['name']); ?></h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">ID Desain</span>
                                    <p class="text-base text-gray-900"><?php echo $id_desain; ?></p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tanggal Unggah</span>
                                    <p class="text-base text-gray-900"><?php echo $design['upload_date']; ?></p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Ukuran</span>
                                    <p class="text-base text-gray-900"><?php echo $design['size']; ?></p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Status</span>
                                    <div class="mt-1">
                                        <?php
                                        $status = $design['status'];
                                        $badgeColor = '';

                                        if ($status == 'Disetujui') {
                                            $badgeColor = 'bg-green-500';
                                        } elseif ($status == 'Revisi') {
                                            $badgeColor = 'bg-red-500';
                                        } elseif ($status == 'Menunggu') {
                                            $badgeColor = 'bg-yellow-400';
                                        } else {
                                            $badgeColor = 'bg-gray-400';
                                        }
                                        ?>
                                        <span class="inline-block text-white px-3 py-1 rounded-full text-sm font-semibold <?php echo $badgeColor; ?>">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <span class="text-sm font-medium text-gray-500">Material</span>
                                <p class="text-base text-gray-900 mt-1"><?php echo $design['material']; ?></p>
                            </div>

                            <div>
                                <span class="text-sm font-medium text-gray-500">Deskripsi</span>
                                <p class="text-base text-gray-900 mt-1"><?php echo $design['deskripsi_desain']; ?></p>
                            </div>

                            <?php if (!empty($design['due_date'])): ?>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Batas Waktu Revisi</span>
                                <p class="text-base text-red-500 mt-1 font-semibold"><?php echo $design['due_date']; ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($design['feedback'])): ?>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Feedback</span>
                                <p class="text-base text-red-500 mt-1"><?php echo $design['feedback']; ?></p>
                            </div>
                            <?php endif; ?>

                            <!-- Action Button -->
                            <div class="pt-4">
                                <button onclick="document.getElementById('popupForm').style.display='block';"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                                    Upload Revisi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popup Form Upload Desain -->
<div id="popupForm" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
    <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg animate-fade-in relative">
        <button onclick="document.getElementById('popupForm').classList.add('hidden');"
                class="absolute top-2 right-3 text-gray-600 hover:text-black text-2xl font-bold">&times;</button>

        <h2 class="text-xl font-semibold mb-4 text-center">Revisi Desain</h2>

        <form action="proses_revisi.php" method="POST" class="space-y-4" enctype="multipart/form-data">
            <input type="hidden" name="id_desain" value="<?= $id_desain; ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Desain</label>
                <input type="text" name="nama_desain" value="<?php echo $design['name']; ?>"
                       class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran</label>
                <input type="text" name="ukuran" value="<?php echo $design['size']; ?>"
                       class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Material</label>
                <input type="text" name="material" value="<?php echo $design['material']; ?>"
                       class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Desain</label>
                <textarea name="deskripsi_desain" rows="3"
                          class="w-full border border-gray-300 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo $design['deskripsi_desain']; ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Desain Revisi</label>
                <label for="file_revisi"
                       class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-gray-400 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m5 0a5 5 0 015 5v3a5 5 0 01-5 5H6a5 5 0 01-5-5v-3a5 5 0 015-5h.28" />
                    </svg>
                    <p class="text-sm text-gray-500">Drop file here or browse</p>
                    <input id="file_revisi" type="file" name="file_revisi" class="hidden" accept="image/*" required onchange="tampilkanNamaFile(this)">
                </label>
                <p id="namaFile" class="text-sm text-green-600 mt-2 hidden"></p>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('popupForm').style.display='none';"
                        class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
function tampilkanNamaFile(input) {
    const namaFile = input.files[0]?.name;
    const tampil = document.getElementById("namaFile");
    
    if (namaFile) {
        tampil.textContent = "✔ Gambar telah dipilih: " + namaFile;
        tampil.classList.remove("hidden");
    } else {
        tampil.textContent = "";
        tampil.classList.add("hidden");
    }
}

function logout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = '../logout.php';
    }
}

// Auto hide alerts after 4 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 4000);
</script>
</body>
</html>