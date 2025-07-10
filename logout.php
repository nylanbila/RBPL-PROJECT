<?php
session_start();
session_unset();  // Hapus semua variabel session
session_destroy(); // Hancurkan session

// Redirect ke halaman login
header("Location:login.php");
exit();
?>
