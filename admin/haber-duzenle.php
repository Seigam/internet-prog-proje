<?php
require_once '../include/conn.php';

// 1. ID KONTROLÜ
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: haberler.php");
    exit;
}

$id = intval($_GET['id']);
$mesaj = "";

// 2. MEVCUT HABERİ ÇEK
$sorgu = $db->prepare("SELECT * FROM haberler WHERE id = :id");
$sorgu->execute([':id' => $id]);
$haber = $sorgu->fetch(PDO::FETCH_ASSOC);

// Haber yoksa geri gönder
if (!$haber) {
    header("Location: haberler.php");
    exit;
}

// 3. KATEGORİLERİ ÇEK (Dropdown için)
$kat_sorgu = $db->prepare("SELECT * FROM kategoriler ORDER BY sira ASC");
$kat_sorgu->execute();
$kategoriler = $kat_sorgu->fetchAll(PDO::FETCH_ASSOC);

// 4. GÜNCELLEME İŞLEMİ
if (isset($_POST['haber_guncelle'])) {
    
    $baslik     = htmlspecialchars(trim($_POST['baslik']));
    $kategori_id= intval($_POST['kategori_id']);
    $icerik     = trim($_POST['icerik']); // İçerikte HTML olabilir diye htmlspecialchars kullanmadık
    $durum      = intval($_POST['yayin_durumu']);
    
    // Eski resim adını sakla
    $resim_adi  = $haber['resim']; 

    // Basit Doğrulama
    if (empty($baslik)) {
        $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Başlık alanı boş bırakılamaz.</div>';
    } else {
        
        // --- RESİM YÜKLEME İŞLEMİ ---
        // Eğer yeni bir dosya seçildiyse:
        if (isset($_FILES['resim']) && $_FILES['resim']['error'] == 0) {
            
            $izin_verilenler = ['jpg', 'jpeg', 'png', 'webp'];
            $dosya_uzantisi  = strtolower(pathinfo($_FILES['resim']['name'], PATHINFO_EXTENSION));

            if (in_array($dosya_uzantisi, $izin_verilenler)) {
                // Yeni isim oluştur
                $yeni_ad = uniqid() . "." . $dosya_uzantisi;
                $hedef_yol = "../yuklemeler/" . $yeni_ad;

                if (move_uploaded_file($_FILES['resim']['tmp_name'], $hedef_yol)) {
                    // Yükleme başarılıysa eski resmi sil (Opsiyonel ama önerilir)
                    if (!empty($haber['resim']) && file_exists("../yuklemeler/" . $haber['resim'])) {
                        unlink("../yuklemeler/" . $haber['resim']);
                    }
                    // Veritabanına gidecek ismi güncelle
                    $resim_adi = $yeni_ad;
                }
            } else {
                $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Sadece JPG, PNG veya WEBP formatları yüklenebilir.</div>';
            }
        }

        // --- VERİTABANI GÜNCELLEME ---
        if (empty($mesaj)) {
            try {
                $sql = "UPDATE haberler SET 
                        baslik = :baslik, 
                        kategori_id = :kat_id, 
                        icerik = :icerik, 
                        resim = :resim, 
                        yayin_durumu = :durum 
                        WHERE id = :id";
                
                $stmt = $db->prepare($sql);
                $sonuc = $stmt->execute([
                    ':baslik' => $baslik,
                    ':kat_id' => $kategori_id,
                    ':icerik' => $icerik,
                    ':resim'  => $resim_adi,
                    ':durum'  => $durum,
                    ':id'     => $id
                ]);

                if ($sonuc) {
                    $mesaj = '<div class="bg-primary/10 border border-primary/20 text-primary p-4 rounded-[6px] mb-6 text-sm flex items-center gap-2"><i class="fa-solid fa-check"></i> Haber başarıyla güncellendi!</div>';
                    
                    // Güncel veriyi sayfada da görelim (Refresh gerekmesin diye)
                    $haber['baslik'] = $baslik;
                    $haber['kategori_id'] = $kategori_id;
                    $haber['icerik'] = $icerik;
                    $haber['resim'] = $resim_adi;
                    $haber['yayin_durumu'] = $durum;
                } else {
                    $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Güncelleme sırasında hata oluştu.</div>';
                }
            } catch (PDOException $e) {
                $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Veritabanı Hatası: ' . $e->getMessage() . '</div>';
            }
        }
    }
}
?>

<?php include 'include/header.php'; ?>

