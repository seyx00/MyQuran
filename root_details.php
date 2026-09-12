<?php
include 'config.php';

$kok = isset($_GET['kok']) ? $_GET['kok'] : '';

if (empty($kok)) { die("Kök seçilməyib."); }

// --- JSON-DAN KÖK MƏNASINI ÇƏKMƏK ---
$kokManasi = "";
$jsonFile = 'root_extraction.json';
if (file_exists($jsonFile)) {
    $jsonData = file_get_contents($jsonFile);
    $rootsArray = json_decode($jsonData, true);
    $temizKok = str_replace(' ', '', $kok);
    if ($rootsArray) {
        foreach ($rootsArray as $rootItem) {
            if (isset($rootItem['arabic']) && $rootItem['arabic'] == $temizKok) {
                $kokManasi = $rootItem['mean'];
                break;
            }
        }
    }
}

// --- NAVİQASİYA ÜÇÜN MƏLUMATLARIN HAZIRLANMASI ---
$allRootsStmt = $pdo->query("SELECT DISTINCT KokArapca FROM tbl_sozluk WHERE KokArapca IS NOT NULL AND KokArapca != '' ORDER BY KokArapca ASC");
$allRoots = $allRootsStmt->fetchAll(PDO::FETCH_COLUMN);

$rootsByLetter = [];
foreach ($allRoots as $r) {
    $firstChar = mb_substr(trim($r), 0, 1, 'UTF-8');
    $rootsByLetter[$firstChar][] = $r;
}
$currentFirstChar = mb_substr(trim($kok), 0, 1, 'UTF-8');

$transMap = [
    'ا' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 's', 'ج' => 'c', 'ح' => 'h', 'خ' => 'x',
    'د' => 'd', 'ذ' => 'z', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'ş', 'ص' => 's',
    'ض' => 'd', 'ط' => 't', 'ظ' => 'z', 'ع' => 'e', 'غ' => 'g', 'ف' => 'f', 'ق' => 'q',
    'ك' => 'k', 'ل' => 'l', 'm' => 'm', 'ن' => 'n', 'و' => 'v', 'ه' => 'h', 'ي' => 'y',
    'ء' => 'e', 'ى' => 'y', 'و' => 'v'
];

function getTrans($text, $map) {
    $res = ""; $chars = mb_str_split($text);
    foreach($chars as $char) {
        if(trim($char) == "") { $res .= "-"; } 
        else { $res .= isset($map[$char]) ? $map[$char] : ""; }
    }
    return trim($res, "-");
}
$kokLatinca = getTrans($kok, $transMap);

