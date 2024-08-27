<?php
header('Content-Type: application/json');

include('koneksi.php');

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Query untuk mengambil data PoI untuk megnhitung jarak
  $stmt = $pdo->query('SELECT name, lat, lng FROM poi');
  $poiData = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Mengembalikan data dalam format JSON
  echo json_encode($poiData);
} catch (PDOException $e) {
  echo json_encode(['error' => $e->getMessage()]);
}