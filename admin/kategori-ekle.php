<?php
require_once '../include/conn.php';

$mesaj = "";

if (isset($_POST['kategori_ekle'])) {
    $kategori_adi = htmlspecialchars(trim($_POST['kategori_adi']));
    $sira = !empty($_POST['sira']) ? intval($_POST['sira']) : 0; 

    if (empty($kategori_adi)) {
        $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Lütfen kategori adını yazınız.</div>';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO kategoriler (kategori_adi, sira) VALUES (:adi, :sira)");
            $sonuc = $stmt->execute([':adi' => $kategori_adi, ':sira' => $sira]);

            if ($sonuc) {
                $mesaj = '<div class="bg-primary/10 border border-primary/20 text-primary p-4 rounded-[6px] mb-6 text-sm">Kategori başarıyla eklendi!</div>';
            } else {
                $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Hata oluştu.</div>';
            }
        } catch (PDOException $e) {
            $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">DB Hatası: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<?php include 'include/header.php'; ?>

<main class="flex-center min-h-[80vh] w-full px-4">
    <div class="w-full max-w-md bg-dark-100 border border-dark-200 rounded-[10px] p-8 card-shadow relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-2 flex justify-center items-center gap-2">
                <i class="fa fa-plus-circle text-primary"></i> 
                <span class="text-gradient">Yeni Kategori</span>
            </h2>
            <p class="text-light-200 text-sm">Veritabanına yeni bir kategori tanımlayın.</p>
        </div>

        <?php echo $mesaj; ?>

        <form action="" method="POST" class="flex flex-col gap-5">
            <div class="flex flex-col gap-2">
                <label class="text-light-100 text-sm font-medium">Kategori Adı *</label>
                <input type="text" name="kategori_adi" required
                       class="w-full bg-dark-200 text-white border border-dark-200 rounded-[6px] px-4 py-3 focus:border-primary focus:outline-none transition-colors placeholder-gray-600"
                       placeholder="Örn: Teknoloji">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-light-100 text-sm font-medium">Sıra No</label>
                <input type="number" name="sira" value="0"
                       class="w-full bg-dark-200 text-white border border-dark-200 rounded-[6px] px-4 py-3 focus:border-primary focus:outline-none transition-colors">
            </div>

            <div class="flex flex-col gap-3 mt-2">
                <button type="submit" name="kategori_ekle" 
                        class="w-full bg-primary text-black font-semibold py-3 rounded-[6px] hover:bg-primary/90 transition-all cursor-pointer shadow-[0_0_15px_rgba(89,222,202,0.3)] hover:shadow-[0_0_25px_rgba(89,222,202,0.5)]">
                    Kaydet
                </button>
                
                <a href="kategoriler.php" 
                   class="w-full text-center bg-dark-200 text-light-100 font-medium py-3 rounded-[6px] hover:bg-dark-200/80 transition-colors border border-dark-200">
                    Geri Dön
                </a>
            </div>
        </form>
    </div>
</main>