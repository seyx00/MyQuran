<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user']) || !isset($_POST['sure'])) exit;

$uId = $_SESSION['user']['id'];
$sure = (int)$_POST['sure'];
$ayet = (int)$_POST['ayet'];

// Mövcud olub olmadığını yoxla
$stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND sure_no = ? AND ayet_no = ?");
$stmt->execute([$uId, $sure, $ayet]);
$fav = $stmt->fetch();

if ($fav) {
    // Varsa sil
    $del = $pdo->prepare("DELETE FROM favorites WHERE id = ?");
    $del->execute([$fav['id']]);
    echo json_encode(['status' => 'removed']);
} else {
    // Yoxdursa əlavə et
    $ins = $pdo->prepare("INSERT INTO favorites (user_id, sure_no, ayet_no) VALUES (?, ?, ?)");
    $ins->execute([$uId, $sure, $ayet]);
    echo json_encode(['status' => 'added']);
}