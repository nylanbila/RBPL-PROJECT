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
    $query = "SELECT username, gambar_profile FROM admin WHERE id_admin = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_admin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $data = $result->fetch_assoc()) {
        $nama = $data['username'];
        $gambar_profile = $data['gambar_profile'];
    }
}

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mulai transaksi
    $mysqli->begin_transaction();
    
    try {
        $nama_admin = $_POST['nama_admin'];
        $alamat = $_POST['alamat_pengiriman'];
        $kontak = $_POST['kontak'];
        $batas_pengiriman = $_POST['batas_pengiriman'];
        
        // Insert ke tabel pengajuan_bahan
        $query_pengajuan = "INSERT INTO pengajuan_bahan (nama_admin, alamat, kontak, batas_pengiriman) 
                            VALUES (?, ?, ?, ?)";
        
        $stmt = $mysqli->prepare($query_pengajuan);
        $stmt->bind_param("ssss", $nama_admin, $alamat, $kontak, $batas_pengiriman);
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting pengajuan: " . $stmt->error);
        }
        
        $id_pengajuan = $mysqli->insert_id;
        
        // Insert ke tabel bahan_diajukan
        $bahan_nama = $_POST['bahan_nama'];
        $ukuran = $_POST['ukuran'];
        $jenis = $_POST['jenis'];
        $jumlah = $_POST['jumlah'];
        $satuan = $_POST['satuan'];
        
        $query_bahan = "INSERT INTO bahan_diajukan (id_pengajuan, nama_bahan, ukuran, jenis, jumlah, satuan) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt_bahan = $mysqli->prepare($query_bahan);
        
        for ($i = 0; $i < count($bahan_nama); $i++) {
            $stmt_bahan->bind_param("isssss", $id_pengajuan, $bahan_nama[$i], $ukuran[$i], $jenis[$i], $jumlah[$i], $satuan[$i]);
            
            if (!$stmt_bahan->execute()) {
                throw new Exception("Error inserting bahan: " . $stmt_bahan->error);
            }
        }
        
        // Commit transaksi
        $mysqli->commit();
        
        echo "<script>
                alert('Pengajuan berhasil dikirim!'); 
                window.location.href='pengajuan-bahan.php';
              </script>";
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $mysqli->rollback();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Fetch data pengajuan untuk ditampilkan
$query_display = "SELECT p.*, GROUP_CONCAT(CONCAT(b.nama_bahan, ' (', b.ukuran, ', ', b.jenis, ', ', b.jumlah, ' ', b.satuan, ')') SEPARATOR '<br>') as detail_bahan
                  FROM pengajuan_bahan p 
                  LEFT JOIN bahan_diajukan b ON p.id_pengajuan = b.id_pengajuan 
                  GROUP BY p.id_pengajuan 
                  ORDER BY p.tanggal_pengajuan DESC";