// --- SQL SORĞUSU: YENİ SÜTUN ADLARI İLƏ ---
try {
    $sql = "SELECT s.*, sur.SureAdi, g.IrabTR, g.IrabAB
            FROM tbl_sozluk s
            LEFT JOIN tbl_sureler sur ON s.SureNo = sur.SureNo
            LEFT JOIN gramer g ON (s.SureNo = g.SureNo AND s.AyetNo = g.AyetNo AND s.KelimeNo = g.KelimeNo)
            WHERE s.KokArapca = ? 
            ORDER BY s.SureNo ASC, s.AyetNo ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$kok]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Xəta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kök Təhlili: <?= htmlspecialchars($kok) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary-green: #27ae60; --bg-color: #ffffff; --text-main: #333; 
            --text-muted: #888; --border-color: #eee; --header-bg: #ffffff;
            --nav-bg: #1a1a1a; --card-hover: #f9f9f9;
        }
        body.dark {
            --bg-color: #121212; --text-main: #e0e0e0; --text-muted: #aaa;
            --border-color: #2c2c2c; --header-bg: #1a1a1a; --card-hover: #1e1e1e;
        }
        body { font-family: 'Roboto', sans-serif; margin: 0; background: var(--bg-color); color: var(--text-main); transition: 0.3s; padding-bottom: 70px; }
        .header { position: sticky; top: 0; background: var(--header-bg); padding: 15px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 15px; z-index: 1000; }
        .container { max-width: 800px; margin: 20px auto; padding: 0 15px; }
        .root-title { font-size: 2.2rem; color: var(--primary-green); font-family: 'Amiri', serif; direction: rtl; }
        .dictionary-box { margin-top: 20px; padding: 15px; background: var(--card-hover); border-radius: 12px; border-left: 4px solid var(--primary-green); line-height: 1.6; font-size: 0.95rem; }
        .result-item { padding: 20px 15px; border-bottom: 1px solid var(--border-color); text-decoration: none; display: block; color: inherit; }
        .result-item:hover { background: var(--card-hover); }
        .word-display { display: flex; align-items: center; justify-content: space-between; flex-direction: row-reverse; }
        .arabic-word { font-family: 'Amiri', serif; font-size: 1.8rem; direction: rtl; }
        .trans-word { color: var(--primary-green); font-weight: 500; font-size: 1.1rem; display: block; }
        
        /* Qrammatika Meta Stilləri */
        .gramer-meta { margin-top: 8px; font-size: 0.8rem; display: flex; flex-direction: column; gap: 2px; }
        .irab-tr-tag { color: var(--primary-green); font-weight: 500; }
        .irab-ab-tag { font-family: 'Amiri', serif; font-size: 1rem; opacity: 0.8; direction: rtl; color: var(--text-muted); }

        .independent-nav { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--nav-bg); padding: 10px 15px; display: flex; gap: 10px; border-top: 1px solid #2c2c2c; z-index: 9999; box-sizing: border-box; }
        .nav-select { flex: 1; height: 40px; background: #2a2a2a; color: #fff; border: 1px solid #444; border-radius: 8px; padding: 0 8px; font-size: 0.9rem; }
        .home-link { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #2a2a2a; border: 1px solid #444; border-radius: 8px; color: #fff; text-decoration: none; }
    </style>
</head>
<body id="pageBody">

<script>if (localStorage.getItem('theme') === 'dark') { document.body.classList.add('dark'); }</script>

<div class="header">
    <a href="javascript:history.back()" style="color:var(--text-main); font-size: 1.2rem;"><i class="fas fa-arrow-left"></i></a>
    <h3 style="margin:0;">Kök Təhlili</h3>
</div>

<div class="container">
    <div class="root-info" style="margin-bottom: 30px; border-bottom: 2px solid var(--border-color); padding-bottom: 15px;">
        <div style="display: flex; align-items: baseline; gap: 15px;">
            <div class="root-title"><?= str_replace(' ', '', $kok) ?></div>
            <div style="color: var(--text-muted); font-style: italic;">(<?= $kokLatinca ?>)</div>
        </div>
        <?php if ($kokManasi): ?>
            <div class="dictionary-box">
                <span style="font-weight:bold; color:var(--primary-green); font-size:0.85rem;">KÖKÜN LÜĞƏT MƏNASI (MÜFRƏDAT)</span><br>
                <?= nl2br(htmlspecialchars($kokManasi)) ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($results): ?>
        <?php foreach ($results as $row): ?>
            <a href="verse.php?sure=<?= $row['SureNo'] ?>&ayet=<?= $row['AyetNo'] ?>" class="result-item">
                <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:10px;">
                    <i class="fas fa-book-quran"></i> <?= htmlspecialchars($row['SureAdi']) ?> / <?= $row['SureNo'] ?>:<?= $row['AyetNo'] ?>
                </div>
                <div class="word-display">
                    <div class="arabic-word"><?= $row['Arapca'] ?></div>
                    <div style="text-align: left;">
                        <span class="trans-word"><?= mb_strtolower($row['TranscriptTurkce'], 'UTF-8') ?></span>
                        <span style="font-size:0.9rem; color:var(--text-muted);"><?= $row['Turkce'] ?></span>
                        
                        <?php if(!empty($row['IrabTR']) || !empty($row['IrabAB'])): ?>
                        <div class="gramer-meta">
                            <?php if(!empty($row['IrabTR'])): ?>
                                <span class="irab-tr-tag"><?= htmlspecialchars($row['IrabTR']) ?></span>
                            <?php endif; ?>
                            <?php if(!empty($row['IrabAB'])): ?>
                                <span class="irab-ab-tag"><?= htmlspecialchars($row['IrabAB']) ?></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="independent-nav">
    <a href="index.php" class="home-link"><i class="fas fa-home"></i></a>
    
    <select class="nav-select" id="letterSelect" onchange="updateRoots(this.value)">
        <?php foreach (array_keys($rootsByLetter) as $letter): ?>
            <option value="<?= $letter ?>" <?= ($letter == $currentFirstChar) ? 'selected' : '' ?>><?= $letter ?></option>
        <?php endforeach; ?>
    </select>

    <select class="nav-select" id="rootSelect" onchange="location.href='root_details.php?kok=' + encodeURIComponent(this.value)">
        <?php 
        $currentLetterRoots = $rootsByLetter[$currentFirstChar] ?? [];
        foreach ($currentLetterRoots as $r): 
        ?>
            <option value="<?= $r ?>" <?= ($r == $kok) ? 'selected' : '' ?>>
                <?= $r ?> (<?= getTrans($r, $transMap) ?>)
            </option>
        <?php endforeach; ?>
    </select>
</div>

<script>
const rootsData = <?= json_encode($rootsByLetter, JSON_UNESCAPED_UNICODE) ?>;
const transMapJS = <?= json_encode($transMap, JSON_UNESCAPED_UNICODE) ?>;

function getTransJS(text) {
    let res = "";
    for (let char of text) {
        if (char.trim() === "") res += "-";
        else res += transMapJS[char] || "";
    }
    return res.replace(/^-+|-+$/g, '');
}

function updateRoots(letter) {
    const rootSelect = document.getElementById('rootSelect');
    rootSelect.innerHTML = "";
    if (rootsData[letter]) {
        rootsData[letter].forEach(r => {
            const opt = document.createElement('option');
            opt.value = r;
            opt.textContent = r + " (" + getTransJS(r) + ")";
            rootSelect.appendChild(opt);
        });
    }
}
</script>

</body>
</html>