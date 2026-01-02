<?php
require_once '../include/conn.php';

// Verileri Çek
$sorgu = $db->prepare("
    SELECT h.*, k.kategori_adi 
    FROM haberler h 
    LEFT JOIN kategoriler k ON h.kategori_id = k.id 
    ORDER BY h.id DESC
");
$sorgu->execute();
$haberler = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'include/header.php'; ?>

<div class="w-full max-w-[1800px] mx-auto p-6 md:p-10">

    <div class="flex flex-row justify-between items-center mb-8 border-b border-dark-200 pb-6">
        
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fa-regular fa-newspaper text-primary"></i>
                <span class="text-gradient">Haber Yönetimi</span>
            </h1>
            <p class="text-light-200 mt-1 text-sm hidden md:block">Sisteme eklenen haberleri buradan yönetebilirsiniz.</p>
        </div>
        
        <a href="haber-ekle.php" 
           class="bg-primary hover:bg-primary/90 text-black font-bold text-sm px-5 py-2.5 rounded-[6px] transition-all shadow-[0_0_10px_rgba(89,222,202,0.3)] hover:shadow-[0_0_20px_rgba(89,222,202,0.5)] flex items-center gap-2 whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> 
            <span class="hidden sm:inline">Yeni Haber Ekle</span>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 pb-20">
        
        <?php if (count($haberler) > 0): ?>
            <?php foreach ($haberler as $haber): ?>
                
                <a href="haber-detay.php?id=<?php echo $haber['id']; ?>" class="group block h-full">
                    
                    <div class="bg-dark-100 border border-dark-200 rounded-[10px] card-shadow overflow-hidden h-full flex flex-col relative transition-all duration-300 hover:border-primary/50 hover:-translate-y-2 hover:shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                        
                        <div class="absolute top-2 right-2 z-10">
                            <?php if ($haber['yayin_durumu'] == 1): ?>
                                <span class="bg-black/70 backdrop-blur-md text-primary border border-primary/30 text-[10px] font-bold px-2 py-1 rounded-[4px]">
                                    YAYINDA
                                </span>
                            <?php else: ?>
                                <span class="bg-black/70 backdrop-blur-md text-red-400 border border-red-400/30 text-[10px] font-bold px-2 py-1 rounded-[4px]">
                                    PASİF
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="w-full h-72 overflow-hidden relative bg-dark-200">
                            <?php if (!empty($haber['resim'])): ?>
                                <img src="../yuklemeler/<?php echo htmlspecialchars($haber['resim']); ?>" 
                                     alt="Haber" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-600 bg-dark-200">
                                    <i class="fa-regular fa-image text-4xl opacity-30 mb-2"></i>
                                    <span class="text-xs opacity-50">Görsel Yok</span>
                                </div>
                            <?php endif; ?>

                            <div class="absolute bottom-0 left-0 w-full p-3 bg-gradient-to-t from-black via-black/70 to-transparent pt-10">
                                <span class="bg-primary text-black text-[10px] font-bold px-2 py-0.5 rounded-[3px] uppercase tracking-wider shadow-lg">
                                    <?php echo htmlspecialchars($haber['kategori_adi'] ?? 'Genel'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-4 flex flex-col flex-1 bg-dark-100">
                            
                            <h3 class="text-white font-bold text-lg leading-snug mb-3 line-clamp-3 group-hover:text-primary transition-colors">
                                <?php echo htmlspecialchars($haber['baslik']); ?>
                            </h3>

                            <div class="mt-auto pt-3 border-t border-dark-200 flex justify-between items-center text-xs text-gray-500">
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-primary"></i>
                                    <span><?php echo date("d.m.Y", strtotime($haber['eklenme_tarihi'])); ?></span>
                                </div>
                                <i class="fa-solid fa-arrow-right opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 text-primary"></i>
                            </div>
                        </div>

                    </div>
                </a>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="col-span-full py-20 text-center bg-dark-100 border border-dark-200 border-dashed rounded-[10px]">
                <div class="w-16 h-16 bg-dark-200 rounded-full flex items-center justify-center mx-auto mb-4 text-light-200">
                    <i class="fa-regular fa-folder-open text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white">Henüz Haber Yok</h3>
                <p class="text-gray-500 mt-2 text-sm">Sağ üstteki butonu kullanarak yeni haber ekleyebilirsiniz.</p>
            </div>
        <?php endif; ?>

    </div>

</div>