<?php
$koneksi = new mysqli("localhost", "root", "", "inerior");
	if($koneksi->connect_error) { //cek error
		die("Koneksi gagal dilakukan".$koneksi->connect_error);
	}
?>