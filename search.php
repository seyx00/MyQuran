<?php
include 'config.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchTerm = "%$q%";

// Burada da eyni məntiqlə 101 və 102-ci məallar əsas götürülür
$sql = "SELECT m.*, s.SureAdi 
        FROM tbl_meal m
        JOIN tbl_sureler s ON m.SureNo = s.SureNo
        WHERE (
            (m.MealNo IN (101, 102) AND m.MealMetin LIKE ?) 
            OR m.Arapca LIKE ? 
            OR m.TranscriptTurkce LIKE ?
        )
        ORDER BY m.SureNo ASC, m.AyetNo ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Səhifədə nəticələri göstərmək üçün dövr (foreach) aşağıda olacaq