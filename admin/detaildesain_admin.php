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

// Ambil parameter ID dari URL
$designId = $_GET['id'] ?? '';
$design = null;

if (!empty($designId)) {
    // Query untuk mengambil data desain berdasarkan ID
    $query = "SELECT dd.*, d.username as nama_desainer 
              FROM data_desain dd 
              LEFT JOIN desainer d ON dd.id_desainer = d.id_desainer 
              WHERE dd.id_desain = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $designId);
    $stmt->execute();
    $result = $stmt->get_result();
    
   if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Assign data dari database ke array
    $design = [
        'name' => $row['nama_desain'],
        'designer' => $row['nama_desainer'] ?? 'Unknown',
        'upload_date' => date('d-m-Y', strtotime($row['tanggal_dibuat'])),
        'size' => $row['ukuran'],
        'material' => explode(',', $row['material']), // Split material jika ada beberapa
        'image' => "../desainer/" . $row['gambar_desain'], // TAMBAHKAN PATH PREFIX
        'deskripsi_desain' => $row['deskripsi_desain'],
        'status' => $row['status_desain'],
        'feedback' => '',
        'feedback_date' => ''
    ];

        // Ambil feedback dan tanggal dari tabel revisi_desain
        $query_feedback = "SELECT feedback_admin, DATE_FORMAT(tanggal_upload_revisi, '%d-%m-%Y') as tanggal_feedback 
                           FROM revisi_desain 
                           WHERE id_desain = ? 
                           ORDER BY id_revisi DESC LIMIT 1";
        $stmt_feedback = $mysqli->prepare($query_feedback);
        $stmt_feedback->bind_param("s", $designId);
        $stmt_feedback->execute();
        $result_feedback = $stmt_feedback->get_result();
        
        if ($result_feedback && $result_feedback->num_rows > 0) {
            $row_feedback = $result_feedback->fetch_assoc();
            $design['feedback'] = $row_feedback['feedback_admin'];
            $design['feedback_date'] = $row_feedback['tanggal_feedback'];
        }
        
        // Cek apakah ada revisi terbaru dengan file_revisi
        $query_revisi = "SELECT file_revisi FROM revisi_desain 
                         WHERE id_desain = ? 
                         AND file_revisi IS NOT NULL AND file_revisi != ''
                         ORDER BY id_revisi DESC LIMIT 1";
        $stmt_revisi = $mysqli->prepare($query_revisi);
        $stmt_revisi->bind_param("s", $designId);
        $stmt_revisi->execute();
        $result_revisi = $stmt_revisi->get_result();
        
        if ($result_revisi && $result_revisi->num_rows > 0) {
            $row_revisi = $result_revisi->fetch_assoc();
            $design['image'] = "../desainer/" . $row_revisi['file_revisi'];
        }
    }
}

// Proses approval desain
if (isset($_POST['approve_design'])) {
    $update_query = "UPDATE data_desain SET status_desain = 'Disetujui' WHERE id_desain = ?";
    $stmt_update = $mysqli->prepare($update_query);
    $stmt_update->bind_param("s", $designId);
    if ($stmt_update->execute()) {
        echo "<script>alert('Desain berhasil disetujui!'); window.location.href='detaildesain_admin.php?id=$designId';</script>";
    }
}

