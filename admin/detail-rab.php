<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data RAB</title>
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
                <img src="https://placehold.co/59x60" class="w-14 h-14 object-cover" alt="Profile" style="border-radius: 0;">
                <span class="text-2xl font-medium text-black">Jakson</span>
            </div>
        </div>
<?php
// Ambil parameter no_rab dari URL
$no_rab = $_GET['no_rab'] ?? '';

// Data statis RAB
$data_rab = [
  'RAB-001' => [
    'proyek' => 'Interior Rumah',
    'klien' => 'Budi Santoso',
    'no_rab' => 'RAB-001',
    'tgl_buat' => '01 Maret 2025',
    'tgl_kesepakatan' => '02 Maret 2025',
    'total' => 'Rp150.000.000'
  ],
  'RAB-002' => [
    'proyek' => 'Desain Café Cozy',
    'klien' => 'Siti Rahma',
    'no_rab' => 'RAB-002',
    'tgl_buat' => '10 Maret 2025',
    'tgl_kesepakatan' => '11 Maret 2025',
    'total' => 'Rp180.000.000'
  ],
  'RAB-003' => [
    'proyek' => 'Konstruksi Ruang Meeting',
    'klien' => 'CV. Sukses',
    'no_rab' => 'RAB-003',
    'tgl_buat' => '20 Februari 2025',
    'tgl_kesepakatan' => '22 Februari 2025',
    'total' => 'Rp210.000.000'
  ],
  'RAB-004' => [
    'proyek' => 'Renovasi Kantor ABC',
    'klien' => 'PT. ABC',
    'no_rab' => 'RAB-004',
    'tgl_buat' => '15 Februari 2025',
    'tgl_kesepakatan' => '16 Februari 2025',
    'total' => 'Rp275.000.000'
  ],
  'RAB-005' => [
    'proyek' => 'Pembangunan Showroom',
    'klien' => 'PT. Maju Jaya',
    'no_rab' => 'RAB-005',
    'tgl_buat' => '05 Maret 2025',
    'tgl_kesepakatan' => '06 Maret 2025',
    'total' => 'Rp320.000.000'
  ]
];

// Validasi data
if (!isset($data_rab[$no_rab])) {
  echo "<h2 class='text-center text-red-600 font-bold mt-10'>Data RAB tidak ditemukan.</h2>";
  exit;
}

