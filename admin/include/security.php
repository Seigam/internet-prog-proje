<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['oturum']) || $_SESSION['oturum'] !== true) {
    
    // Eğer giriş yapmamışsa login sayfasına yönledirme
    header("Location: login.php");
    exit;
}
?>