<?php
// Veritabanı Ayarları
$host = 'localhost';
$dbname = 'haber_db'; // Veritabanı
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $db->exec("SET NAMES 'utf8mb4'");
    $db->exec("SET CHARSET 'utf8mb4'");

} catch (PDOException $e) {
    $hata_mesaji = "[" . date("d-m-Y H:i:s") . "] - " . $e->getMessage() . PHP_EOL;

    file_put_contents(__DIR__ . '/db_hatalari.log', $hata_mesaji, FILE_APPEND);

    die("Bir hata ile karşılaşıldı. Lütfen daha sonra tekrar deneyiniz.");
}
?>