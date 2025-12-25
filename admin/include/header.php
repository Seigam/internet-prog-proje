<?php require_once 'include/security.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Haber Paneli</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-background text-foreground flex h-screen overflow-hidden">

    <?php include 'include/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto relative">
        
        <header class="glass sticky top-0 z-50 px-8 py-4 flex justify-between items-center border-b border-border-dark">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-bold font-schibsted-grotesk text-white">
                    Panel
                </h2>
            </div>
            
            <div class="flex items-center gap-6">
                <span class="text-light-200 text-sm">
                    Hoşgeldin, <strong class="text-primary"><?php echo $_SESSION['ad_soyad'] ?? 'Yönetici'; ?></strong>
                </span>
                <a href="logout.php" class="text-red-400 hover:text-red-300 text-sm font-semibold transition-all">
                    <i class="fa-solid fa-right-from-bracket"></i> Çıkış
                </a>
            </div>
        </header>

        <div class="p-8">