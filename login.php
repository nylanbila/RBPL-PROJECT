<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com">
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-200 flex items-center justify-center min-h-screen">

<?php
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "gagal")
            {
                // echo "Login gagal. Email atau password salah.";
                ?>
                <script>alert('Login gagal. Email, password, atau level user salah.')</script>
                <?php
            } elseif ($_GET['pesan'] == "logout")
            {
                // echo "Logout berhasil";
                ?>
                <script>alert('Logout Berhasil')</script>
                <?php
            } elseif ($_GET['pesan'] == "belum_login")
            {
                echo "Anda harus login terlebih dahulu";
            }
        }
    ?>

    <div class="bg-white rounded-3xl shadow-xl p-14 w-834 h-950">
        <div class="flex justify-center m-0">
            <img alt="Logo of the company" src="img/logo.png" width="150px" />
        </div>
        <h1 class="text-2xl text-left mb-2" font-family="Poppins" style="font-weight:600;">
            Login
        </h1>

        <p class="text-left text-gray-500 mb-6 " font-family="Poppins" style="font-weight:400; font-style:normal;">
            Silahkan masukkan username dan password Anda
        </p>

        <form method ="POST" action="loginproses.php">
            <div class="mb-4">
                <label class="block text-gray-700">
                    Username
                </label>
                <input class="w-full px-3 py-2 border rounded-lg" type="email" name="username" placeholder="example123@gmail.com" />
            </div>
            <div class="mb-4 relative">
                <label class="block text-gray-700">
                    Password
                </label>
                <input class="w-full px-3 py-2 border rounded-lg" type="password" name="password"/>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">
                    Level user
                </label>
                <select class="w-full px-3 py-2 border rounded-lg" name="level">

                    <option value="admin">
                        admin
                    </option>
                    <option value="mandor">
                        mandor
                    </option>
                    <option value="desainer">
                        desainer
                    </option>
                </select>
            </div>
            <div class="flex justify-between items-center mb-4">
                <a class="text-blue-500" href="#">
                    Lupa password ?
                </a>
            </div>
            <button class="w-full bg-black text-white py-2 rounded-lg" type="submit">
                Login
            </button>
        </form>
    </div>
</body>

</html>