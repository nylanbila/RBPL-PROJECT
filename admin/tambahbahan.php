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

$query_user = "SELECT username, gambar_profile FROM admin WHERE id_admin = $id_desainer";
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
            window.location.href = 'tambahbahan.php';
        </script>";
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
            background-color: #22c55e;
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
            background-color: #16a34a;
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
            <form action="tambah_proses.php" method="POST">
                <div>
                    <h2 class="form-title">Data Bahan</h2>
                    <div class="form-group">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" name="nama_bahan" class="form-input" placeholder="Masukkan nama bahan" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Bahan</label>
                        <input type="text" name="jenis_bahan" class="form-input" placeholder="Masukkan jenis bahan" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-input" placeholder="Masukkan harga" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-input" placeholder="Masukkan satuan" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-input" placeholder="Masukkan stok" required>
                    </div>
                    

                <div class="form-button-container">
                    <button type="submit" name="simpan" class="form-button">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Sukses -->
    <div id="successModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.5); z-index:9999; justify-content: center; align-items: center;">
        <div style="background: white; padding: 40px; border-radius: 10px; text-align: center; width: 400px; position: relative;">
            <span onclick="closeModal()" style="position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 20px;">&times;</span>
            <p style="font-size: 20px; font-weight: 600;">Data bahan berhasil ditambahkan !</p>
            <div style="font-size: 48px; color: green; margin: 20px auto;">✔</div>
            <a href="tambahsukses.php" style="color: #3b82f6; text-decoration: underline;">Lihat hasil</a>
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
    
        function logout() {
            if (confirm('Apakah Anda yakin ingin logout?')) {
                window.location.href = 'login.php';
            }
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