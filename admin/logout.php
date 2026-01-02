<?php
// Eğer session ayarların (session_start vb.) config dosyasındaysa onu dahil et.
// Değilse, standart olarak session'ı başlatmamız gerekir ki silebilelim.
session_start();

// 1. Tüm session değişkenlerini hafızadan sil
$_SESSION = array();

// 2. Eğer session cookie kullanılıyorsa, tarayıcıdaki cookie'yi de sil (Tam temizlik)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Sunucu tarafındaki oturumu tamamen yok et
session_destroy();

// 4. Kullanıcıyı Giriş Sayfasına Yönlendir
// Not: 'login.php' dosyanın adı neyse ona göre değiştirebilirsin.
header("Location: login.php?durum=logout");
exit;
?>