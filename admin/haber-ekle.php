<?php
require_once 'include/header.php';
require_once '../include/conn.php';

$mesaj = [];


$kat_sorgu = $db->prepare("SELECT * FROM kategoriler ORDER BY kategori_adi ASC");
$kat_sorgu->execute();
$kategoriler = $kat_sorgu->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    $baslik = trim($_POST['baslik']);
    $kategori_id = intval($_POST['kategori_id']);
    $ozet = trim($_POST['ozet']);
    $icerik = trim($_POST['icerik']);
    $yayin_durumu = isset($_POST['yayin_durumu']) ? 1 : 0;


    if (empty($baslik) || empty($icerik) || $kategori_id == 0) {
        $mesaj['hata'] = "Lütfen başlık, kategori ve içerik alanlarını doldurunuz.";
    } else {
        
        // --- DOSYA YÜKLEME ---
        $resim_adi = null;

        if (isset($_FILES['resim']) && $_FILES['resim']['error'] === 0) {
            
            $izin_verilen_uzantilar = ['jpg', 'jpeg', 'png', 'webp'];
            $dosya_adi = $_FILES['resim']['name'];
            $dosya_boyutu = $_FILES['resim']['size'];
            $gecici_yol = $_FILES['resim']['tmp_name'];
            
            $uzanti = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));

            if (!in_array($uzanti, $izin_verilen_uzantilar)) {
                $mesaj['hata'] = "Sadece JPG, PNG ve WEBP formatları yüklenebilir.";
            } 
            // Boyut Kontrolü (Max 2MB)
            elseif ($dosya_boyutu > 20 * 1024 * 1024) {
                $mesaj['hata'] = "Dosya boyutu 2MB'dan büyük olamaz.";
            } 
            else {
                // Unique Dosya Adları 
                $yeni_ad = "haber-" . uniqid() . "." . $uzanti;
                $hedef_yol = "../uploads/" . $yeni_ad;

                if (move_uploaded_file($gecici_yol, $hedef_yol)) {
                    $resim_adi = $yeni_ad;
                } else {
                    $mesaj['hata'] = "Resim yüklenirken bir hata oluştu.";
                }
            }
        }

        if (!isset($mesaj['hata'])) {
            $sql = "INSERT INTO haberler (kategori_id, baslik, ozet, icerik, resim, yayin_durumu) 
                    VALUES (:kat_id, :baslik, :ozet, :icerik, :resim, :durum)";
            
            $ekle = $db->prepare($sql);
            $sonuc = $ekle->execute([
                'kat_id' => $kategori_id,
                'baslik' => $baslik,
                'ozet' => $ozet,
                'icerik' => $icerik,
                'resim' => $resim_adi,
                'durum' => $yayin_durumu
            ]);

            if ($sonuc) {
                $mesaj['basari'] = "Haber başarıyla eklendi.";
            } else {
                $mesaj['hata'] = "Veritabanı hatası oluştu.";
            }
        }
    }
}
?>

<div class="mb-8">
    <h1 class="text-gradient text-4xl font-bold">Yeni Haber Ekle</h1>
    <p class="text-light-200 mt-2">Sitenize yeni bir içerik eklemek için formu doldurun.</p>
</div>

<?php if (isset($mesaj['hata'])): ?>
    <div class="mb-6 p-4 rounded bg-destructive/20 border border-destructive text-red-400">
        <i class="fa-solid fa-circle-exclamation mr-2"></i> <?php echo $mesaj['hata']; ?>
    </div>
<?php endif; ?>

<?php if (isset($mesaj['basari'])): ?>
    <div class="mb-6 p-4 rounded bg-primary/20 border border-primary text-primary">
        <i class="fa-solid fa-check-circle mr-2"></i> <?php echo $mesaj['basari']; ?>
    </div>
<?php endif; ?>


<div class="glass card-shadow p-8 w-full max-w-4xl">
    
    <form method="POST" action="" enctype="multipart/form-data" class="flex flex-col gap-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-2 flex flex-col gap-2">
                <label for="baslik" class="text-light-100 font-semibold">Haber Başlığı <span class="text-red-500">*</span></label>
                <input type="text" id="baslik" name="baslik" required
                       class="bg-dark-200 text-white rounded-[6px] px-4 py-3 border border-border-dark focus:border-primary outline-none transition-all"
                       placeholder="Örn: Teknoloji Dünyasında Yeni Gelişmeler">
            </div>

            <div class="flex flex-col gap-2">
                <label for="kategori_id" class="text-light-100 font-semibold">Kategori <span class="text-red-500">*</span></label>
                <select id="kategori_id" name="kategori_id" required
                        class="bg-dark-200 text-white rounded-[6px] px-4 py-3 border border-border-dark focus:border-primary outline-none transition-all cursor-pointer">
                    <option value="0">Seçiniz...</option>
                    <?php foreach ($kategoriler as $kat): ?>
                        <option value="<?php echo $kat['id']; ?>"><?php echo htmlspecialchars($kat['kategori_adi']); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if(count($kategoriler) == 0): ?>
                    <span class="text-xs text-red-400">Önce kategori eklemelisiniz!</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <label for="ozet" class="text-light-100 font-semibold">Kısa Özet (Meta Description)</label>
            <textarea id="ozet" name="ozet" rows="2"
                      class="bg-dark-200 text-white rounded-[6px] px-4 py-3 border border-border-dark focus:border-primary outline-none transition-all resize-none"
                      placeholder="Haberin listeleme ekranında görünecek kısa özeti..."></textarea>
        </div>

        <div class="flex flex-col gap-2">
            <label for="icerik" class="text-light-100 font-semibold">Haber İçeriği <span class="text-red-500">*</span></label>
            <textarea id="icerik" name="icerik" rows="10" required
                      class="bg-dark-200 text-white rounded-[6px] px-4 py-3 border border-border-dark focus:border-primary outline-none transition-all"
                      placeholder="Haber detaylarını buraya yazınız..."></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            
            <div class="flex flex-col gap-2">
                <label for="resim" class="text-light-100 font-semibold">Kapak Görseli</label>
                <input type="file" id="resim" name="resim" accept=".jpg,.jpeg,.png,.webp"
                       class="block w-full text-sm text-light-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-dark-100 file:text-primary hover:file:bg-dark-200 cursor-pointer">
                <span class="text-xs text-gray-500">Max: 2MB (jpg, png, webp)</span>
            </div>

            <div class="flex items-center gap-3 bg-dark-200 p-3 rounded border border-border-dark w-fit">
                <input type="checkbox" id="yayin_durumu" name="yayin_durumu" value="1" checked
                       class="w-5 h-5 accent-primary cursor-pointer">
                <label for="yayin_durumu" class="text-white cursor-pointer select-none">Bu haberi hemen yayınla</label>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="bg-primary hover:bg-primary/90 text-black font-bold rounded-[6px] px-8 py-3 transition-all shadow-lg w-full md:w-auto">
                <i class="fa-solid fa-save mr-2"></i> İçeriği Kaydet
            </button>
        </div>

    </form>
</div>

<?php require_once 'inc/footer.php'; ?>