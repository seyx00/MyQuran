<?php
include 'config.php';
session_start();

// 1. Parametrləri alırıq
$sureNo = isset($_GET['sure']) ? (int)$_GET['sure'] : 1;
$ayetNo = isset($_GET['ayet']) ? (int)$_GET['ayet'] : 1;
$mealNo = isset($_GET['meal']) ? (int)$_GET['meal'] : 1; 

// 2. Standart Ayarlar
$showArabic = 1;
$showTrans  = 1;
$showMushaf = 1;
$showMeal   = 1; 
$isFav = false; 

// 3. Giriş edibsə ayarları və favori vəziyyətini bazadan götür
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

    $fCheck = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND sure_no = ? AND ayet_no = ?");
    $fCheck->execute([$uId, $sureNo, $ayetNo]);
    if ($fCheck->fetch()) $isFav = true;
}

/**
 * TƏKMİLLƏŞDİRİLMİŞ NÖQTƏSİZLƏŞDİRMƏ
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

// Məal yazarı
$mStmt = $pdo->prepare("SELECT mealAdi FROM tbl_meal WHERE mealNo = ? AND Aktif = 0");
$mStmt->execute([$mealNo]);
$activeMealData = $mStmt->fetch();

// Maksimum ayə sayı
$maxAyetStmt = $pdo->prepare("SELECT MAX(AyetNo) as topsay FROM tbl_kayit WHERE SureNo = ?");
$maxAyetStmt->execute([$sureNo]);
$resAyet = $maxAyetStmt->fetch();
$maxAyet = $resAyet['topsay'] ? (int)$resAyet['topsay'] : 0;

// Əsas məlumatlar
$mainMealStmt = $pdo->prepare("
    SELECT k.Metin, a.mushaf_ayah 
    FROM tbl_kayit k
    LEFT JOIN ayahs a ON (k.SureNo = a.surah_id AND k.AyetNo = a.ayah_number)
    WHERE k.SureNo = ? AND k.AyetNo = ? AND k.MealAdi = ?
    LIMIT 1
");
$mainMealStmt->execute([$sureNo, $ayetNo, $activeMealData['mealAdi']]);
$mainMeal = $mainMealStmt->fetch();

// Dipnotu yoxlayırıq
$dStmt = $pdo->prepare("SELECT DipnotMetin FROM tbl_dipnot WHERE SureNo = ? AND AyetNo = ? AND MealNo = ? LIMIT 1");
$dStmt->execute([$sureNo, $ayetNo, $mealNo]);
$dipnot = $dStmt->fetch();

// Kəlimələr
$kStmt = $pdo->prepare("SELECT KelimeNo, Arapca, TranscriptTurkce, Turkce, KokArapca FROM tbl_sozluk WHERE SureNo = ? AND AyetNo = ? ORDER BY KelimeNo ASC");
$kStmt->execute([$sureNo, $ayetNo]);
$kelimeler = $kStmt->fetchAll();

$fullArabic = ""; $fullTrans = "";
foreach($kelimeler as $k) {
    $fullArabic .= $k['Arapca'] . " ";
    $fullTrans .= $k['TranscriptTurkce'] . " ";
}

$currentUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sureNo ?>:<?= $ayetNo ?> - MyQuran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        @font-face { font-family: 'MushafFontu'; src: url('fonts/MushafFontu-Regular.ttf') format('truetype'); font-display: swap; }
        
        :root { 
            --primary-green: #27ae60; --bg-color: #ffffff; --text-main: #333; 
            --text-muted: #888; --border-color: #eee; --header-bg: #ffffff; 
            --modal-bg: #fff; --nav-bg: #222; --card-hover: #f9f9f9;
        }
        
        body.dark { 
            --bg-color: #121212; --text-main: #e0e0e0; --text-muted: #aaa; 
            --border-color: #2c2c2c; --header-bg: #1a1a1a; --modal-bg: #1e1e1e; 
            --nav-bg: #000; --card-hover: #252525;
        }

        body { font-family: 'Roboto', sans-serif; margin: 0; background: var(--bg-color); color: var(--text-main); padding-bottom: 80px; transition: background 0.3s, color 0.3s; }
        
        .header { position: sticky; top: 0; background: var(--header-bg); padding: 10px 15px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; z-index: 1000; }
        .header-center { display: flex; align-items: center; gap: 10px; flex: 1; justify-content: center; }
        .meal-select-header { border: 1px solid var(--border-color); padding: 5px 10px; border-radius: 20px; font-size: 0.85rem; color: var(--text-muted); background: var(--bg-color); cursor: pointer; }
        .header-right { display: flex; align-items: center; gap: 15px; }
        .fav-icon { font-size: 1.2rem; cursor: pointer; transition: 0.2s; color: var(--text-muted); }
        .fav-icon.active { color: #e74c3c; }

        /* Share Dropdown Styles */
        .share-container { position: relative; display: inline-block; }
        .share-btn { background: none; border: none; color: var(--text-muted); font-size: 1.2rem; cursor: pointer; padding: 5px; }
        .share-dropdown { display: none; position: absolute; right: 0; top: 40px; background-color: var(--modal-bg); min-width: 200px; box-shadow: 0px 8px 16px rgba(0,0,0,0.2); border: 1px solid var(--border-color); border-radius: 8px; z-index: 1001; }
        .share-dropdown a { color: var(--text-main); padding: 12px 16px; text-decoration: none; display: block; font-size: 0.9rem; border-bottom: 1px solid var(--border-color); text-align: left; }
        .share-dropdown a:last-child { border-bottom: none; }
        .share-dropdown a:hover { background-color: var(--card-hover); }
        .share-dropdown i { margin-right: 10px; width: 20px; text-align: center; }

        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .verse-header { text-align: center; margin-bottom: 30px; }
        .meal-highlight { font-size: 1.45rem; line-height: 1.7; margin-bottom: 25px; }
        
        /* Dipnot Trigger */
        .dipnot-trigger { color: var(--primary-green); cursor: pointer; font-weight: bold; margin-left: 5px; font-size: 1.2rem; vertical-align: super; }
        
        .arabic-text { font-family: 'Amiri', serif; font-size: 2.3rem; direction: rtl; line-height: 2; margin-bottom: 15px; }
        .mushaf-block { font-family: 'MushafFontu', serif; font-size: 3rem; direction: rtl; line-height: 1.8; background: rgba(39, 174, 96, 0.04); padding: 35px; border-radius: 15px; margin: 25px 0; border: 1px dashed var(--border-color); }

        .tabs { display: flex; gap: 10px; border-bottom: 2px solid var(--border-color); margin-bottom: 20px; }
        .tab-btn { padding: 12px 20px; cursor: pointer; border: none; background: none; font-weight: 500; color: var(--text-muted); border-bottom: 3px solid transparent; }
        .tab-btn.active { color: var(--primary-green); border-bottom-color: var(--primary-green); }

        .word-table { width: 100%; border-collapse: collapse; }
        .word-table td { padding: 15px 10px; border-bottom: 1px solid var(--border-color); }
        
        .settings-modal, .dipnot-modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; }
        .modal-box { background: var(--modal-bg); padding: 25px; border-radius: 15px; width: 90%; max-width: 350px; }
        
        /* Dipnot Content */
        .dipnot-content { background: var(--modal-bg); color: var(--text-main); width: 90%; max-width: 500px; padding: 25px; border-radius: 15px; position: relative; line-height: 1.6; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .dipnot-close { float: right; cursor: pointer; font-size: 1.5rem; color: var(--text-muted); margin-top: -10px; }

        .setting-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        
        .fixed-nav { position: fixed; bottom: 0; left: 0; right: 0; background: var(--nav-bg); padding: 10px 5px; z-index: 10000; display: flex; justify-content: center; }
        .nav-container { display: flex; align-items: center; gap: 5px; width: 100%; max-width: 600px; }
        .nav-btn { background: #333; color: #ccc; border: none; padding: 10px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; }
        .nav-select { background: #333; color: #fff; border: 1px solid #444; padding: 10px; border-radius: 5px; font-size: 0.85rem; flex: 1; }

        @media (min-width: 992px) {
            .tabs { display: none; }
            .content-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
            .tab-pane { display: block !important; }
            .column-title { font-size: 1.2rem; font-weight: bold; color: var(--text-muted); margin-bottom: 15px; border-bottom: 2px solid var(--primary-green); padding-bottom: 10px; }
        }
        @media (max-width: 991px) {
            .tab-pane { display: none; }
            .tab-pane.active { display: block; }
        }
    </style>
</head>
<body id="pageBody">

<div class="header">
    <a href="surah.php?id=<?= $sureNo ?>&meal=<?= $mealNo ?>" style="color:var(--text-main);"><i class="fas fa-arrow-left"></i></a>
    <div class="header-center">
        <strong><?= $sureNo ?>:<?= $ayetNo ?></strong>
        <select class="meal-select-header" onchange="location.href='verse.php?sure=<?= $sureNo ?>&ayet=<?= $ayetNo ?>&meal=' + this.value">
            <?php
            $mList = $pdo->query("SELECT mealNo, mealAdi FROM tbl_meal WHERE Aktif = 0 ORDER BY mealNo ASC");
            while($m = $mList->fetch()) {
                echo "<option value='{$m['mealNo']}' ".($m['mealNo']==$mealNo?'selected':'').">{$m['mealAdi']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="header-right">
        <i id="favBtn" class="<?= $isFav ? 'fas' : 'far' ?> fa-bookmark fav-icon <?= $isFav ? 'active' : '' ?>" onclick="handleFavClick(<?= $sureNo ?>, <?= $ayetNo ?>)"></i>
        
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

        <i class="fas fa-cog" style="color: var(--text-muted); cursor:pointer;" onclick="handleSettingsClick()"></i>
    </div>
</div>

<div class="container">
    <div class="verse-header">
        <?php if($showMeal): ?>
            <div class="meal-highlight">
                <?= $mainMeal['Metin'] ?? 'Məal tapılmadı.' ?>
                <?php if ($dipnot): ?>
                    <span class="dipnot-trigger" onclick="openDipnot()">*</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if($showArabic): ?>
            <div class="arabic-text"><?= trim($fullArabic) ?></div>
        <?php endif; ?>

        <?php if($showMushaf): ?>
            <div class="mushaf-block"><?= noqtesizEle($mainMeal['mushaf_ayah'] ?? '') ?></div>
        <?php endif; ?>

        <?php if($showTrans): ?>
            <div class="transcription-text" style="color: var(--primary-green); text-align:center;"><?= mb_strtolower(trim($fullTrans), 'UTF-8') ?>.</div>
        <?php endif; ?>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="openTab(event, 'kelimeler')">Kəlimələr</button>
        <button class="tab-btn" onclick="openTab(event, 'ceviriler')">Çevirilər</button>
    </div>

    <div class="content-wrapper">
        <div id="kelimeler" class="tab-pane active">
            <div class="column-title">Kəlimə Analizi</div>
            <table class="word-table">
                <?php foreach($kelimeler as $k): ?>
                <tr>
                    <td style="color:var(--text-muted); width:20px;"><?= $k['KelimeNo'] ?></td>
                    <td><strong><?= mb_strtolower($k['TranscriptTurkce'], 'UTF-8') ?></strong></td>
                    <td><?= $k['Turkce'] ?></td>
                    <td style="text-align:right;">
                        <a href="root_details.php?kok=<?= urlencode($k['KokArapca']) ?>" style="color:var(--primary-green); text-decoration:none; font-family:'Amiri'; font-size: 1.2rem;"><?= $k['KokArapca'] ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div id="ceviriler" class="tab-pane">
            <div class="column-title">Digər Məallar</div>
            <?php
            $allMealsStmt = $pdo->prepare("SELECT k.MealAdi, k.Metin FROM tbl_kayit k JOIN tbl_meal m ON k.MealAdi = m.mealAdi WHERE k.SureNo = ? AND k.AyetNo = ? AND m.Aktif = 0 AND m.mealNo != ?");
            $allMealsStmt->execute([$sureNo, $ayetNo, $mealNo]);
            while($m = $allMealsStmt->fetch()): ?>
                <div style="margin-bottom:20px; padding:10px; border-left:3px solid var(--border-color);">
                    <small style="color:var(--primary-green); font-weight:bold;"><?= $m['MealAdi'] ?></small>
                    <div style="line-height:1.6; margin-top:5px; font-size: 0.95rem;"><?= $m['Metin'] ?></div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<div id="settingsModal" class="settings-modal">
    <div class="modal-box">
        <h3 style="margin-top:0;">Görünüş Ayarları</h3>
        <a href="favorites_list.php" style="display: block; width: 100%; padding: 10px; background: #f0f0f0; color: #333; text-decoration: none; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold;">
           <i class="fas fa-heart" style="color: #e74c3c;"></i> Favorilərim
        </a>
        <hr style="border:0; border-top:1px solid var(--border-color); margin-bottom:15px;">
        <div class="setting-row"><span>Məal (Tərcümə)</span><input type="checkbox" <?= $showMeal ? 'checked' : '' ?> onchange="updateSetting('show_translation', this.checked)"></div>
        <div class="setting-row"><span>Ərəbcə Mətn</span><input type="checkbox" <?= $showArabic ? 'checked' : '' ?> onchange="updateSetting('show_arabic', this.checked)"></div>
        <div class="setting-row"><span>Müshaf (Nöqtəsiz)</span><input type="checkbox" <?= $showMushaf ? 'checked' : '' ?> onchange="updateSetting('show_mushaf', this.checked)"></div>
        <div class="setting-row"><span>Transkripsiya</span><input type="checkbox" <?= $showTrans ? 'checked' : '' ?> onchange="updateSetting('show_transcription', this.checked)"></div>
        <button onclick="toggleSettings()" style="width:100%; padding:10px; background:var(--primary-green); color:white; border:none; border-radius:8px; margin-top:10px;">Bağla</button>
    </div>
</div>

<?php if ($dipnot): ?>
<div id="dipnotModal" class="dipnot-modal">
    <div class="dipnot-content">
        <span class="dipnot-close" onclick="closeDipnot()">&times;</span>
        <h4 style="margin-top:0; color:var(--primary-green);">Dipnot (Qeyd)</h4>
        <div style="max-height: 300px; overflow-y: auto;"><?= nl2br(htmlspecialchars($dipnot['DipnotMetin'])) ?></div>
    </div>
</div>
<?php endif; ?>

<div class="fixed-nav">
    <div class="nav-container">
        <a href="index.php" class="nav-btn"><i class="fas fa-home"></i></a>
        <a href="verse.php?sure=<?= ($ayetNo > 1) ? $sureNo : (($sureNo > 1) ? $sureNo - 1 : 114) ?>&ayet=<?= ($ayetNo > 1) ? $ayetNo - 1 : 1 ?>&meal=<?= $mealNo ?>" class="nav-btn"><i class="fas fa-chevron-left"></i></a>
        <select class="nav-select" onchange="location.href='verse.php?sure=' + this.value + '&ayet=1&meal=<?= $mealNo ?>'">
            <?php
            $sList = $pdo->query("SELECT SureNo, SureAdi FROM tbl_sureler ORDER BY CAST(SureNo AS UNSIGNED) ASC");
            while($s = $sList->fetch()) { echo "<option value='{$s['SureNo']}' ".($s['SureNo']==$sureNo?'selected':'').">{$s['SureNo']}. {$s['SureAdi']}</option>"; }
            ?>
        </select>
        <a href="verse.php?sure=<?= ($ayetNo < $maxAyet) ? $sureNo : (($sureNo < 114) ? $sureNo + 1 : 1) ?>&ayet=<?= ($ayetNo < $maxAyet) ? $ayetNo + 1 : 1 ?>&meal=<?= $mealNo ?>" class="nav-btn"><i class="fas fa-chevron-right"></i></a>
        <select class="nav-select" style="max-width: 80px;" onchange="location.href='verse.php?sure=<?= $sureNo ?>&ayet=' + this.value + '&meal=<?= $mealNo ?>'">
            <?php for($i=1; $i<=$maxAyet; $i++) echo "<option value='$i' ".($i==$ayetNo?'selected':'').">$i</option>"; ?>
        </select>
    </div>
</div>

<script>
function openDipnot() {
    const dModal = document.getElementById('dipnotModal');
    if(dModal) dModal.style.display = 'flex';
}

function closeDipnot() {
    const dModal = document.getElementById('dipnotModal');
    if(dModal) dModal.style.display = 'none';
}

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

window.onclick = function(event) {
    var shareMenu = document.getElementById("shareMenu");
    if (!event.target.closest('.share-container')) {
        shareMenu.style.display = "none";
    }
    // Dipnot modalını kənara basanda bağlamaq üçün
    var dModal = document.getElementById('dipnotModal');
    if (event.target == dModal) {
        closeDipnot();
    }
    // Ayarlar modalı üçün
    var sModal = document.getElementById('settingsModal');
    if (event.target == sModal) {
        toggleSettings();
    }
}

if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark');
}

const isLoggedIn = <?= isset($_SESSION['user']) ? 'true' : 'false' ?>;

function handleFavClick(sure, ayet) { if (!isLoggedIn) { window.location.href = "login.php"; return; } toggleFav(sure, ayet); }
function handleSettingsClick() { if (!isLoggedIn) { window.location.href = "login.php"; return; } toggleSettings(); }
function toggleSettings() { const modal = document.getElementById('settingsModal'); modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex'; }

function openTab(evt, tabId) {
    if (window.innerWidth < 992) {
        var i, tabPane, tabBtn;
        tabPane = document.getElementsByClassName("tab-pane");
        for (i = 0; i < tabPane.length; i++) { tabPane[i].style.display = "none"; tabPane[i].classList.remove("active"); }
        tabBtn = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tabBtn.length; i++) { tabBtn[i].classList.remove("active"); }
        document.getElementById(tabId).style.display = "block";
        document.getElementById(tabId).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
}

function updateSetting(field, value) {
    if (!isLoggedIn) { window.location.href = "login.php"; return; }
    fetch('update_settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `field=${field}&value=${value?1:0}`
    }).then(() => location.reload());
}

function toggleFav(sure, ayet) {
    const btn = document.getElementById('favBtn');
    fetch('toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `sure=${sure}&ayet=${ayet}`
    }).then(res => res.json()).then(data => {
        if (data.status === 'added') { btn.classList.replace('far', 'fas'); btn.classList.add('active'); }
        else { btn.classList.replace('fas', 'far'); btn.classList.remove('active'); }
    });
}
</script>
</body>
</html>