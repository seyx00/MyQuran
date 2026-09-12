<?php
include 'config.php';

$letter = $_GET['letter'] ?? '';
if($letter) {
    $stmt = $pdo->prepare("SELECT DISTINCT KokArapca FROM tbl_sozluk WHERE KokArapca LIKE ? ORDER BY KokArapca ASC");
    $stmt->execute([$letter . '%']);
    $roots = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($roots);
}
?>