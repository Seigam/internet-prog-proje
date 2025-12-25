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
    <title>Panel Giriş | YazTek Haber</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-background flex-center h-screen w-full">

    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 50% 10%, #182830 0%, #030708 60%); z-index: -1;"></div>

    <div class="glass card-shadow p-10 w-full max-w-md flex flex-col gap-4 border border-dark-200">
        
        <div class="text-center mb-8">
            <h1 class="text-gradient text-4xl font-bold mb-2">Yönetim Paneli</h1>
            <p class="text-light-200 text-sm">Devam etmek için giriş yapınız</p>
        </div>

        <?php if($hata): ?>
            <div class="bg-destructive/20 border border-destructive text-red-400 p-3 rounded-[6px] text-sm text-center">
                <?php echo $hata; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <label for="kullanici_adi" class="text-light-100 text-sm font-semibold">Kullanıcı Adı</label>
                <input type="text" id="kullanici_adi" name="kullanici_adi" 
                       class="bg-dark-200 text-white rounded-[6px] px-5 py-2.5 border border-border-dark focus:border-primary outline-none transition-all" 
                       placeholder="Örn: admin" required>
            </div>

            <div class="flex flex-col gap-2">
                <label for="sifre" class="text-light-100 text-sm font-semibold">Şifre</label>
                <input type="password" id="sifre" name="sifre" 
                       class="bg-dark-200 text-white rounded-[6px] px-5 py-2.5 border border-border-dark focus:border-primary outline-none transition-all" 
                       placeholder="••••••" required>
            </div>

            <button type="submit" class="bg-primary hover:bg-primary/90 text-black font-bold rounded-[6px] px-4 py-3 mt-2 cursor-pointer transition-all card-shadow">
                Giriş Yap
            </button>
        </form>
        
        <div class="text-center mt-4">
            <p class="text-light-200 text-xs">YazTek Haber &copy; <?php echo date("Y"); ?></p>
        </div>
    </div>

</body>
</html>