<main class="w-full max-w-4xl mx-auto p-6 md:p-10 pb-20">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fa-solid fa-pen-to-square text-primary"></i>
                <span class="text-gradient">Haber Düzenle</span>
            </h1>
            <p class="text-light-200 mt-2 text-sm">Mevcut haberi düzenliyorsunuz.</p>
        </div>
        <a href="haberler.php" class="text-light-200 hover:text-white transition-colors text-sm flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Listeye Dön
        </a>
    </div>

    <?php echo $mesaj; ?>

    <div class="bg-dark-100 border border-dark-200 rounded-[10px] card-shadow p-8 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>

        <form action="" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">

            <div class="flex flex-col gap-2">
                <label class="text-light-100 text-sm font-medium">Haber Başlığı *</label>
                <input type="text" name="baslik" required
                       value="<?php echo htmlspecialchars($haber['baslik']); ?>"
                       class="w-full bg-dark-200 text-white border border-dark-200 rounded-[6px] px-4 py-3 focus:border-primary focus:outline-none transition-colors placeholder-gray-600">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-2">
                    <label class="text-light-100 text-sm font-medium">Kategori</label>
                    <div class="relative">
                        <select name="kategori_id" class="w-full bg-dark-200 text-white border border-dark-200 rounded-[6px] px-4 py-3 appearance-none focus:border-primary focus:outline-none transition-colors cursor-pointer">
                            <option value="0">Genel</option>
                            <?php foreach ($kategoriler as $kat): ?>
                                <option value="<?php echo $kat['id']; ?>" 
                                    <?php echo ($kat['id'] == $haber['kategori_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kat['kategori_adi']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-500 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-light-100 text-sm font-medium">Yayın Durumu</label>
                    <div class="relative">
                        <select name="yayin_durumu" class="w-full bg-dark-200 text-white border border-dark-200 rounded-[6px] px-4 py-3 appearance-none focus:border-primary focus:outline-none transition-colors cursor-pointer">
                            <option value="1" <?php echo ($haber['yayin_durumu'] == 1) ? 'selected' : ''; ?>>Yayında</option>
                            <option value="0" <?php echo ($haber['yayin_durumu'] == 0) ? 'selected' : ''; ?>>Pasif (Taslak)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-500 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-light-100 text-sm font-medium">Haber Görseli</label>
                
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="w-full md:w-1/3">
                        <div class="border border-dark-200 rounded-[6px] p-2 bg-dark-200/50">
                            <?php if (!empty($haber['resim'])): ?>
                                <img src="../yuklemeler/<?php echo $haber['resim']; ?>" class="w-full h-32 object-cover rounded-[4px]" alt="Mevcut Resim">
                                <p class="text-[10px] text-center text-gray-500 mt-2">Mevcut Görsel</p>
                            <?php else: ?>
                                <div class="w-full h-32 flex items-center justify-center bg-dark-200 text-gray-600 rounded-[4px]">
                                    <span class="text-xs">Görsel Yok</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="w-full md:w-2/3">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dark-200 border-dashed rounded-[6px] cursor-pointer bg-dark-200/30 hover:bg-dark-200/50 hover:border-primary/50 transition-all group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-500 group-hover:text-primary mb-2 transition-colors"></i>
                                <p class="text-sm text-gray-400"><span class="font-semibold group-hover:text-white transition-colors">Görseli değiştirmek için tıklayın</span></p>
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG, WEBP (Opsiyonel)</p>
                            </div>
                            <input type="file" name="resim" class="hidden" accept="image/png, image/jpeg, image/webp">
                        </label>
                        <p class="text-xs text-yellow-500/80 mt-2">
                            <i class="fa-solid fa-circle-info mr-1"></i> 
                            Eğer görseli değiştirmek istemiyorsanız burayı boş bırakın.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-light-100 text-sm font-medium">Haber İçeriği</label>
                <textarea name="icerik" rows="10" 
                          class="w-full bg-dark-200 text-white border border-dark-200 rounded-[6px] px-4 py-3 focus:border-primary focus:outline-none transition-colors placeholder-gray-600 leading-relaxed"><?php echo htmlspecialchars($haber['icerik']); ?></textarea>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mt-4 pt-6 border-t border-dark-200">
                <button type="submit" name="haber_guncelle" 
                        class="flex-1 bg-primary text-black font-bold py-3 rounded-[6px] hover:bg-primary/90 transition-all shadow-[0_0_15px_rgba(89,222,202,0.3)] hover:shadow-[0_0_25px_rgba(89,222,202,0.5)] cursor-pointer">
                    Güncelle
                </button>
                
                <a href="haberler.php" 
                   class="flex-1 text-center bg-dark-200 text-light-100 font-medium py-3 rounded-[6px] hover:bg-dark-200/80 transition-colors border border-dark-200">
                    İptal
                </a>
            </div>

        </form>
    </div>

</main>
