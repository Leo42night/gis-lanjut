<?php

// digunakan untuk fetcing data map di index
header('Content-Type: application/json');

include 'koneksi.php';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Query untuk mengambil data PoI untuk megnhitung jarak
  $stmt = $pdo->prepare('
    SELECT 
      produk.bar AS productID,
      produk.nama AS product_name, 
      transaksi.harga AS harga, 
      toko.nama AS shop_name, 
      toko.lat AS shop_lat, 
      toko.lng AS shop_lng
    FROM 
        transaksi
    JOIN 
        produk ON transaksi.prod_id = produk.bar
    JOIN 
        toko ON transaksi.toko_id = toko.id;
  ');
  // $stmt->bindParam(':product_id', $_GET['id'], PDO::PARAM_STR); //filter ID dilakukan setelah fetching

  // Execute the statement
  $stmt->execute();

  $poiData = $stmt->fetchAll(PDO::FETCH_ASSOC);
  // die(var_dump($poiData));

  // Mengembalikan data dalam format JSON
  echo json_encode($poiData);
} catch (PDOException $e) {
  echo json_encode(['error' => $e->getMessage()]);
}