<?php
$page_title = 'Tambah Projek Berhasil - Mandor';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0fdf4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .success-box {
            background-color: white;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        .success-box h1 {
            color: #16a34a;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .success-box p {
            color: #4b5563;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .checkmark {
            font-size: 50px;
            color: #22c55e;
            margin-bottom: 20px;
        }

        .btn {
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 500;
            color: white;
            background-color: #3b82f6;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>

    <div class="success-box">
        <div class="checkmark">✔</div>
        <h1>Data Projek Tersimpan!</h1>
        <p>Projek telah berhasil ditambahkan ke dalam sistem.</p>
        <a href="dataproject.php" class="btn">Lihat Daftar Projek</a>
    </div>

</body>
</html>
