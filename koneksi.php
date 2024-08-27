<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "poi_db";

$kon = new mysqli($host, $user, $password, $db);
if (!$kon) {
   die("Koneksi Gagal:" . mysqli_connect_error());
}
