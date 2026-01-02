<?php
require_once 'include/header.php';
require_once '../include/conn.php'; // Veritabanı bağlantısı

// İstatistikleri Çek
$sorgu_haber = $db->prepare("SELECT COUNT(*) FROM haberler");
$sorgu_haber->execute();
$toplam_haber = $sorgu_haber->fetchColumn();

$sorgu_kategori = $db->prepare("SELECT COUNT(*) FROM kategoriler");
$sorgu_kategori->execute();
$toplam_kategori = $sorgu_kategori->fetchColumn();

// Son Haberleri Çek
$sorgu_son_haberler = $db->prepare("
    SELECT haberler.*, kategoriler.kategori_adi 
    FROM haberler 
    LEFT JOIN kategoriler ON haberler.kategori_id = kategoriler.id 
    ORDER BY haberler.id DESC LIMIT 5
");
$sorgu_son_haberler->execute();
$son_haberler = $sorgu_son_haberler->fetchAll();
?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-gradient text-4xl font-bold">Genel Bakış</h1>
        <p class="text-light-200 mt-2">Site durumunu buradan takip edebilirsiniz.</p>
    </div>
    <a href="haber-ekle.php" class="bg-primary hover:bg-primary/90 text-black font-bold rounded-[6px] px-6 py-3 transition-all shadow-lg flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Yeni Haber Ekle
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    
    <div class="glass card-shadow p-6 flex flex-col gap-2 relative overflow-hidden group">
        <div class="absolute right-[-20px] top-[-20px] text-primary/10 text-9xl transition-all group-hover:scale-110">
            <i class="fa-regular fa-newspaper"></i>
        </div>
        <h3 class="text-light-200 text-sm font-semibold uppercase tracking-wider">Toplam Haber</h3>
        <p class="text-4xl font-bold text-white"><?php echo $toplam_haber; ?></p>
        <span class="text-xs text-primary mt-2">Yayındaki içerikler</span>
    </div>

    <div class="glass card-shadow p-6 flex flex-col gap-2 relative overflow-hidden group">
        <div class="absolute right-[-20px] top-[-20px] text-blue-400/10 text-9xl transition-all group-hover:scale-110">
            <i class="fa-solid fa-list"></i>
        </div>
        <h3 class="text-light-200 text-sm font-semibold uppercase tracking-wider">Kategoriler</h3>
        <p class="text-4xl font-bold text-white"><?php echo $toplam_kategori; ?></p>
        <span class="text-xs text-blue-400 mt-2">Aktif kategoriler</span>
    </div>

    <div class="glass card-shadow p-6 flex flex-col gap-2 relative overflow-hidden">
        <h3 class="text-light-200 text-sm font-semibold uppercase tracking-wider">Sunucu Saati</h3>
        
        <p id="canli-saat" class="text-4xl font-bold text-white">
            <?php echo date("H:i:s"); ?>
        </p>
        
        <span id="canli-tarih" class="text-xs text-light-200 mt-2">
            <?php echo date("d.m.Y"); ?>
        </span>
    </div>

</div>

<div class="glass card-shadow p-8 w-full">
    <h3 class="text-2xl font-bold text-white mb-6 border-b border-border-dark pb-4">
        Son Eklenen Haberler
    </h3>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-light-200 text-sm border-b border-border-dark">
                    <th class="py-3 px-4 font-semibold">#ID</th>
                    <th class="py-3 px-4 font-semibold">Başlık</th>
                    <th class="py-3 px-4 font-semibold">Kategori</th>
                    <th class="py-3 px-4 font-semibold">Tarih</th>
                    <th class="py-3 px-4 font-semibold">Durum</th>
                    <th class="py-3 px-4 font-semibold text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="text-light-100 text-sm">
                <?php if($sorgu_son_haberler->rowCount() > 0): ?>
                    <?php foreach($son_haberler as $haber): ?>
                    <tr class="border-b border-border-dark/50 hover:bg-dark-200/50 transition-colors group">
                        
                        <td class="py-3 px-4 text-light-200"><?php echo $haber['id']; ?></td>
                        
                        <td class="py-3 px-4 font-medium text-white">
                            <a href="haber-detay.php?id=<?php echo $haber['id']; ?>" class="hover:text-primary transition-colors flex items-center gap-2">
                                <?php echo htmlspecialchars($haber['baslik']); ?>
                            </a>
                        </td>
                        
                        <td class="py-3 px-4">
                            <span class="pill bg-dark-200 text-xs px-2 py-1"><?php echo $haber['kategori_adi'] ?? 'Genel'; ?></span>
                        </td>
                        
                        <td class="py-3 px-4 text-light-200"><?php echo date("d.m.Y", strtotime($haber['eklenme_tarihi'])); ?></td>
                        
                        <td class="py-3 px-4">
                            <?php if($haber['yayin_durumu'] == 1): ?>
                                <span class="text-primary text-xs font-bold">Yayında</span>
                            <?php else: ?>
                                <span class="text-red-400 text-xs font-bold">Pasif</span>
                            <?php endif; ?>
                        </td>

                        <td class="py-3 px-4 text-right">
                            <a href="haber-detay.php?id=<?php echo $haber['id']; ?>" class="text-gray-500 hover:text-primary transition-colors">
                                <i class="fa-solid fa-arrow-right-long"></i>
                            </a>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-light-200">Henüz hiç haber eklenmemiş.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function saatiGuncelle() {
        const simdi = new Date();
        
        const saat = simdi.toLocaleTimeString('tr-TR', { 
            hour12: false,
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
        
        const tarih = simdi.toLocaleDateString('tr-TR');

        document.getElementById('canli-saat').innerText = saat;
        document.getElementById('canli-tarih').innerText = tarih;
    }

    setInterval(saatiGuncelle, 1000);
    saatiGuncelle();
</script>