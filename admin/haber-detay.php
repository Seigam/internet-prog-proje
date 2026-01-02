<?php
require_once '../include/conn.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: haberler.php");
    exit;
}

$id = intval($_GET['id']);

$sorgu = $db->prepare("
    SELECT h.*, k.kategori_adi 
    FROM haberler h 
    LEFT JOIN kategoriler k ON h.kategori_id = k.id 
    WHERE h.id = :id
");
$sorgu->execute([':id' => $id]);
$haber = $sorgu->fetch(PDO::FETCH_ASSOC);

// Eğer haber bulunamazsa listeye geri gönder
if (!$haber) {
    header("Location: haberler.php");
    exit;
}
?>

<?php include 'include/header.php'; ?>

<main class="w-full max-w-4xl mx-auto p-6 md:p-10 pb-20">

    <div class="mb-8">
        <a href="haberler.php" class="inline-flex items-center gap-2 text-light-200 hover:text-primary transition-colors text-sm font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Haber Listesine Dön
        </a>
    </div>

    <div class="bg-dark-100 border border-dark-200 rounded-[10px] card-shadow overflow-hidden relative">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>

        <div class="w-full h-[300px] md:h-[400px] bg-dark-200 relative group overflow-hidden">
            <?php if (!empty($haber['resim'])): ?>
                <img src="../yuklemeler/<?php echo htmlspecialchars($haber['resim']); ?>" 
                     alt="<?php echo htmlspecialchars($haber['baslik']); ?>" 
                     class="w-full h-full object-cover">
                
                <div class="absolute inset-0 bg-gradient-to-t from-dark-100 via-transparent to-transparent"></div>
            <?php else: ?>
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-600">
                    <i class="fa-regular fa-image text-6xl opacity-20 mb-3"></i>
                    <span class="text-sm">Bu haberin görseli bulunmuyor</span>
                </div>
            <?php endif; ?>

            <div class="absolute top-4 right-4 flex gap-2">
                <?php if ($haber['yayin_durumu'] == 1): ?>
                    <span class="bg-black/60 backdrop-blur-md text-green-400 border border-green-400/30 text-xs font-bold px-3 py-1.5 rounded-[6px]">
                        <i class="fa-solid fa-circle text-[8px] mr-1"></i> YAYINDA
                    </span>
                <?php else: ?>
                    <span class="bg-black/60 backdrop-blur-md text-red-400 border border-red-400/30 text-xs font-bold px-3 py-1.5 rounded-[6px]">
                        PASİF
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="absolute bottom-4 left-4">
                <span class="bg-primary text-black text-xs font-bold px-3 py-1.5 rounded-[4px] uppercase tracking-wider shadow-lg">
                    <?php echo htmlspecialchars($haber['kategori_adi'] ?? 'Genel'); ?>
                </span>
            </div>
        </div>

        <div class="p-8 md:p-10">
            
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4 border-b border-dark-200 pb-4">
                <i class="fa-regular fa-calendar text-primary"></i>
                <span><?php echo date("d.m.Y H:i", strtotime($haber['eklenme_tarihi'])); ?></span>
                <span class="mx-2 text-dark-200">|</span>
                <span class="text-xs text-light-200">ID: #<?php echo $haber['id']; ?></span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-white mb-8 leading-tight">
                <?php echo htmlspecialchars($haber['baslik']); ?>
            </h1>

            <div class="text-light-200 leading-relaxed text-lg space-y-4">
                <?php echo nl2br(htmlspecialchars($haber['icerik'])); ?>
            </div>

        </div>

        <div class="bg-dark-200/50 p-6 border-t border-dark-200 flex justify-end gap-3">
            <a href="haber-duzenle.php?id=<?php echo $haber['id']; ?>" 
               class="bg-yellow-500/10 text-yellow-500 hover:bg-yellow-500 hover:text-black border border-yellow-500/20 font-medium px-5 py-2.5 rounded-[6px] transition-all flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Düzenle
            </a>

            <a href="haber-sil.php?id=<?php echo $haber['id']; ?>" 
               onclick="return confirm('Bu haberi silmek istediğinize emin misiniz?')"
               class="bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white border border-red-500/20 font-medium px-5 py-2.5 rounded-[6px] transition-all flex items-center gap-2">
                <i class="fa-solid fa-trash"></i> Sil
            </a>
        </div>

    </div>

</main>