<?php
require_once '../include/conn.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: haberler.php");
    exit;
}

$id = intval($_GET['id']);

$sorgu = $db->prepare("SELECT resim FROM haberler WHERE id = :id");
$sorgu->execute([':id' => $id]);
$haber = $sorgu->fetch(PDO::FETCH_ASSOC);

if ($haber) {
    if (!empty($haber['resim']) && file_exists("../uploads/" . $haber['resim'])) {
        unlink("../uploads/" . $haber['resim']);
    }

    $sil_sorgu = $db->prepare("DELETE FROM haberler WHERE id = :id");
    $sil = $sil_sorgu->execute([':id' => $id]);

    if ($sil) {
        header("Location: haberler.php?durum=ok");
        exit;
    }
}

header("Location: haberler.php?durum=no");
exit;
?>