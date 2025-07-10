<?php
session_start();

// Cek apakah sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

$koneksi = mysqli_connect("localhost", "root", "", "inerior");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$id_admin = $_SESSION['id_user'];
$nama = "";
$gambar_profile = "";

// Ambil data profil admin
$query = "SELECT username, gambar_profile FROM admin WHERE id_admin = $id_admin";
$result = mysqli_query($koneksi, $query);
if ($result && $data = mysqli_fetch_assoc($result)) {
    $nama = $data['username'];
    $gambar_profile = $data['gambar_profile'];
}

// Ambil ID bahan dari GET
$id_bahan = isset($_GET['id_bahan']) ? intval($_GET['id_bahan']) : 0;

// Ambil data bahan
$query_bahan = "SELECT * FROM data_bahan WHERE id_bahan = $id_bahan";
$result_bahan = mysqli_query($koneksi, $query_bahan);

if (!$result_bahan || mysqli_num_rows($result_bahan) == 0) {
    header("Location: data-bahan.php");
    exit();
}
$bahan = mysqli_fetch_assoc($result_bahan);

// Update data bahan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_bahan'])) {
    $id_bahan = intval($_POST['id_bahan']);
    $nama_bahan = mysqli_real_escape_string($koneksi, $_POST['nama_bahan']);
    $jenis_bahan = mysqli_real_escape_string($koneksi, $_POST['jenis_bahan']);
    $harga = floatval($_POST['harga']);
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $stok = intval($_POST['stok']);

    $query = "UPDATE data_bahan SET 
               nama_bahan = '$nama_bahan',
               jenis_bahan = '$jenis_bahan',
               harga = '$harga',
               satuan = '$satuan',
               stok = '$stok'
              WHERE id_bahan = $id_bahan";

   if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data Bahan berhasil diperbarui!'); window.location.href = 'data-bahan.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data proyek.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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
                <div class="relative bg-neutral-300/70 rounded py-2 pl-2 flex items-center space-x-3 cursor-pointer hover:bg-white/10" onclick="window.location.href='admin_dashboard.php'">
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
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white shadow px-10 h-24">
            <img src="admin/assets/Vector.png" class="w-10 h-8" alt="Menu">
            <div class="flex items-center space-x-4">
                <img src="https://placehold.co/59x60" class="w-14 h-14 object-cover" alt="Profile" style="border-radius: 0;">
                <span class="text-2xl font-medium text-black">Jakson</span>
            </div>
        </div>

        <!-- Dashboard Title -->
        <div class="pl-10 pt-4">
            <h2 class="text-4xl font-medium text-zinc-800 mb-1 text-left">Form Data Bahan</h2>
            <p class="text-neutral-500 text-xl">Silahkan mengisi seluruh data dibawah ini</p>
        </div>

        <style>
        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 150px auto;
            background-color: white;
            padding: 30px 25px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-title {
            font-size: 22px;
            font-weight: 600;
            color: #27272a;
            text-align: center;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-label {
            font-size: 16px;
            font-weight: 500;
            color: #000;
        }

        .form-input,
        .form-input:focus,
        select,
        textarea {
            padding: 10px 14px;
            border: 1px solid #999;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            box-sizing: border-box;
        }

        .form-textarea {
            height: 60px;
            resize: vertical;
        }

        .form-button-container {
            display: flex;
            justify-content: flex-end;
        }

        .form-button {
            padding: 10px 20px;
            background-color:rgb(58, 93, 207);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }

        .form-button:hover {
            background-color: rgb(58, 93, 207);
        }

        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

        <div class="form-container">
            <form action="" method="POST" id="projekForm">
                <div>
                    <h2 class="form-title">Data Bahan</h2>
                        <div class="form-group">
                        <input type="hidden" name="id_bahan" value="<?= $id_bahan ?>">
                        
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" name="nama_bahan" class="form-input"
                            value="<?= htmlspecialchars($bahan['nama_bahan']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Bahan</label>
                        <input type="text" name="jenis_bahan" class="form-input"
                            value="<?= htmlspecialchars($bahan['jenis_bahan']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-input"
                            value="<?= htmlspecialchars($bahan['harga']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-input"
                            value="<?= htmlspecialchars($bahan['satuan']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-input"
                            value="<?= htmlspecialchars($bahan['stok']) ?>" required>
                    </div>
                <div class="form-button-container">
                    <button type="submit" class="form-button" onclick="handleSubmit()">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    
    function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

        function handleSubmit() {
            const form = document.getElementById("projekForm");
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Simulasi submit PHP
            document.getElementById('successModal').style.display = 'flex';
            setTimeout(() => {
                window.location.href = 'tambahsukses.php';
            }, 2000);
        }

        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }
    </script>
     <script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        dropdown.classList.toggle('active');
    }
</script>
</body>
</html>