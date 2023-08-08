<?php  

$host = "localhost";
$database = "kelola_ntb";
$username = "root";
$password = "";

$koneksi = mysqli_connect($host, $username, $password, $database);
//$koneksi = mysqli_real_escape_string($host, $username, $password, $database);

if (!$koneksi) {
	echo "Koneksi gagal " . mysqli_connect_error();
}

?>