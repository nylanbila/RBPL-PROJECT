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

$id_admin = $_SESSION['id_user'] ?? null;
$nama = "";
$gambar_profile = ""; // default jika tidak ada gambar

if ($id_admin) {
    $query = "SELECT username, gambar_profile FROM admin WHERE id_admin = $id_admin";
    $result = $mysqli->query($query);
    if ($result && $data = $result->fetch_assoc()) {
        $nama = $data['username'];
       $gambar_profile = $data['gambar_profile'];
    }
}

$query = "SELECT * FROM data_bahan
          ORDER BY tanggal_diinputkan DESC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Bahan</title>
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
        <div class="px-10 py-6">
            <h2 class="text-4xl font-medium text-zinc-800 mb-1">Data Bahan</h2>
            <p class="text-neutral-500 text-xl">Berikut Data Bahan yang Tersedia di Gudang</p>
        </div>


    <style>  
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #e5e5e5;
    }
    .table-container {
      background-color: white;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .btn {
      padding: 6px 12px;
      border-radius: 5px;
      font-size: 14px;
      color: white;
      cursor: pointer;
    }
    .btn-green {
      background-color: #28a745;
    }
    .btn-blue {
      background-color: #3b82f6;
    }
    .btn-red {
      background-color: #ef4444;
    }
    .badge {
      background-color: #22c55e;
      color: white;
      padding: 4px 8px;
      border-radius: 5px;
      font-size: 13px;
    }
  </style>
</head>
<body>

<div class="ml-[20px] mt-100 px-10 pb-10">
  <div class="table-container">

    <!-- Top Control -->
    <div class="flex justify-between items-center mb-4">
      <div class="flex items-center gap-2">
        <label class="text-sm">Show</label>
        <select class="border rounded px-2 py-1 text-sm">
          <option>5</option>
          <option>10</option>
          <option>25</option>
        </select>
        <label class="text-sm">entries</label>
      </div>

      <div class="flex items-center gap-2">
        <input type="text" placeholder="Search..." class="border rounded px-2 py-1 text-sm"/>
        <button class="btn btn-green" onclick="window.location.href='tambahbahan.php'">Tambah Data</button> 
      </div>
    </div>

    <!-- Table -->
    <table class="w-full text-sm border border-gray-300">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-4 py-2 text-left">ID Bahan</th>
          <th class="border px-4 py-2 text-left">Nama Bahan</th>
          <th class="border px-4 py-2 text-left">Jenis Bahan</th>
          <th class="border px-4 py-2 text-left">Harga</th>
          <th class="border px-4 py-2 text-left">Satuan</th>
          <th class="border px-4 py-2 text-left">Stok</th>
          <th class="border px-4 py-2 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody>
<?php
   if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['id_bahan']) . "</td>";
            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['nama_bahan']) . "</td>";
            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['jenis_bahan']) . "</td>";
            echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['harga']) . "</td>";
             echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['satuan']) . "</td>";
              echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['stok']) . "</td>";
            echo "<td class='border px-4 py-2'>
                    <a href='edit_bahan.php?id_bahan=" . $row['id_bahan'] . "' class='btn btn-blue'>Edit</a>
                    <a href='hapus_bahan.php?id_bahan=" . $row['id_bahan']. "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data bahan ini?');\" class='btn btn-red'>Hapus</a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-center text-gray-500 py-4'>Tidak ada data proyek</td></tr>";
    }
?>
      </tbody>
    </table>
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

// Navigasi kartu
    document.querySelectorAll('.card-link').forEach(card => {
        card.addEventListener('click', function () {
            const target = this.getAttribute('data-target');
            if (target) window.location.href = target;
        });
    });
</script>
</body>
</html>