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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat RAB</title>
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

                <!-- RAB -->
                <div class="flex items-center space-x-3 cursor-pointer" onclick="toggleDropdown('rab-dropdown')">
                    <img src="admin/assets/Frame(1).png" class="w-6 h-7 ml-2" alt="RAB">
                    <span class="text-2xl font-medium">RAB</span>
                    <img src="admin/assets/Frame5.png" class="w-5 h-5 ml-auto" alt="Toggle">
                </div>
                <div id="rab-dropdown" class="dropdown-content pl-12 space-y-1">
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='data-rab.php'">Data RAB</div>
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='buat-rab.php'">Buat RAB</div>
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
            <h2 class="text-4xl font-medium text-zinc-800 mb-1 text-left">Form Data RAB</h2>
            <p class="text-neutral-500 text-xl">Silakan lengkapi seluruh data dibawah ini </p>
        </div>

    

<!DOCTYPE html>
<html>
<head>
  <title>Buat RAB</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #eaeaea;
      margin: 0;
      padding: 0;
    }

    .container {
  max-width: 600px;
  background-color: white;
  margin: 100px 0 0 600px; /* lebih ke atas dan ke kiri */
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
}


    h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .form-step {
      display: none;
    }

    .form-step.active {
      display: block;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      font-weight: bold;
    }

    input[type="text"],
    input[type="date"],
    input[type="number"],
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    textarea {
      height: 80px;
    }

    .button-group {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .button-group button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn-next {
      background-color: #007bff;
      color: white;
    }

    .btn-prev {
      background-color: #6c757d;
      color: white;
    }

    .btn-submit {
      background-color: #28a745;
      color: white;
      width: 100%;
    }

    .tenaga-item,
    .bahan-item,
    .transportasi-item {
      margin-bottom: 15px;
      padding: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #f9f9f9;
    }

    button[onclick^="tambah"] {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
    }

    .form-group input,
    .form-group textarea {
      margin-bottom: 10px;
    }
    .btn-small {
  width: auto;
  padding: 10px 20px;
  white-space: nowrap;
}

  </style>
</head>
<body>

<div class="container">
  <form id="rabForm" action="simpan-rab.php" method="POST">
    <!-- STEP 1: Data Dasar -->
    <div class="form-step active">
      <h2>Data Dasar</h2>
      <div class="form-group">
        <label>No. RAB</label>
        <input type="text" name="no_rab" required>
      </div>
      <div class="form-group">
        <label>Tanggal Pembuatan</label>
        <input type="date" name="tgl_pembuatan" required>
      </div>
      <div class="form-group">
        <label>Tanggal Kesepakatan Harga</label>
        <input type="date" name="tgl_kesepakatan" required>
      </div>
      <div class="form-group">
        <label>Nama Proyek</label>
        <input type="text" name="nama_proyek" required>
      </div>
      <div class="form-group">
        <label>Nama Client</label>
        <input type="text" name="nama_client" required>
      </div>
      <div class="button-group">
        <button type="button" class="btn-next">Next</button>
      </div>
    </div>

    <!-- STEP 2: Biaya Tenaga Kerja -->
    <div class="form-step">
      <h2>Biaya Tenaga Kerja</h2>
      <div id="tenaga-container">
        <div class="tenaga-item form-group">
          <label>Detail pekerjaan</label>
          <input type="text" name="detail_pekerjaan[]" placeholder="Masukkan Detail Pekerjaan" required>
          <label>Biaya</label>
          <input type="number" name="tenaga_biaya[]" placeholder="Rp" required>
        </div>
      </div>
      <button type="button" onclick="tambahTenaga()" style="margin-top: 10px; background-color: #22c55e; color: white; padding: 5px 12px; border-radius: 10px; font-size: 14px;">
        ➕ Tambah
      </button>
      <div class="button-group">
        <button type="button" class="btn-prev">Previous</button>
        <button type="button" class="btn-next">Next</button>
      </div>
    </div>

    <!-- STEP 3: Bahan -->
    <div class="form-step">
      <h2>Biaya Bahan</h2>
      <div id="bahan-container">
        <div class="bahan-item">
          <div class="form-group">
            <label>Nama Bahan</label>
            <input type="text" name="bahan_nama[]" required>
          </div>
          <div class="form-group">
            <label>Satuan</label>
            <input type="text" name="satuan[]" required>
          </div>
          <div class="form-group">
            <label>Volume</label>
            <input type="text" name="volume[]" required>
          </div>
          <div class="form-group">
            <label>Harga Satuan</label>
            <input type="text" name="bahan_harga[]" required>
          </div>
        </div>
      </div>
      <button type="button" onclick="tambahBahan()" style="margin-top: 10px; background-color: #22c55e; color: white; padding: 5px 12px; border-radius: 10px; font-size: 14px;">
        ➕ Tambah
      </button>
      <div class="button-group">
        <button type="button" class="btn-prev">Previous</button>
        <button type="button" class="btn-next">Next</button>
      </div>
    </div>

    <!-- STEP 4: Transportasi -->
    <div class="form-step">
      <h2>Biaya Transportasi</h2>
      <div id="transportasi-container">
        <div class="transportasi-item">
          <div class="form-group">
            <label>Detail</label>
            <input type="text" name="detail_transportasi[]" required>
          </div>
          <div class="form-group">
            <label>Biaya</label>
            <input type="text" name="biaya_transportasi[]" required>
          </div>
        </div>
      </div>
      <button type="button" onclick="tambahTransportasi()" style="margin-top: 10px; background-color: #22c55e; color: white; padding: 5px 12px; border-radius: 10px; font-size: 14px;">
        ➕ Tambah
      </button>
      <div class="button-group">
      <button type="button" class="btn-prev">Previous</button>
      <button type="submit" class="btn-submit btn-small">Simpan RAB</button>
      </div>
    </div>
  </form>
</div>

<!-- Script Navigasi dan Tambah -->
<script>
  const steps = document.querySelectorAll(".form-step");
  const btnNext = document.querySelectorAll(".btn-next");
  const btnPrev = document.querySelectorAll(".btn-prev");

  let currentStep = 0;

  btnNext.forEach(btn => {
    btn.addEventListener("click", () => {
      steps[currentStep].classList.remove("active");
      currentStep++;
      steps[currentStep].classList.add("active");
    });
  });

  btnPrev.forEach(btn => {
    btn.addEventListener("click", () => {
      steps[currentStep].classList.remove("active");
      currentStep--;
      steps[currentStep].classList.add("active");
    });
  });

  function tambahTenaga() {
    alert('Data Tenaga Kerja telah disimpan!');
    const inputs = document.querySelectorAll('#tenaga-container input');
    inputs.forEach(input => input.value = '');
  }

  function tambahBahan() {
    alert('Data Bahan telah disimpan!');
    const inputs = document.querySelectorAll('#bahan-container input');
    inputs.forEach(input => input.value = '');
  }

  function tambahTransportasi() {
    alert('Data Transportasi telah disimpan!');
    const inputs = document.querySelectorAll('#transportasi-container input');
    inputs.forEach(input => input.value = '');
  }
</script>

</body>
</html>


<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('active');
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
