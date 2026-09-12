<?php
include 'config.php';
session_start();

// 1. Parametrləri alırıq
$sureNo = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$mealNo = isset($_GET['meal']) ? (int)$_GET['meal'] : 1; 

// 2. Standart Görünüş Ayarları
$showArabic = 1;
$showTrans  = 1;
$showMushaf = 1;
$showMeal   = 1;

// 3. Giriş edibsə ayarları bazadan götür
if (isset($_SESSION['user'])) {
    $uId = $_SESSION['user']['id'];
    $uStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $uStmt->execute([$uId]);
    $userPrefs = $uStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userPrefs) {
        $showArabic = isset($userPrefs['show_arabic']) ? $userPrefs['show_arabic'] : 1;
        $showTrans  = isset($userPrefs['show_transcription']) ? $userPrefs['show_transcription'] : 1;
        $showMeal   = isset($userPrefs['show_translation']) ? $userPrefs['show_translation'] : 1;
        $showMushaf = isset($userPrefs['show_mushaf']) ? $userPrefs['show_mushaf'] : 1;
    }
}

/**
 * TƏKMİLLƏŞDİRİLMİŞ NÖQTƏSİZLƏŞDİRMƏ FUNKSİYASI
 */
function noqtesizEle($metin) {
    if (empty($metin)) return "";
    $metin = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06ED}]/u', '', $metin);
    $kelimeler = explode(' ', $metin);
    $yeniMetin = [];
    foreach ($kelimeler as $kelime) {
        $harfler = preg_split('//u', $kelime, -1, PREG_SPLIT_NO_EMPTY);
        $uzunluq = count($harfler);
        $yeniKelime = "";
        foreach ($harfler as $index => $harf) {
            $yer = ($uzunluq == 1) ? 'tek' : (($index == 0) ? 'bash' : (($index == $uzunluq - 1) ? 'son' : 'orta'));
            if (in_array($harf, ['ي', 'ن', 'ت', 'ث', 'ب', 'ى', 'ی'])) {
                if ($yer == 'bash' || $yer == 'orta') $harf = '‍ٮ‍'; 
                elseif ($yer == 'son') $harf = (in_array($harf, ['ب', 'ت', 'ث'])) ? 'ٮ' : 'ں';
            }
            elseif (in_array($harf, ['ف', 'ق'])) {
                $harf = ($yer == 'bash' || $yer == 'orta') ? '‍ڡ‍' : 'ڡ';
            } else {
                $mapping = ['ج'=>'ح', 'خ'=>'ح', 'ذ'=>'د', 'ز'=>'ر', 'ش'=>'س', 'ض'=>'ص', 'ظ'=>'ط', 'غ'=>'ع', 'ة'=>'ه', 'ئ'=>'ى', 'ؤ'=>'و'];
                if (isset($mapping[$harf])) $harf = $mapping[$harf];
            }
            $yeniKelime .= $harf;
        }
        $yeniMetin[] = $yeniKelime;
    }
    return implode(' ', $yeniMetin);
}

// Surə məlumatları
$sStmt = $pdo->prepare("SELECT SureAdi FROM tbl_sureler WHERE SureNo = ?");
$sStmt->execute([$sureNo]);
$sure = $sStmt->fetch();
if (!$sure) { die("Surə tapılmadı."); }

// Aktiv Məal
$mStmt = $pdo->prepare("SELECT mealNo, mealAdi FROM tbl_meal WHERE mealNo = ? AND Aktif = 0");
$mStmt->execute([$mealNo]);
$activeMeal = $mStmt->fetch();

if (!$activeMeal) {
    $defaultM = $pdo->query("SELECT mealNo, mealAdi FROM tbl_meal WHERE Aktif = 0 ORDER BY mealNo ASC LIMIT 1")->fetch();
    $mealNo = $defaultM['mealNo'];
    $activeMeal = $defaultM;
}

