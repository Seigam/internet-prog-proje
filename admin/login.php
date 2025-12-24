<?php
session_start();

if (isset($_SESSION['oturum']) && $_SESSION['oturum'] === true) {
    header("Location: index.php");
    exit;
}

require_once '../include/conn.php';

$hata = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $kadi = trim($_POST['kullanici_adi']);
    $sifre = trim($_POST['sifre']);

    if (empty($kadi) || empty($sifre)) {
        $hata = "Lütfen kullanıcı adı ve şifreyi giriniz.";
    } else {
        $sorgu = $db->prepare("SELECT * FROM yoneticiler WHERE kullanici_adi = :kadi AND aktif = 1");
        $sorgu->execute(['kadi' => $kadi]);
        $yonetici = $sorgu->fetch();

        // 4. Şifre Doğrulama (password_verify)
        if ($yonetici && password_verify($sifre, $yonetici['sifre'])) {
            $_SESSION['oturum'] = true;
            $_SESSION['kullanici_id'] = $yonetici['id'];
            $_SESSION['kullanici_adi'] = $yonetici['kullanici_adi'];
            $_SESSION['ad_soyad'] = $yonetici['ad_soyad'];

            // Panele Yönlendir
            header("Location: index.php");
            exit;
        } else {
            $hata = "Kullanıcı adı veya şifre hatalı!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <div class="login-container">
        <h2>Yönetim Paneli</h2>
        
        <?php if($hata): ?>
            <div class="alert"><?php echo $hata; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="kullanici_adi">Kullanıcı Adı</label>
                <input type="text" id="kullanici_adi" name="kullanici_adi" placeholder="Kullanıcı adınız" required>
            </div>

            <div class="form-group">
                <label for="sifre">Şifre</label>
                <input type="password" id="sifre" name="sifre" placeholder="Şifreniz" required>
            </div>

            <button type="submit" class="btn-giris">Giriş Yap</button>
        </form>
    </div>

</body>
</html>