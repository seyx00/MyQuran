<?php
// 1. Verilənlər bazası və funksiyaların olduğu faylı daxil edirik
// getGoogleLoginUrl() funksiyası böyük ehtimalla config.php-dədir.
include 'config.php'; 
session_start();

// 2. Əgər istifadəçi onsuz da giriş edibsə, birbaşa ana səhifəyə göndər
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// 3. İndex.php-dəki həmin funksiyanı çağırıb linki alırıq
$googleUrl = getGoogleLoginUrl();

// 4. Avtomatik Google-a yönləndiririk
if ($googleUrl) {
    header("Location: " . $googleUrl);
    exit();
} else {
    // Əgər funksiya boş qayıtsa, xəta verməsin deyə index-ə qaytar
    header("Location: index.php");
    exit();
}
?>