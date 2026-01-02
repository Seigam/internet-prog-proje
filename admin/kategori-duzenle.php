<?php
require_once '../include/conn.php';

include 'include/header.php'; 

// --- ID KONTROLÜ ---
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location.href='kategoriler.php';</script>";
    exit;
}

$id = intval($_GET['id']);
$mesaj = "";

$sorgu = $db->prepare("SELECT * FROM kategoriler WHERE id = :id");
$sorgu->execute([':id' => $id]);
$kategori = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$kategori) {
    echo "<script>window.location.href='kategoriler.php';</script>";
    exit;
}

// --- GÜNCELLEME İŞLEMİ ---
if (isset($_POST['kategori_duzenle'])) {
    
    $kategori_adi = htmlspecialchars(trim($_POST['kategori_adi']));
    $sira = !empty($_POST['sira']) ? intval($_POST['sira']) : 0;

    if (empty($kategori_adi)) {
        $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Kategori adı boş olamaz.</div>';
    } else {
        try {
            $sql = "UPDATE kategoriler SET kategori_adi = :adi, sira = :sira WHERE id = :id";
            $stmt = $db->prepare($sql);
            
            $sonuc = $stmt->execute([
                ':adi' => $kategori_adi,
                ':sira' => $sira,
                ':id' => $id
            ]);

            if ($sonuc) {
                $mesaj = '<div class="bg-primary/10 border border-primary/20 text-primary p-4 rounded-[6px] mb-6 text-sm">Kategori başarıyla güncellendi.</div>';
                $kategori['kategori_adi'] = $kategori_adi;
                $kategori['sira'] = $sira;
            } else {
                $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Güncelleme başarısız oldu.</div>';
            }
        } catch (PDOException $e) {
            $mesaj = '<div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm">Hata: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<main class="flex-center min-h-[80vh] w-full px-4">
    <div class="w-full max-w-md bg-dark-100 border border-dark-200 rounded-[10px] p-8 card-shadow relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-2 flex justify-center items-center gap-2">
                <i class="fa fa-edit text-primary"></i> 
                <span class="text-gradient">Kategori Düzenle</span>
            </h2>
            <p class="text-light-200 text-sm">Kategori bilgilerini güncelleyin.</p>
        </div>
        
        <?php echo $mesaj; ?>

        <form action="" method="POST" class="flex flex-col gap-5">
            <div class="flex flex-col gap-2">
                <label class="text-light-100 text-sm font-medium">Kategori Adı *</label>
                <input type="text" name="kategori_adi" 
                       value="<?php echo htmlspecialchars($kategori['kategori_adi']); ?>" required
                       class="w-full bg-dark-200 text-white border border-dark-200 rounded-[6px] px-4 py-3 focus:border-primary focus:outline-none transition-colors">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-light-100 text-sm font-medium">Sıra No</label>
                <input type="number" name="sira" 
                       value="<?php echo $kategori['sira']; ?>"
                       class="w-full bg-dark-200 text-white border border-dark-200 rounded-[6px] px-4 py-3 focus:border-primary focus:outline-none transition-colors">
            </div>

            <div class="flex flex-col gap-3 mt-2">
                <button type="submit" name="kategori_duzenle" 
                        class="w-full bg-primary text-black font-semibold py-3 rounded-[6px] hover:bg-primary/90 transition-all cursor-pointer shadow-[0_0_15px_rgba(89,222,202,0.3)] hover:shadow-[0_0_25px_rgba(89,222,202,0.5)]">
                    Güncelle
                </button>
                <a href="kategoriler.php" class="w-full text-center bg-dark-200 text-light-100 font-medium py-3 rounded-[6px] hover:bg-dark-200/80 transition-colors border border-dark-200">İptal</a>
            </div>
        </form>
    </div>
</main>