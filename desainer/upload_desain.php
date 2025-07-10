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

// Inisialisasi variabel
$sukses = "";
$error = "";

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

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'gagal') {
        echo "<script>alert('Data Gagal dimasukkan')</script>";
    } elseif ($_GET['pesan'] == 'berhasil') {
        echo "<script>
            alert('Data berhasil dimasukkan !.');
            window.location.href = 'upload_desain.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Desain - Dashboard Desainer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .font-poppins { font-family: 'Poppins', sans-serif; }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-input,
        .form-input:focus,
        select,
        textarea {
            padding: 12px 16px;
            border: 1px solid #999;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.2s ease;
        }
        .form-input:focus {
            border-color: #3b82f6;
        }
        .form-button {
            padding: 12px 24px;
            background-color: #22c55e;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .form-button:hover {
            background-color: #16a34a;
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
                <div class="flex items-center space-x-3 py-2 pl-2 rounded cursor-pointer bg-white/20">
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

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-10">
            <!-- Page Title -->
            <div class="mb-6">
                <h2 class="text-3xl font-semibold text-zinc-800 mb-2">Upload Desain</h2>
                <p class="text-gray-500 text-lg">Silahkan mengisi seluruh data dibawah ini</p>
            </div>

            <!-- Form Container -->
            <div class="max-w-2xl mx-auto">
                <div class="form-container">
                    <form action="upload_proses.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-zinc-800 mb-6 text-center">Data Desain</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Nama Desain</label>
                                    <input type="text" name="nama_desain" class="form-input" placeholder="Masukkan Nama Desain" required>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Unggah</label>
                                    <input type="date" name="tanggal_dibuat" class="form-input" required>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Ukuran</label>
                                    <input type="text" name="ukuran" class="form-input" placeholder="Masukkan ukuran" required>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Material</label>
                                    <input type="text" name="material" class="form-input" placeholder="Masukkan material" required>
                                </div>
                            </div>

                            <div class="space-y-2 mt-6">
                                <label class="block text-sm font-medium text-gray-700">Status Review</label>
                                <select name="status_desain" class="form-input" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Revisi">Revisi</option>
                                    <option value="Disetujui">Disetujui</option>
                                </select>
                            </div>

                            <div class="space-y-2 mt-6">
                                <label class="block text-sm font-medium text-gray-700">Deskripsi Desain</label>
                                <textarea name="deskripsi_desain" class="form-input" rows="4" placeholder="Masukkan deskripsi desain" required></textarea>
                            </div>

                            <div class="space-y-2 mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Desain</label>
                                <label for="gambar_desain" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-400 rounded-lg cursor-pointer hover:border-gray-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m5 0a5 5 0 015 5v3a5 5 0 01-5 5H6a5 5 0 01-5-5v-3a5 5 0 015-5h.28" />
                                    </svg>
                                    <p class="text-sm text-gray-600">Drop file here</p>
                                    <p class="text-sm text-indigo-600 underline">or browse</p>
                                    <input id="gambar_desain" type="file" name="gambar_desain" class="hidden" accept="image/*" required onchange="tampilkanNamaFile(this)">
                                </label>
                                <p id="namaFile" class="text-sm text-green-600 mt-2 hidden"></p>
                            </div>

                            <div class="flex justify-end mt-8">
                                <button type="submit" name="simpan" class="form-button">Tambah</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div id="successModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.5); z-index:9999; justify-content: center; align-items: center;">
    <div style="background: white; padding: 40px; border-radius: 10px; text-align: center; width: 400px; position: relative;">
        <span onclick="closeModal()" style="position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 20px;">&times;</span>
        <p style="font-size: 20px; font-weight: 600;">Data projek berhasil ditambahkan !</p>
        <div style="font-size: 48px; color: green; margin: 20px auto;">✔</div>
        <a href="tambahsukses.php" style="color: #3b82f6; text-decoration: underline;">Lihat hasil</a>
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

function handleSubmit() {
    const form = document.querySelector("form");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Simulasi submit PHP
    document.getElementById('successModal').style.display = 'flex';
    setTimeout(() => {
        window.location.href = 'tambahsuksesdesain.php';
    }, 2000);
}

function closeModal() {
    document.getElementById('successModal').style.display = 'none';
}
</script>
</body>
</html>