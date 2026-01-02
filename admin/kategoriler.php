<?php
// 1. Veritabanı bağlantısı (Ana dizine çık -> include -> conn.php)
require_once '../include/conn.php';

// --- SİLME İŞLEMİ ---
if (isset($_GET['sil_id'])) {
    $sil_id = intval($_GET['sil_id']);
    
    // Veritabanından sil
    $sil_sorgu = $db->prepare("DELETE FROM kategoriler WHERE id = :id");
    $sil = $sil_sorgu->execute([':id' => $sil_id]);

    if ($sil) {
        header("Location: kategoriler.php?durum=ok");
        exit;
    } else {
        header("Location: kategoriler.php?durum=no");
        exit;
    }
}
?>

<?php include 'include/header.php'; ?>

<main class="w-full max-w-6xl mx-auto mt-10 px-4 pb-20 min-h-[80vh]">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fa fa-list text-primary"></i> 
                <span class="text-gradient">Kategori Listesi</span>
            </h2>
            <p class="text-light-200 text-sm mt-1">Ekli olan tüm kategorileri buradan yönetebilirsiniz.</p>
        </div>

        <a href="kategori-ekle.php" 
           class="group flex items-center gap-2 bg-primary text-black font-semibold px-6 py-3 rounded-[6px] hover:bg-primary/90 transition-all shadow-[0_0_15px_rgba(89,222,202,0.3)] hover:shadow-[0_0_25px_rgba(89,222,202,0.5)]">
            <i class="fa fa-plus-circle transition-transform group-hover:rotate-90"></i>
            Yeni Kategori Ekle
        </a>
    </div>

    <?php if (isset($_GET['durum']) && $_GET['durum'] == 'ok') { ?>
        <div class="bg-primary/10 border border-primary/20 text-primary p-4 rounded-[6px] mb-6 text-sm flex items-center gap-3 shadow-[0_0_15px_rgba(89,222,202,0.1)]">
            <i class="fa fa-check-circle text-lg"></i> İşlem başarıyla gerçekleştirildi.
        </div>
    <?php } elseif (isset($_GET['durum']) && $_GET['durum'] == 'no') { ?>
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-[6px] mb-6 text-sm flex items-center gap-3">
            <i class="fa fa-times-circle text-lg"></i> İşlem sırasında bir hata oluştu!
        </div>
    <?php } ?>

    <div class="bg-dark-100 border border-dark-200 rounded-[10px] card-shadow overflow-hidden relative">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-dark-200 text-light-100 text-sm uppercase tracking-wider border-b border-dark-200">
                        <th class="p-4 font-medium text-center w-24">#ID</th>
                        <th class="p-4 font-medium">Kategori Adı</th>
                        <th class="p-4 font-medium text-center w-32">Sıra</th>
                        <th class="p-4 font-medium text-center w-48">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="text-light-200 divide-y divide-dark-200/50">
                    <?php
                    // Verileri Çekme
                    $sorgu = $db->prepare("SELECT * FROM kategoriler ORDER BY sira ASC, id DESC");
                    $sorgu->execute();
                    $kategoriler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

                    if ($sorgu->rowCount() > 0) {
                        foreach ($kategoriler as $kategori) {
                            ?>
                            <tr class="hover:bg-dark-200/50 transition-colors">
                                <td class="p-4 text-center font-bold text-primary">
                                    #<?php echo $kategori['id']; ?>
                                </td>
                                
                                <td class="p-4 font-medium text-white">
                                    <?php echo htmlspecialchars($kategori['kategori_adi']); ?>
                                </td>
                                
                                <td class="p-4 text-center">
                                    <span class="inline-block bg-dark-200 text-light-200 text-xs px-3 py-1 rounded-[6px] border border-dark-200">
                                        <?php echo $kategori['sira']; ?>
                                    </span>
                                </td>
                                
                                <td class="p-4 text-center">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="kategori-duzenle.php?id=<?php echo $kategori['id']; ?>" 
                                           class="w-8 h-8 flex items-center justify-center bg-yellow-500/10 text-yellow-500 rounded-[6px] border border-yellow-500/20 hover:bg-yellow-500 hover:text-black transition-all"
                                           title="Düzenle">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        
                                        <a href="kategoriler.php?sil_id=<?php echo $kategori['id']; ?>" 
                                           class="w-8 h-8 flex items-center justify-center bg-red-500/10 text-red-500 rounded-[6px] border border-red-500/20 hover:bg-red-500 hover:text-white transition-all"
                                           onclick="return confirm('<?php echo htmlspecialchars($kategori['kategori_adi']); ?> kategorisini silmek istediğinize emin misiniz?')"
                                           title="Sil">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        // Kayıt Yoksa
                        echo '<tr><td colspan="4" class="text-center p-8 text-light-200 italic bg-dark-100/50">Henüz veritabanında kayıtlı kategori bulunmuyor.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>