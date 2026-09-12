<?php
include 'config.php';
session_start();

if (isset($_SESSION['user']) && isset($_POST['field']) && isset($_POST['value'])) {
    $field = $_POST['field']; // Məsələn: show_arabic
    $value = (int)$_POST['value'];
    $userId = $_SESSION['user']['id'];

    // İcazə verilən sahələri yoxlayırıq (təhlükəsizlik üçün)
    $allowed = ['show_arabic', 'show_transcription', 'show_translation'];
    
    if (in_array($field, $allowed)) {
        $stmt = $pdo->prepare("UPDATE users SET $field = ? WHERE id = ?");
        $stmt->execute([$value, $userId]);
        
        // Sessiyanı da yeniləyirik
        $_SESSION['user'][$field] = $value;
    }
}