$result_display = $mysqli->query($query_display);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Bahan</title>
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
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        .container {
            max-width: 100%;
        }
        .bahan-item {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
    <div class="flex-1 flex flex-col overflow-y-auto">
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
            <h2 class="text-4xl font-medium text-zinc-800 mb-1 text-left">Form Pengajuan Bahan</h2>
            <p class="text-neutral-500 text-xl">Silakan isi seluruh form pengajuan bahan dibawah ini</p>
        </div>

        <!-- Form dan Data Display -->
        <div class="flex px-10 gap-6 justify-center items-center min-h-screen">
            <!-- Form Container -->
            <div class="w-1/2">
                <div class="container bg-white p-6 rounded-lg shadow-md">
                    <form id="pengajuanForm" method="POST" action="">
                        <!-- STEP 1: Data Pengajuan -->
                        <div class="form-step active">
                            <h2 class="text-2xl font-bold text-center mb-6">Data Pengajuan Bahan</h2>
                            <div class="form-group mb-4">
                                <label class="block font-bold mb-2">Nama Admin</label>
                                <input type="text" name="nama_admin" value="<?= htmlspecialchars($nama); ?>" class="w-full p-3 border border-gray-300 rounded" required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="block font-bold mb-2">Alamat Pengiriman</label>
                                <input type="text" name="alamat_pengiriman" class="w-full p-3 border border-gray-300 rounded" required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="block font-bold mb-2">Kontak</label>
                                <input type="text" name="kontak" class="w-full p-3 border border-gray-300 rounded" required>
                            </div>
                            <div class="form-group mb-4">
                                <label class="block font-bold mb-2">Batas Pengiriman</label>
                                <input type="date" name="batas_pengiriman" class="w-full p-3 border border-gray-300 rounded" required>
                            </div>
                            <div class="button-group">
                                <button type="button" class="btn-next bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Next</button>
                            </div>
                        </div>

                        <!-- STEP 2: Detail Bahan -->
                        <div class="form-step">
                            <h2 class="text-2xl font-bold text-center mb-6">Detail Bahan</h2>
                            <div id="bahan-container">
                                <div class="bahan-item form-group mb-4 p-4 border border-gray-200 rounded">
                                    <div class="flex justify-between items-center mb-2">
                                        <h3 class="text-lg font-semibold">Bahan 1</h3>
                                        <button type="button" onclick="hapusBahan(this)" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Hapus</button>
                                    </div>
                                    <label class="block font-bold mb-2">Nama Bahan</label>
                                    <input type="text" name="bahan_nama[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
                                    <label class="block font-bold mb-2">Ukuran</label>
                                    <input type="text" name="ukuran[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
                                    <label class="block font-bold mb-2">Jenis</label>
                                    <input type="text" name="jenis[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
                                    <label class="block font-bold mb-2">Jumlah</label>
                                    <input type="number" name="jumlah[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
                                    <label class="block font-bold mb-2">Satuan</label>
                                    <input type="text" name="satuan[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
                                </div>
                            </div>
                            <button type="button" onclick="tambahBahan()" class="bg-green-500 text-white px-4 py-2 rounded mb-4 hover:bg-green-600">
                                ➕ Tambah Bahan
                            </button>
                            <div class="button-group flex justify-between">
                                <button type="button" class="btn-prev bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Previous</button>
                                <button type="button" class="btn-next bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Next</button>
                            </div>
                        </div>

                        <!-- STEP 3: Review dan Kirim -->
                        <div class="form-step">
                            <h2 class="text-2xl font-bold text-center mb-6">Review Pengajuan</h2>
                            <div id="review-content" class="mb-6 p-4 bg-gray-50 rounded">
                                <p><strong>Admin:</strong> <span id="r-admin"></span></p>
                                <p><strong>Alamat:</strong> <span id="r-alamat"></span></p>
                                <p><strong>Kontak:</strong> <span id="r-kontak"></span></p>
                                <p><strong>Batas Pengiriman:</strong> <span id="r-batas"></span></p>
                            </div>
                            <div class="overflow-x-auto">
                                <table id="review-bahan" class="w-full border-collapse border border-gray-400">
                                    <thead class="bg-gray-600 text-white">
                                        <tr>
                                            <th class="border border-gray-400 p-2">No</th>
                                            <th class="border border-gray-400 p-2">Nama Bahan</th>
                                            <th class="border border-gray-400 p-2">Ukuran</th>
                                            <th class="border border-gray-400 p-2">Jenis</th>
                                            <th class="border border-gray-400 p-2">Jumlah</th>
                                            <th class="border border-gray-400 p-2">Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="button-group flex justify-between mt-6">
                                <button type="button" class="btn-prev bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Previous</button>
                                <button type="submit" class="btn-submit bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">Kirim Pengajuan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const steps = document.querySelectorAll(".form-step");
    const btnNext = document.querySelectorAll(".btn-next");
    const btnPrev = document.querySelectorAll(".btn-prev");
    
    let currentStep = 0;
    
    btnNext.forEach(btn => {
        btn.addEventListener("click", () => {
            if (currentStep < steps.length - 1) {
                // Validasi sebelum pindah ke step berikutnya
                if (validateCurrentStep()) {
                    // Update review content sebelum ke step terakhir
                    if (currentStep === 1) {
                        updateReview();
                    }
                    steps[currentStep].classList.remove("active");
                    currentStep++;
                    steps[currentStep].classList.add("active");
                }
            }
        });
    });
    
    btnPrev.forEach(btn => {
        btn.addEventListener("click", () => {
            if (currentStep > 0) {
                steps[currentStep].classList.remove("active");
                currentStep--;
                steps[currentStep].classList.add("active");
            }
        });
    });
    
    function validateCurrentStep() {
        const currentStepElement = steps[currentStep];
        const requiredInputs = currentStepElement.querySelectorAll('input[required]');
        let isValid = true;
        
        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = 'red';
                input.focus();
            } else {
                input.style.borderColor = '#d1d5db';
            }
        });
        
        if (!isValid) {
            alert('Mohon lengkapi semua field yang diperlukan!');
        }
        
        return isValid;
    }
    
    function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

    function tambahBahan() {
        const bahanContainer = document.getElementById("bahan-container");
        
        // Validasi form bahan terakhir
        const activeForm = bahanContainer.querySelector('.bahan-item:last-child');
        if (activeForm) {
            const inputs = activeForm.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '#d1d5db';
                }
            });
            
            if (!isValid) {
                alert('Mohon lengkapi data bahan sebelumnya terlebih dahulu!');
                return;
            }
        }
        
        // Buat form bahan baru
        const newBahan = document.createElement("div");
        newBahan.className = "bahan-item form-group mb-4 p-4 border border-gray-200 rounded";
        newBahan.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold">Bahan ${bahanContainer.children.length + 1}</h3>
                <button type="button" onclick="hapusBahan(this)" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">Hapus</button>
            </div>
            <label class="block font-bold mb-2">Nama Bahan</label>
            <input type="text" name="bahan_nama[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
            <label class="block font-bold mb-2">Ukuran</label>
            <input type="text" name="ukuran[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
            <label class="block font-bold mb-2">Jenis</label>
            <input type="text" name="jenis[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
            <label class="block font-bold mb-2">Jumlah</label>
            <input type="number" name="jumlah[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
            <label class="block font-bold mb-2">Satuan</label>
            <input type="text" name="satuan[]" class="w-full p-3 border border-gray-300 rounded mb-2" required>
        `;
        
        bahanContainer.appendChild(newBahan);
        
        // Fokus ke input pertama dari form bahan yang baru
        const firstInput = newBahan.querySelector('input[name="bahan_nama[]"]');
        firstInput.focus();
        
        // Scroll ke form bahan yang baru
        newBahan.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    function hapusBahan(button) {
        const bahanItem = button.closest('.bahan-item');
        const bahanContainer = document.getElementById("bahan-container");
        
        // Jangan hapus jika hanya ada satu form bahan
        if (bahanContainer.children.length <= 1) {
            alert('Minimal harus ada satu bahan!');
            return;
        }
        
        bahanItem.remove();
        
        // Update nomor urut bahan
        updateNomorBahan();
    }
    
    function updateNomorBahan() {
        const bahanItems = document.querySelectorAll('.bahan-item');
        bahanItems.forEach((item, index) => {
            const header = item.querySelector('h3');
            if (header) {
                header.textContent = `Bahan ${index + 1}`;
            }
        });
    }
    
    function updateReview() {
        document.getElementById("r-admin").textContent = document.querySelector("input[name='nama_admin']").value;
        document.getElementById("r-alamat").textContent = document.querySelector("input[name='alamat_pengiriman']").value;
        document.getElementById("r-kontak").textContent = document.querySelector("input[name='kontak']").value;
        document.getElementById("r-batas").textContent = document.querySelector("input[name='batas_pengiriman']").value;
        
        const tbody = document.querySelector("#review-bahan tbody");
        tbody.innerHTML = '';
        
        const bahanNames = document.querySelectorAll("input[name='bahan_nama[]']");
        const ukurans = document.querySelectorAll("input[name='ukuran[]']");
        const jenis = document.querySelectorAll("input[name='jenis[]']");
        const jumlahs = document.querySelectorAll("input[name='jumlah[]']");
        const satuans = document.querySelectorAll("input[name='satuan[]']");
        
        for (let i = 0; i < bahanNames.length; i++) {
            const newRow = tbody.insertRow();
            newRow.innerHTML = `
                <td class="border border-gray-400 p-2">${i + 1}</td>
                <td class="border border-gray-400 p-2">${bahanNames[i].value}</td>
                <td class="border border-gray-400 p-2">${ukurans[i].value}</td>
                <td class="border border-gray-400 p-2">${jenis[i].value}</td>
                <td class="border border-gray-400 p-2">${jumlahs[i].value}</td>
                <td class="border border-gray-400 p-2">${satuans[i].value}</td>
            `;
        }
    }
    
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        dropdown.classList.toggle('active');
    }
    
    function showDetail(id) {
        // Implementasi untuk menampilkan detail pengajuan
        alert('Detail pengajuan ID: ' + id);
    }
</script>
</body>
</html>