// Proses feedback desain
if (isset($_POST['submit_feedback'])) {
    $feedback = $_POST['feedback'];
    $deadline = $_POST['deadline'];
    
    // Validasi input dan session
    if (empty($feedback) || empty($deadline)) {
        echo "<script>alert('Feedback dan deadline harus diisi!'); window.location.href='detaildesain_admin.php?id=$designId';</script>";
        exit();
    }

    // Pastikan admin sudah login
    if (!$id_admin) {
        echo "<script>alert('Session expired, silakan login kembali!'); window.location.href='../login.php';</script>";
        exit();
    }
    
    // Cek apakah ID admin ada di database
    $check_admin = "SELECT id_admin FROM admin WHERE id_admin = ?";
    $stmt_check = $mysqli->prepare($check_admin);
    $stmt_check->bind_param("i", $id_admin);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        echo "<script>alert('ID Admin tidak valid!'); window.location.href='../login.php';</script>";
        exit();
    }

    // Mulai transaksi database
    $mysqli->begin_transaction();

    try {
        // Insert feedback ke tabel revisi_desain (dengan id_admin)
        $insert_feedback = "INSERT INTO revisi_desain (id_desain, id_admin, feedback_admin, deadline_revisi, tanggal_upload_revisi) VALUES (?, ?, ?, ?, NOW())";
        $stmt_insert = $mysqli->prepare($insert_feedback);
        
        if (!$stmt_insert) {
            throw new Exception("Prepare statement error: " . $mysqli->error);
        }
        
        $stmt_insert->bind_param("siss", $designId, $id_admin, $feedback, $deadline);
        
        if (!$stmt_insert->execute()) {
            throw new Exception("Execute error: " . $stmt_insert->error);
        }
        
        // Update status desain menjadi 'Revisi'
        $update_status = "UPDATE data_desain SET status_desain = 'Revisi' WHERE id_desain = ?";
        $stmt_status = $mysqli->prepare($update_status);
        
        if (!$stmt_status) {
            throw new Exception("Prepare statement error: " . $mysqli->error);
        }
        
        $stmt_status->bind_param("s", $designId);
        
        if (!$stmt_status->execute()) {
            throw new Exception("Execute error: " . $stmt_status->error);
        }
        
        // Commit transaksi
        $mysqli->commit();
        
        echo "<script>alert('Feedback berhasil dikirim!'); window.location.href='detaildesain_admin.php?id=$designId';</script>";
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $mysqli->rollback();
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='detaildesain_admin.php?id=$designId';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Desain - Admin</title>
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
                <div class="flex items-center space-x-3 cursor-pointer hover:bg-white/10 py-2 pl-2 rounded" onclick="window.location.href='dashboard_admin.php'">
                    <img src="admin/assets/Frame.png" class="w-6 h-7" alt="Dashboard">
                    <span class="text-xl font-medium">Dashboard</span>
                </div>

                <!-- Bahan -->
                <div class="flex items-center space-x-3 cursor-pointer hover:bg-white/10 py-2 pl-2 rounded" onclick="toggleDropdown('bahan-dropdown')">
                    <img src="admin/assets/Frame(2).png" class="w-6 h-6" alt="Bahan">
                    <span class="text-2xl font-medium">Bahan</span>
                    <img src="admin/assets/Frame5.png" class="w-5 h-5 ml-auto" alt="Toggle">
                </div>
                <div id="bahan-dropdown" class="dropdown-content pl-12 space-y-1">
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='data-bahan.php'">Data Bahan</div>
                    <div class="hover:bg-zinc-700 px-2 py-1 rounded cursor-pointer" onclick="window.location.href='pengajuan-bahan.php'">Pengajuan Bahan</div>
                </div>

                <!-- Desain -->
                <div class="flex items-center space-x-3 cursor-pointer hover:bg-white/10 py-2 pl-2 rounded bg-neutral-300/70" onclick="toggleDropdown('desain-dropdown')">
                    <img src="admin/assets/Frame(3).png" class="w-6 h-6" alt="Desain">
                    <span class="text-2xl font-medium">Desain</span>
                    <img src="admin/assets/Frame5.png" class="w-5 h-5 ml-auto" alt="Toggle">
                </div>
                <div id="desain-dropdown" class="dropdown-content pl-12 space-y-1 active">
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

        <!-- Content -->
        <div class="flex-1 overflow-auto">
            <?php if ($design): ?>
                <!-- Title -->
                <div class="px-10 py-6">
                    <h2 class="text-4xl font-semibold text-zinc-800 mb-2">Detail Desain</h2>
                    <p class="text-neutral-500 text-xl">
                        Berikut detail desain <?= htmlspecialchars($design['name']) ?>
                    </p>
                </div>

                <!-- Detail Content -->
                <div class="px-10 pb-10">
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <div class="flex flex-col lg:flex-row gap-8">
                            <!-- Image -->
                            <div class="flex-shrink-0">
                                <img src="<?= htmlspecialchars($design['image']) ?>" 
                                     alt="<?= htmlspecialchars($design['name']) ?>"
                                     class="w-full lg:w-[439px] h-[391px] rounded-lg object-cover border border-neutral-400">
                            </div>

                            <!-- Details -->
                            <div class="flex-1">
                                <h3 class="text-2xl font-semibold text-zinc-800 mb-4"><?= htmlspecialchars($design['name']) ?></h3>
                                
                                <div class="space-y-3 text-black text-[15px]">
                                    <div><strong>ID Desain:</strong> <?= htmlspecialchars($designId) ?></div>
                                    <div><strong>Nama Desainer:</strong> <?= htmlspecialchars($design['designer']) ?></div>
                                    <div><strong>Tanggal Unggah:</strong> <?= htmlspecialchars($design['upload_date']) ?></div>
                                    <div><strong>Ukuran:</strong> <?= htmlspecialchars($design['size']) ?></div>
                                    
                                    <div>
                                        <strong>Material:</strong>
                                        <ul class="list-disc list-inside ml-4 mt-1">
                                            <?php if (is_array($design['material'])): ?>
                                                <?php foreach ($design['material'] as $item): ?>
                                                    <li><?= htmlspecialchars(trim($item)) ?></li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li><?= htmlspecialchars($design['material']) ?></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>

                                    <div>
                                        <strong>Deskripsi:</strong>
                                        <p class="mt-1"><?= htmlspecialchars($design['deskripsi_desain']) ?></p>
                                    </div>

                                    <div>
                                        <strong>Status:</strong>
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
                                        <span class="inline-block text-white px-3 py-1 rounded-full text-sm font-semibold <?= $badgeColor ?> mt-1">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </div>

                                    <?php if (!empty($design['feedback'])): ?>
                                    <div>
                                        <strong>Feedback:</strong>
                                        <div class="bg-neutral-100 border border-gray-300 px-3 py-2 rounded-md mt-1 text-sm">
                                            <?= htmlspecialchars($design['feedback_date']) ?> - "<?= htmlspecialchars($design['feedback']) ?>"
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap gap-4 mt-6">
                                    <form method="POST" style="display: inline;">
                                        <button type="submit" name="approve_design" 
                                                onclick="return confirm('Apakah Anda yakin ingin menyetujui desain ini?')" 
                                                class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-full text-sm flex items-center gap-2 transition-colors">
                                            ✅ Setujui
                                        </button>
                                    </form>
                                    <button onclick="openFeedbackPopup()" 
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white font-semibold py-2 px-6 rounded-full text-sm flex items-center gap-2 transition-colors">
                                        ✏️ Revisi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="px-10 py-20 text-center">
                    <div class="text-red-600 font-semibold text-lg">
                        Data desain tidak ditemukan.
                    </div>
                    <button onclick="window.location.href='data-desain.php'" 
                            class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Kembali ke Data Desain
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Popup Form Feedback -->
<div id="popupFeedback" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <h2 class="text-xl font-semibold mb-4 text-zinc-800">Masukkan Feedback Anda</h2>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                <textarea name="feedback" 
                          class="w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                          rows="4" 
                          placeholder="Contoh: Desain meja perlu direvisi..." 
                          required></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deadline Revisi</label>
                <input type="date" name="deadline" 
                       class="w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       required>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeFeedbackPopup()" 
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit" name="submit_feedback" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md font-semibold transition-colors">
                    Kirim Feedback
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('active');
}

function openFeedbackPopup() {
    document.getElementById('popupFeedback').classList.remove('hidden');
}

function closeFeedbackPopup() {
    document.getElementById('popupFeedback').classList.add('hidden');
}

function logout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = '../logout.php';
        }
    }

// Close popup when clicking outside
document.getElementById('popupFeedback').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFeedbackPopup();
    }
});
</script>
</body>
</html>