$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sure['SureAdi'] ?> surəsi - MyQuran</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @font-face { font-family: 'MushafFontu'; src: url('fonts/MushafFontu-Regular.ttf') format('truetype'); font-display: swap; }
        :root { --primary-green: #27ae60; --bg-color: #ffffff; --text-main: #333333; --text-gray: #666; --border-color: #f0f0f0; --card-hover: #fcfcfc; --nav-bg: #ffffff; --nav-btn-bg: #f8f9fa; --modal-bg: #fff; }
        body.dark { --bg-color: #121212; --text-main: #e0e0e0; --text-gray: #aaa; --border-color: #222; --card-hover: #1a1a1a; --nav-bg: #1a1a1a; --nav-btn-bg: #222; --modal-bg: #1e1e1e; }
        body { font-family: 'Roboto', sans-serif; background: var(--bg-color); color: var(--text-main); margin: 0; padding-bottom: 90px; transition: background 0.3s, color 0.3s; }
        
        .top-nav { position: sticky; top: 0; background: var(--nav-bg); padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000; }
        .top-nav a { color: var(--text-main); text-decoration: none; }
        
        .nav-right-icons { display: flex; align-items: center; gap: 20px; }

        /* Share Dropdown Styles */
        .share-container { position: relative; display: inline-block; }
        .share-btn { background: none; border: none; color: #888; font-size: 1.2rem; cursor: pointer; padding: 5px; }
        .share-dropdown { display: none; position: absolute; right: 0; top: 40px; background-color: var(--modal-bg); min-width: 200px; box-shadow: 0px 8px 16px rgba(0,0,0,0.2); border: 1px solid var(--border-color); border-radius: 8px; z-index: 1001; }
        .share-dropdown a { color: var(--text-main); padding: 12px 16px; text-decoration: none; display: block; font-size: 0.9rem; border-bottom: 1px solid var(--border-color); text-align: left; }
        .share-dropdown a:last-child { border-bottom: none; }
        .share-dropdown a:hover { background-color: var(--card-hover); }
        .share-dropdown i { margin-right: 10px; width: 20px; text-align: center; }

        .container { max-width: 850px; margin: 0 auto; padding: 20px; }
        .sure-header { text-align: center; padding: 30px 0; border-bottom: 1px solid var(--border-color); margin-bottom: 20px; }
        .arabic-title { font-family: 'Amiri', serif; font-size: 1.8rem; color: var(--primary-green); }
        .author-badge { display: inline-flex; align-items: center; gap: 8px; background: var(--nav-btn-bg); padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; color: var(--text-gray); cursor: pointer; border: 1px solid var(--border-color); margin-top: 15px; }
        .aye-wrapper { padding: 35px 15px; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: 0.2s; }
        .aye-wrapper:hover { background-color: var(--card-hover); }
        .aye-no { color: #888; font-size: 0.9rem; margin-bottom: 12px; display: block; font-weight: bold; }
        .meal-text { font-size: 1.25rem; line-height: 1.7; color: var(--text-main); margin-bottom: 18px; }
        .arabic-text { font-family: 'Amiri', serif; font-size: 2.5rem; text-align: right; direction: rtl; line-height: 1.8; margin-bottom: 5px; color: var(--text-main); }
        .mushaf-text { font-family: 'MushafFontu', serif; font-size: 2rem; text-align: right; direction: rtl; color: var(--text-gray); margin-bottom: 15px; line-height: 1.6; }
        .transcription { color: var(--primary-green); font-size: 1.15rem; font-weight: 500; }
        
        .fixed-nav { position: fixed; bottom: 0; left: 0; right: 0; background: var(--nav-bg); padding: 12px 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.2); z-index: 10000; display: flex; justify-content: center; }
        .nav-container { width: 90%; max-width: 600px; display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        .nav-btn { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: var(--nav-btn-bg); border-radius: 12px; color: var(--text-main); text-decoration: none; border: 1px solid var(--border-color); }
        .nav-btn:hover { background: var(--primary-green); color: white; }
        .nav-select-wrapper { flex: 1; position: relative; }
        .nav-select-wrapper select { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--nav-btn-bg); color: var(--text-main); appearance: none; text-align: center; }
        
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; }
        .modal-content { background: var(--modal-bg); color: var(--text-main); width: 90%; max-width: 400px; border-radius: 15px; padding: 25px; }
        .setting-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding: 5px 0; }
        .meal-option { padding: 12px; border-bottom: 1px solid var(--border-color); cursor: pointer; }
        .meal-option:hover { color: var(--primary-green); }
    </style>
</head>
<body id="pageBody">

<script>
    if (localStorage.getItem('theme') === 'dark') { document.getElementById('pageBody').classList.add('dark'); }
</script>

<div class="top-nav">
    <a href="index.php"><i class="fas fa-chevron-left"></i></a>
    <div style="font-weight: 500;"><?= $sureNo ?>. <?= $sure['SureAdi'] ?></div>
    
    <div class="nav-right-icons">
        <div class="share-container">
            <button onclick="toggleShareMenu(event)" class="share-btn">
                <i class="fas fa-share-alt"></i>
            </button>
            <div id="shareMenu" class="share-dropdown">
                <a href="javascript:void(0)" onclick="copyCurrentLink()">
                    <i class="far fa-copy"></i> Bağlantını kopyala
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($currentUrl) ?>" target="_blank">
                    <i class="fab fa-twitter"></i> Twitter'da paylaş
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($currentUrl) ?>" target="_blank">
                    <i class="fab fa-facebook"></i> Facebook'ta paylaş
                </a>
            </div>
        </div>
        <i class="fas fa-cog" style="cursor:pointer; color: #888;" onclick="handleSettingsClick()"></i>
    </div>
</div>

<div class="container">
    <div class="sure-header">
        <div class="arabic-title">سورة <?= $sure['SureAdi'] ?></div>
        <h1 style="margin: 5px 0;"><?= $sure['SureAdi'] ?> surəsi</h1>
        <div class="author-badge" onclick="toggleSettings()">
            <i class="fas fa-book"></i> <?= $activeMeal['mealAdi'] ?> <i class="fas fa-chevron-down"></i>
        </div>
    </div>

    <?php
    $aStmt = $pdo->prepare("
        SELECT k.AyetNo, k.Metin, a.mushaf_ayah 
        FROM tbl_kayit k
        LEFT JOIN ayahs a ON (k.SureNo = a.surah_id AND k.AyetNo = a.ayah_number)
        WHERE k.SureNo = ? AND k.MealAdi = ?
        ORDER BY k.AyetNo ASC
    ");
    $aStmt->execute([$sureNo, $activeMeal['mealAdi']]);
    
    while ($aye = $aStmt->fetch()) {
        $sQuery = $pdo->prepare("SELECT Arapca, TranscriptTurkce FROM tbl_sozluk WHERE SureNo = ? AND AyetNo = ? ORDER BY KelimeNo ASC");
        $sQuery->execute([$sureNo, $aye['AyetNo']]);
        $words = $sQuery->fetchAll();

        $fullArabic = ""; $fullTrans = "";
        foreach($words as $w) {
            $fullArabic .= $w['Arapca'] . " ";
            $fullTrans .= $w['TranscriptTurkce'] . " ";
        }
        ?>

        <div class="aye-wrapper" onclick="window.location.href='verse.php?sure=<?= $sureNo ?>&ayet=<?= $aye['AyetNo'] ?>&meal=<?= $mealNo ?>'">
            <span class="aye-no"><?= $aye['AyetNo'] ?>.</span>
            
            <?php if($showMeal): ?>
                <div class="meal-text"><?= $aye['Metin'] ?></div>
            <?php endif; ?>
            
            <?php if($showArabic): ?>
                <div class="arabic-text"><?= trim($fullArabic) ?></div>
            <?php endif; ?>
            
            <?php if($showMushaf): ?>
                <div class="mushaf-text"><?= noqtesizEle($aye['mushaf_ayah'] ?? '') ?></div>
            <?php endif; ?>
            
            <?php if($showTrans): ?>
                <div class="transcription"><?= mb_strtolower(trim($fullTrans), 'UTF-8') ?>.</div>
            <?php endif; ?>
        </div>
    <?php } ?>
</div>

<div class="fixed-nav">
    <div class="nav-container">
        <a href="index.php" class="nav-btn"><i class="fas fa-home"></i></a>
        <a href="surah.php?id=<?= ($sureNo > 1) ? $sureNo - 1 : 114 ?>&meal=<?= $mealNo ?>" class="nav-btn"><i class="fas fa-chevron-left"></i></a>
        <div class="nav-select-wrapper">
            <select onchange="location.href='surah.php?id=' + this.value + '&meal=<?= $mealNo ?>'">
                <?php
                $sList = $pdo->query("SELECT SureNo, SureAdi FROM tbl_sureler ORDER BY CAST(SureNo AS UNSIGNED) ASC");
                while($s = $sList->fetch()) {
                    $selected = ($s['SureNo'] == $sureNo) ? 'selected' : '';
                    echo "<option value='{$s['SureNo']}' {$selected}>{$s['SureNo']}. {$s['SureAdi']}</option>";
                }
                ?>
            </select>
        </div>
        <a href="surah.php?id=<?= ($sureNo < 114) ? $sureNo + 1 : 1 ?>&meal=<?= $mealNo ?>" class="nav-btn"><i class="fas fa-chevron-right"></i></a>
    </div>
</div>

<div id="settingsModal" class="modal">
    <div class="modal-content">
        <h3 style="margin-top:0;">Görünüş Ayarları</h3>
        <a href="favorites_list.php" style="display: block; width: 100%; padding: 10px; background: var(--nav-btn-bg); color: var(--text-main); text-decoration: none; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid var(--border-color);">
           <i class="fas fa-heart" style="color: #e74c3c;"></i> Favorilərim
        </a>
        <div class="setting-row"><span>Məal</span><input type="checkbox" <?= $showMeal ? 'checked' : '' ?> onchange="updateSetting('show_translation', this.checked)"></div>
        <div class="setting-row"><span>Ərəbcə</span><input type="checkbox" <?= $showArabic ? 'checked' : '' ?> onchange="updateSetting('show_arabic', this.checked)"></div>
        <div class="setting-row"><span>Müshaf (Nöqtəsiz)</span><input type="checkbox" <?= $showMushaf ? 'checked' : '' ?> onchange="updateSetting('show_mushaf', this.checked)"></div>
        <div class="setting-row"><span>Transkripsiya</span><input type="checkbox" <?= $showTrans ? 'checked' : '' ?> onchange="updateSetting('show_transcription', this.checked)"></div>
        
        <hr style="border:0; border-top:1px solid var(--border-color); margin: 15px 0;">
        <h4 style="margin-bottom:10px;">Məal müəllifi</h4>
        <div style="max-height: 200px; overflow-y: auto;">
            <?php
            $mList = $pdo->query("SELECT mealNo, mealAdi FROM tbl_meal WHERE Aktif = 0 ORDER BY mealNo ASC");
            while($m = $mList->fetch()) {
                $style = ($m['mealNo'] == $mealNo) ? 'color:var(--primary-green); font-weight:bold;' : '';
                echo "<div class='meal-option' style='{$style}' onclick='changeMeal(".$m['mealNo'].")'>".$m['mealAdi']."</div>";
            }
            ?>
        </div>
        <button onclick="toggleSettings()" style="width:100%; margin-top:20px; padding:12px; border:none; background:var(--primary-green); color:white; border-radius:8px; cursor:pointer;">Bağla</button>
    </div>
</div>

<script>
const isLoggedIn = <?= isset($_SESSION['user']) ? 'true' : 'false' ?>;

function toggleShareMenu(event) {
    event.stopPropagation();
    var menu = document.getElementById("shareMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

function copyCurrentLink() {
    var dummy = document.createElement('input'),
    text = window.location.href;
    document.body.appendChild(dummy);
    dummy.value = text;
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);
    alert("Bağlantı kopyalandı!");
}

function handleSettingsClick() {
    if (!isLoggedIn) { window.location.href = "login.php"; return; }
    toggleSettings();
}

function toggleSettings() {
    const modal = document.getElementById('settingsModal');
    modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex';
}

function updateSetting(field, value) {
    fetch('update_settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `field=${field}&value=${value?1:0}`
    }).then(() => location.reload());
}

function changeMeal(mId) {
    location.href = 'surah.php?id=<?= $sureNo ?>&meal=' + mId;
}

window.onclick = function(e) { 
    if (e.target.className === 'modal') {
        toggleSettings();
    }
    if (!e.target.closest('.share-container')) {
        document.getElementById("shareMenu").style.display = "none";
    }
}
</script>

</body>
</html>