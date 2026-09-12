<?php
include 'config.php';
header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (mb_strlen($q, 'UTF-8') < 2) {
    echo json_encode([]);
    exit;
}

$searchTerm = "%$q%";

try {
    // Şəkildə gördüyümüz ayahs cədvəli və sütunları
    $sql = "SELECT a.surah_id as SureNo, a.ayah_number as AyetNo, 
                   a.translation as MealMetin, a.arabic_text as Arapca, 
                   a.transcription as Transcript, s.SureAdi 
            FROM ayahs a
            JOIN tbl_sureler s ON a.surah_id = s.SureNo
            WHERE (a.translation LIKE ? 
               OR a.arabic_text LIKE ? 
               OR a.transcription LIKE ?)
            ORDER BY a.surah_id ASC, a.ayah_number ASC 
            LIMIT 20";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}