$rab = $data_rab[$no_rab];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>RENCANA ANGGARAN BIAYA</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex items-start justify-start pt-[200px] pl-[250px]"></body>
  <div class="bg-white p-10 shadow-md text-sm" style="margin-left: 600px; margin-top: 200px; width: 1100px;">
  


    <h1 class="text-xl font-bold text-center mb-6">RENCANA ANGGARAN BIAYA</h1>

    <!-- Keterangan Proyek Dinamis -->
    <div class="mb-6 space-y-1 text-sm">
      <p>Nama Proyek: <?= $rab['proyek']; ?></p>
      <p>Nama Klien: <?= $rab['klien']; ?></p>
      <p>No. RAB: <strong><?= $rab['no_rab']; ?></strong></p>
      <p>Tanggal Pembuatan RAB: <?= $rab['tgl_buat']; ?></p>
      <p>Tanggal Kesepakatan Harga: <?= $rab['tgl_kesepakatan']; ?></p>
    </div>

       <!-- Tabel RAB -->
    <table class="w-full border border-gray-300 text-xs">
      <thead class="bg-gray-700 text-white">
        <tr>
          <th class="border px-2 py-2 text-center w-8">No.</th>
          <th class="border px-2 py-2 text-left">Uraian Pekerjaan/Bahan</th>
          <th class="border px-2 py-2 text-center w-16">Volume</th>
          <th class="border px-2 py-2 text-center w-16">Satuan</th>
          <th class="border px-2 py-2 text-right w-32">Harga Satuan</th>
          <th class="border px-2 py-2 text-right w-40">Jumlah Harga Satuan</th>
        </tr>
      </thead>
      <tbody class="bg-white text-gray-800">
        <!-- Tenaga Kerja -->
        <tr class="bg-gray-200 font-bold">
          <td colspan="6" class="border px-2 py-1">1&nbsp;&nbsp;TENAGA KERJA</td>
        </tr>
        <tr>
          <td class="border px-2 py-1 text-center">1</td>
          <td class="border px-2 py-1">Desain Interior</td>
          <td class="border px-2 py-1 text-center">1</td>
          <td class="border px-2 py-1 text-center">Paket</td>
          <td class="border px-2 py-1 text-right">Rp 5.000.000</td>
          <td class="border px-2 py-1 text-right">Rp 5.000.000</td>
        </tr>
        <tr>
          <td class="border px-2 py-1 text-center">2</td>
          <td class="border px-2 py-1">Pengerjaan Instalasi Furniture</td>
          <td class="border px-2 py-1 text-center">20</td>
          <td class="border px-2 py-1 text-center">m²</td>
          <td class="border px-2 py-1 text-right">Rp 250.000</td>
          <td class="border px-2 py-1 text-right">Rp 5.000.000</td>
        </tr>
        <tr>
          <td class="border px-2 py-1 text-center">3</td>
          <td class="border px-2 py-1">Instalasi Penerangan (LED & Lampu Gantung)</td>
          <td class="border px-2 py-1 text-center">8</td>
          <td class="border px-2 py-1 text-center">Titik</td>
          <td class="border px-2 py-1 text-right">Rp 200.000</td>
          <td class="border px-2 py-1 text-right">Rp 1.600.000</td>
        </tr>
        <tr class="bg-gray-100 font-semibold">
          <td colspan="5" class="border px-2 py-1 text-right">Subtotal Tenaga Kerja</td>
          <td class="border px-2 py-1 text-right">Rp 11.600.000</td>
        </tr>

        <!-- Bahan -->
        <tr class="bg-gray-200 font-bold">
          <td colspan="6" class="border px-2 py-1">2&nbsp;&nbsp;BAHAN</td>
        </tr>
        <tr>
          <td class="border px-2 py-1 text-center">1</td>
          <td class="border px-2 py-1">Plywood Premium</td>
          <td class="border px-2 py-1 text-center">10</td>
          <td class="border px-2 py-1 text-center">m²</td>
          <td class="border px-2 py-1 text-right">Rp 450.000</td>
          <td class="border px-2 py-1 text-right">Rp 4.500.000</td>
        </tr>
        <tr>
          <td class="border px-2 py-1 text-center">2</td>
          <td class="border px-2 py-1">HPL (High-Pressure Laminate)</td>
          <td class="border px-2 py-1 text-center">8</td>
          <td class="border px-2 py-1 text-center">m²</td>
          <td class="border px-2 py-1 text-right">Rp 200.000</td>
          <td class="border px-2 py-1 text-right">Rp 1.600.000</td>
        </tr>
        <tr>
          <td class="border px-2 py-1 text-center">3</td>
          <td class="border px-2 py-1">Jendela Aluminium</td>
          <td class="border px-2 py-1 text-center">4</td>
          <td class="border px-2 py-1 text-center">Unit</td>
          <td class="border px-2 py-1 text-right">Rp 650.000</td>
          <td class="border px-2 py-1 text-right">Rp 2.600.000</td>
        </tr>
        <tr class="bg-gray-100 font-semibold">
          <td colspan="5" class="border px-2 py-1 text-right">Subtotal Bahan</td>
          <td class="border px-2 py-1 text-right">Rp 8.700.000</td>
        </tr>

        <!-- Transportasi -->
        <tr class="bg-gray-200 font-bold">
          <td colspan="6" class="border px-2 py-1">3&nbsp;&nbsp;TRANSPORTASI</td>
        </tr>
        <tr>
          <td class="border px-2 py-1 text-center">1</td>
          <td class="border px-2 py-1">Pengiriman Bahan</td>
          <td class="border px-2 py-1 text-center">3</td>
          <td class="border px-2 py-1 text-center">Trip</td>
          <td class="border px-2 py-1 text-right">Rp 500.000</td>
          <td class="border px-2 py-1 text-right">Rp 1.500.000</td>
        </tr>
        <tr>
          <td class="border px-2 py-1 text-center">2</td>
          <td class="border px-2 py-1">Transportasi Pekerja</td>
          <td class="border px-2 py-1 text-center">10</td>
          <td class="border px-2 py-1 text-center">Hari</td>
          <td class="border px-2 py-1 text-right">Rp 80.000</td>
          <td class="border px-2 py-1 text-right">Rp 800.000</td>
        </tr>
        <tr class="bg-gray-100 font-semibold">
          <td colspan="5" class="border px-2 py-1 text-right">Subtotal Transportasi</td>
          <td class="border px-2 py-1 text-right">Rp 2.300.000</td>
        </tr>

        <!-- Total -->
        <tr class="bg-gray-800 text-white font-bold">
          <td colspan="5" class="border px-2 py-2 text-right">GRAND TOTAL</td>
          <td class="border px-2 py-2 text-right">Rp 23.600.000</td>
        </tr>
      </tbody>
    </table>
  </div>
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