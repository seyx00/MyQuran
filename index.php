<?php
include 'config.php';  
session_start();
$lang = 'az';

// Əsas axtarış üçün istifadə olunacaq meal ID-si
$mainSearchMealID = 105; 

// İstifadəçi məlumatlarını yoxlayırıq
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

$transMap = [
    'ا' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 's', 'ج' => 'c', 'ح' => 'h', 'خ' => 'x',
    'د' => 'd', 'ذ' => 'z', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'ş', 'ص' => 's',
    'ض' => 'd', 'ط' => 't', 'ظ' => 'z', 'ع' => 'e', 'غ' => 'g', 'ف' => 'f', 'ق' => 'q',
    'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'و' => 'v', 'ه' => 'h', 'ي' => 'y',
    'ء' => 'e'
];

function getTrans($text, $map) {
    $res = "";
    $chars = mb_str_split($text);
    foreach($chars as $char) {
        $res .= isset($map[$char]) ? $map[$char] : "";
    }
    return $res;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Quran - Quran Oxu və Öyrən</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --bg-image: url('images/header-dg.jpeg');
            --overlay: rgba(0, 0, 0, 0.35); 
            --text-color: #ffffff;
            --card-bg: rgba(255, 255, 255, 0.15);
            --card-text: #ffffff;
            --accent-color: #27ae60;
        }

        body.dark {
            --bg-image: url('images/header-bg.jpeg');
            --overlay: rgba(0, 0, 0, 0.7); 
            --text-color: #e0e0e0;
            --card-bg: rgba(0, 0, 0, 0.45);
            --card-text: #d1d1d1;
            --accent-color: #2ecc71;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Roboto', sans-serif; 
            background: linear-gradient(var(--overlay), var(--overlay)), var(--bg-image) center/cover no-repeat;
            background-attachment: fixed;
            color: var(--text-color);
            min-height: 100vh;
            transition: background 0.5s ease-in-out;
        }

        .top-actions {
            position: fixed; top: 20px; right: 20px; z-index: 1000;
            display: flex; gap: 10px; align-items: center;
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3);
            color: white; padding: 10px 18px; border-radius: 50px; cursor: pointer; 
            backdrop-filter: blur(8px); text-decoration: none; font-size: 0.9rem;
            display: flex; align-items: center; gap: 8px; transition: 0.3s;
        }
        .action-btn:hover { background: rgba(255, 255, 255, 0.25); }
        .login-google { background: #db4437; border-color: #db4437; }
        .login-google:hover { background: #c53727; }

        header { padding: 120px 20px 60px; text-align: center; }
        header h1 { font-family: 'Amiri', serif; font-size: 5.5rem; letter-spacing: 12px; text-shadow: 2px 4px 15px rgba(0,0,0,0.5); }

        .search-container {
            width: 100%; max-width: 750px; background: white; border-radius: 12px;
            display: flex; align-items: center; padding: 5px 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4); margin: 20px auto;
        }

        .search-container input {
            flex: 1; border: none; outline: none; padding: 15px 10px;
            font-size: 1.1rem; color: #333; min-width: 50px;
        }

        .search-icons {
            display: flex; gap: 15px; border-left: 1px solid #eee;
            padding-left: 15px; color: #666; flex-shrink: 0;
        }

        .search-icons i { cursor: pointer; font-size: 1.2rem; transition: 0.2s; }
        .search-icons i:hover { color: #27ae60; }

        .modal {
            display: none; position: fixed; z-index: 99999; left: 0; top: 0;
            width: 100%; height: 100%; background: rgba(0,0,0,0.8);
            backdrop-filter: blur(8px); align-items: center; justify-content: center;
        }
        .modal-content {
            background: #fff; width: 90%; max-width: 420px; border-radius: 20px;
            padding: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h2 { color: #222; margin: 0; font-size: 1.4rem; }
        .close-modal { cursor: pointer; font-size: 30px; color: #aaa; }

        .step-select {
            width: 100%; padding: 15px; border-radius: 12px; border: 2px solid #eee;
            font-size: 1.1rem; outline: none; color: #333; margin-bottom: 15px;
            font-family: 'Amiri', 'Roboto', sans-serif;
        }

        main { padding: 40px 20px; max-width: 1200px; margin: 0 auto; }
        .surah-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }

        .surah-card {
            background: var(--card-bg); backdrop-filter: blur(12px); padding: 25px;
            border-radius: 18px; display: flex; flex-direction: column; text-decoration: none;
            color: var(--card-text); border: 1px solid rgba(255, 255, 255, 0.1); transition: 0.3s;
        }
        .surah-card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.25); }

        footer { text-align: center; padding: 60px; opacity: 0.7; }
    </style>
</head>
<body id="mainBody">

    <div class="top-actions">
        <button class="action-btn" onclick="toggleTheme()">
            <i id="themeIcon" class="fas fa-moon"></i>
            <span id="themeText">Gecə</span>
        </button>


        <?php if($user): ?>
            <div style="display: flex; gap: 10px;">
                <a href="favorites_list.php" class="action-btn" title="Yer İşarələri">
                    <i class="far fa-bookmark"></i>
                </a>
                
                <a href="profile.php" class="action-btn">
                    <i class="fas fa-user-circle"></i> <?= explode(' ', $user['ad_soyad'])[0] ?>
                </a>
                <a href="logout.php" class="action-btn" title="Çıxış">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        <?php else: ?>
            <a href="<?= getGoogleLoginUrl(); ?>" class="action-btn login-google">
                <i class="fab fa-google"></i> Giriş
            </a>
        <?php endif; ?>
    </div>

    <header>
        <h1>My.</h1>
        <p>Quran-ı Kərimi oxu, dinlə və araşdır</p>

        <div class="search-container">
            <i class="fas fa-search" style="color:#bbb"></i>
            <input type="text" id="searchInput" placeholder="Surə, ayə və ya kəlmə axtar..." onkeyup="liveSearch()">
            <div class="search-icons">
                <i class="fas fa-th-list" id="sureBtn" title="Surə seç"></i>
                <i class="fas fa-font" id="rootBtn" title="Hərf/Kök"></i>
                <a href="dictionary.php" style="color: inherit; text-decoration: none;"><i class="fas fa-book"></i></a>
            </div>
        </div>
    </header>

    <main>
        <div id="searchResults" class="surah-list"></div>
        <div id="defaultSurahList" class="surah-list"></div>
    </main>

    <div id="surahModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Surə seçin</h2>
                <span class="close-modal" onclick="closeModals()">&times;</span>
            </div>
            <select id="surahSelect" class="step-select">
                <option value="" disabled selected>Siyahıdan bir surə seçin...</option>
                <?php
                $sQuery = $pdo->query("SELECT SureNo, SureAdi FROM tbl_sureler ORDER BY CAST(SureNo AS UNSIGNED) ASC");
                while($s = $sQuery->fetch()) {
                    echo "<option value='".$s['SureNo']."'>".$s['SureNo'].". ".$s['SureAdi']."</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div id="rootModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Kök Axtarışı</h2>
                <span class="close-modal" onclick="closeModals()">&times;</span>
            </div>
            <select id="letterSelect" class="step-select" onchange="fetchRoots(this.value)">
                <option value="">Əvvəlcə hərf seçin...</option>
                <?php
                $letters = $pdo->query("SELECT DISTINCT LEFT(KokArapca, 1) as harf FROM tbl_sozluk WHERE KokArapca != '' ORDER BY harf ASC");
                while($l = $letters->fetch()) {
                    if($l['harf']) {
                        $tr = getTrans($l['harf'], $transMap);
                        echo "<option value='{$l['harf']}'>{$l['harf']} . {$tr}</option>";
                    }
                }
                ?>
            </select>
            <select id="rootSelect" class="step-select" style="display:none;" onchange="goToRoot(this.value)">
                <option value="">Kökü seçin...</option>
            </select>
        </div>
    </div>

    <footer>
        <p>© <?= date('Y') ?> My Quran Proyekti.</p>
    </footer>

<footer style="margin-top: 100px; padding: 20px; border-top: 1px solid rgba(128,128,128,0.2);">
    <div style="max-width: 800px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
        
        <div class="social-links">
            <a href="#" style="color: inherit; margin-right: 15px; text-decoration: none;"><i class="fab fa-x-twitter"></i></a>
            <a href="#" style="color: inherit; margin-right: 15px; text-decoration: none;"><i class="fab fa-youtube"></i></a>
            <a href="#" style="color: inherit; margin-right: 15px; text-decoration: none;"><i class="fab fa-instagram"></i></a>
            <a href="#" style="color: inherit; margin-right: 15px; text-decoration: none;"><i class="fab fa-github"></i></a>
        </div>

        <div class="about-link-container">
            <a href="about.php" style="color: inherit; text-decoration: none; font-weight: 500;">
                <i class="fas fa-seedling" style="color: var(--primary-green);"></i> My Quran
            </a>
        </div>
        
    </div>
</footer>

    <script>
        const jsTransMap = <?= json_encode($transMap) ?>;
        function getJsTrans(text) { return text.split('').map(char => jsTransMap[char] || '').join(''); }

        function closeModals() {
            document.getElementById('surahModal').style.display = 'none';
            document.getElementById('rootModal').style.display = 'none';
        }

        function fetchRoots(letter) {
            const rootSelect = document.getElementById('rootSelect');
            if(!letter) { rootSelect.style.display = 'none'; return; }
            fetch('get_roots.php?letter=' + encodeURIComponent(letter))
                .then(response => response.json())
                .then(data => {
                    rootSelect.innerHTML = '<option value="">Kökü seçin...</option>';
                    data.forEach(root => {
                        let tr = getJsTrans(root);
                        rootSelect.innerHTML += `<option value="${root}">${root} (${tr})</option>`;
                    });
                    rootSelect.style.display = 'block';
                });
        }

        function goToRoot(root) {
            if(root) window.location.href = 'root_details.php?kok=' + encodeURIComponent(root);
        }

        function liveSearch() {
            const query = document.getElementById('searchInput').value.trim();
            const resultsDiv = document.getElementById('searchResults');
            const defaultDiv = document.getElementById('defaultSurahList');
            const mealId = <?= $mainSearchMealID ?>;

            if (query.length < 2) {
                resultsDiv.style.display = 'none';
                defaultDiv.style.display = 'grid';
                return;
            }

            fetch(`search_api.php?q=${encodeURIComponent(query)}`)
    .then(response => response.json())
    .then(data => {
        resultsDiv.innerHTML = '';
        if (data && data.length > 0) {
            resultsDiv.style.display = 'grid';
            defaultDiv.style.display = 'none';
            data.forEach(item => {
                resultsDiv.innerHTML += `
                    <a href="verse.php?sure=${item.SureNo}&ayet=${item.AyetNo}" class="surah-card">
                        <span style="font-size:0.8rem; color:var(--accent-color); font-weight:bold;">
                            ${item.SureAdi} - ${item.SureNo}:${item.AyetNo}
                        </span>
                        <div style="font-size:0.95rem; line-height:1.4; opacity:0.9; margin:5px 0;">
                            ${item.MealMetin}
                        </div>
                        <div dir="rtl" style="font-family:'Amiri', serif; font-size:1.1rem; text-align:right; color:var(--text-muted);">
                            ${item.Arapca}
                        </div>
                    </a>`;
            });
        } else {
            resultsDiv.innerHTML = '<p style="text-align:center; width:100%; padding:20px;">Nəticə tapılmadı.</p>';
            resultsDiv.style.display = 'block';
            defaultDiv.style.display = 'none';
        }
    });
        }

        function toggleTheme() {
            const body = document.getElementById('mainBody');
            body.classList.toggle('dark');
            localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
            
            const icon = document.getElementById('themeIcon');
            const text = document.getElementById('themeText');
            if(body.classList.contains('dark')) {
                icon.className = 'fas fa-sun';
                text.innerText = 'Gündüz';
            } else {
                icon.className = 'fas fa-moon';
                text.innerText = 'Gecə';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.getElementById('mainBody').classList.add('dark');
                document.getElementById('themeIcon').className = 'fas fa-sun';
                document.getElementById('themeText').innerText = 'Gündüz';
            }
            
            document.getElementById('sureBtn').onclick = () => document.getElementById('surahModal').style.display = "flex";
            document.getElementById('rootBtn').onclick = () => document.getElementById('rootModal').style.display = "flex";
            
            document.getElementById('surahSelect').onchange = function() {
                if (this.value) window.location.href = 'surah.php?id=' + this.value + '&meal=<?= $mainSearchMealID ?>';
            }
            
            window.onclick = (e) => { 
                if (e.target.className === 'modal') closeModals(); 
            }
        });
    </script>
</body>
</html>