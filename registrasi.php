<?php
// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "inerior");

if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrasi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen font-[Poppins]">

<?php
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'gagal') {
        echo "<script>alert('Registrasi gagal! Email mungkin sudah terdaftar.')</script>";
    } elseif ($_GET['pesan'] == 'berhasil') {
        echo "<script>
            alert('Registrasi berhasil! Silakan login.');
            window.location.href = 'login.php';
        </script>";
    }
}
?>


<div class="bg-white rounded-3xl shadow-xl p-14 w-full max-w-md">
  <div class="text-center mb-6">
    <h2 class="text-2xl font-semibold">Registrasi Akun</h2>
    <p class="text-gray-500 text-sm">Silakan isi data di bawah ini</p>
  </div>

  <form action="registrasi_proses.php" method="POST" enctype="multipart/form-data">
    <div class="mb-4">
      <label class="block text-gray-700">Username</label>
      <input type="text" name="username" class="w-full px-3 py-2 border rounded-lg" required>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Email</label>
      <input type="email" name="email" class="w-full px-3 py-2 border rounded-lg" required>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Password</label>
      <input type="password" name="password" class="w-full px-3 py-2 border rounded-lg" required>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Level</label>
      <select name="level" class="w-full px-3 py-2 border rounded-lg" required>
        <option value="">-- Pilih Level --</option>
        <option value="admin">Admin</option>
        <option value="mandor">Mandor</option>
        <option value="desainer">Desainer</option>
      </select>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Profile</label>
      <label for="profile_gambar"
        class="flex flex-col items-center justify-center w-full h-40 border-2 mb-4 border-dashed border-gray-400 rounded-lg cursor-pointer hover:border-gray-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M7 16V4m0 0L3 8m4-4l4 4m5 0a5 5 0 015 5v3a5 5 0 01-5 5H6a5 5 0 01-5-5v-3a5 5 0 015-5h.28" />
        </svg>
        <p class="text-sm text-gray-600">Drop file here</p>
        <p class="text-sm text-indigo-600 underline">or browse</p>
        <input id="profile_gambar" type="file" name="gambar_profile" class="hidden" accept="image/*" required onchange="tampilkanNamaFile(this)">
        <p id="namaFile" class="text-sm text-green-600 mt-2 hidden"></p>
      </label>
    </div>

    <button type="submit" class="w-full bg-black text-white py-2 rounded-lg">Daftar</button>
  </form>

  <p class="text-sm mt-4 text-center text-gray-500">
    Sudah punya akun? <a href="login.php" class="text-blue-500">Login di sini</a>
  </p>
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
</script>

</body>
</html>
