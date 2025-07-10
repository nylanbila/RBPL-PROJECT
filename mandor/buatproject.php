<?php
session_start();
// Cek apakah sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}
// Koneksi Database
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

// Inisialisasi variabel
$sukses = "";
$error = "";

$desainList = [];
$query_desain = "SELECT id_desain, nama_desain FROM data_desain WHERE status_desain = 'Disetujui'";
$result_desain = mysqli_query($koneksi, $query_desain);
while ($row = mysqli_fetch_assoc($result_desain)) {
    $desainList[] = $row;
}

// Proses form submission
if (isset($_POST['simpan'])) {
    $nama_proyek = mysqli_real_escape_string($koneksi, $_POST['nama_proyek']);
    $id_desain = mysqli_real_escape_string($koneksi, $_POST['id_desain']);
    $tanggal_mulai = mysqli_real_escape_string($koneksi, $_POST['tanggal_mulai']);
    $tanggal_selesai = mysqli_real_escape_string($koneksi, $_POST['tanggal_selesai']);
    $nama_mandor = mysqli_real_escape_string($koneksi, $_POST['nama_mandor']);
    $nama_klien = mysqli_real_escape_string($koneksi, $_POST['nama_klien']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // Validasi input
    if ($id_desain && $nama_proyek && $tanggal_mulai && $tanggal_selesai && $nama_mandor && $nama_klien && $status) {
        // Insert data ke database
        $sql = "INSERT INTO data_proyek (id_desain, nama_proyek, tanggal_mulai, tanggal_selesai, nama_mandor, nama_klien, status, deskripsi) 
                VALUES ('$id_desain', '$nama_proyek', '$tanggal_mulai', '$tanggal_selesai', '$nama_mandor', '$nama_klien', '$status', '$deskripsi')";
        
        $result = mysqli_query($koneksi, $sql);
        
        if ($result) {
            $sukses = "Data proyek berhasil ditambahkan!";
            // Reset form setelah berhasil
            $_POST = array();
        } else {
            $error = "Gagal menambahkan data: " . mysqli_error($koneksi);
        }
    } else {
        $error = "Silahkan lengkapi semua field yang wajib diisi";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Project - Dashboard Mandor</title>
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
        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
                        <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer bg-zinc-700" onclick="window.location.href='buatproject.php'">Buat Projek</div>
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

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto">
            <!-- Page Title -->
            <div class="px-10 pt-8 mb-6">
                <h2 class="text-4xl font-medium text-zinc-800 mb-2">Buat Project</h2>
                <p class="text-neutral-500 text-xl">Silahkan mengisi seluruh data dibawah ini</p>
            </div>

            <!-- Form Container -->
            <div class="flex justify-center px-10 pb-10">
                <div class="w-full max-w-2xl">
                    <!-- Success/Error Messages -->
                    <?php if ($sukses): ?>
                        <div class="success-message">
                            <?php echo $sukses; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="error-message">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h3 class="text-2xl font-semibold text-center mb-6 text-gray-800">Data Projek</h3>
                        
                        <form method="POST" id="projekForm" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama Proyek -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Proyek</label>
                                    <input type="text" name="nama_proyek" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           placeholder="Masukkan nama proyek" 
                                           value="<?php echo isset($_POST['nama_proyek']) && !$sukses ? htmlspecialchars($_POST['nama_proyek']) : ''; ?>" 
                                           required>
                                </div>

                                <!-- Pilih Desain -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Desain</label>
                                    <select name="id_desain" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                            required>
                                        <option value="">-- Pilih Desain --</option>
                                        <?php foreach ($desainList as $desain): ?>
                                            <option value="<?= $desain['id_desain'] ?>"
                                                <?= (isset($_POST['id_desain']) && $_POST['id_desain'] == $desain['id_desain']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($desain['nama_desain']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Tanggal Mulai -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           value="<?php echo isset($_POST['tanggal_mulai']) && !$sukses ? $_POST['tanggal_mulai'] : ''; ?>" 
                                           required>
                                </div>

                                <!-- Tanggal Selesai -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           value="<?php echo isset($_POST['tanggal_selesai']) && !$sukses ? $_POST['tanggal_selesai'] : ''; ?>" 
                                           required>
                                </div>

                                <!-- Nama Mandor -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Mandor</label>
                                    <input type="text" name="nama_mandor" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           placeholder="Masukkan nama mandor" 
                                           value="<?php echo isset($_POST['nama_mandor']) && !$sukses ? htmlspecialchars($_POST['nama_mandor']) : ''; ?>" 
                                           required>
                                </div>

                                <!-- Nama Client -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Client</label>
                                    <input type="text" name="nama_klien" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                           placeholder="Masukkan nama client" 
                                           value="<?php echo isset($_POST['nama_klien']) && !$sukses ? htmlspecialchars($_POST['nama_klien']) : ''; ?>" 
                                           required>
                                </div>

                                <!-- Status -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                            required>
                                        <option value="">Pilih Status</option>
                                        <option value="Belum dimulai" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Belum dimulai' && !$sukses) ? 'selected' : ''; ?>>Belum dimulai</option>
                                        <option value="Dalam Proses" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Dalam Proses' && !$sukses) ? 'selected' : ''; ?>>Dalam Proses</option>
                                        <option value="Selesai" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Selesai' && !$sukses) ? 'selected' : ''; ?>>Selesai</option>
                                        <option value="Tunda" <?php echo (isset($_POST['status']) && $_POST['status'] == 'Tunda' && !$sukses) ? 'selected' : ''; ?>>Tunda</option>
                                    </select>
                                </div>

                                <!-- Deskripsi -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                    <textarea name="deskripsi" rows="4" 
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical" 
                                              placeholder="Masukkan deskripsi proyek"><?php echo isset($_POST['deskripsi']) && !$sukses ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end pt-6">
                                <button type="submit" name="simpan" 
                                        class="px-8 py-3 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                    Tambah Project
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<?php if ($sukses): ?>
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-mx-4 text-center relative">
        <span onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 cursor-pointer text-xl">&times;</span>
        <div class="text-green-500 text-6xl mb-4">✓</div>
        <h3 class="text-xl font-semibold mb-4">Berhasil!</h3>
        <p class="text-gray-600 mb-6">Data proyek berhasil ditambahkan!</p>
        <a href="dataproject.php" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
            Lihat Data Project
        </a>
    </div>
</div>
<?php endif; ?>

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

    function closeModal() {
        document.getElementById('successModal').style.display = 'none';
    }

    // Auto close modal after 3 seconds
    <?php if ($sukses): ?>
    setTimeout(() => {
        window.location.href = 'dataproject.php';
    }, 3000);
    <?php endif; ?>

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