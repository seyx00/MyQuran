<?php
include 'config.php';
session_start();

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$favorites = [];

if ($user) {
    $uId = $user['id'];
    // Favoriləri və surə adlarını bazadan çəkirik
    $stmt = $pdo->prepare("
        SELECT f.*, s.SureAdi 
        FROM favorites f 
        JOIN tbl_sureler s ON f.sure_no = s.SureNo 
        WHERE f.user_id = ? 
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$uId]);
    $favorites = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yer İşarələri - My Quran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root { 
            --p-green: #27ae60; --bg: #ffffff; --txt: #333; --border: #eee; 
            --card-bg: #fdfdfd;
        }
        body.dark { 
            --bg: #121212; --txt: #e0e0e0; --border: #2c2c2c; 
            --card-bg: #1e1e1e;
        }
        body { 
            font-family: 'Roboto', sans-serif; background: var(--bg); 
            color: var(--txt); margin: 0; transition: 0.3s; 
        }
        
        .header { 
            padding: 15px; border-bottom: 1px solid var(--border); 
            display: flex; align-items: center; gap: 15px; 
            position: sticky; top: 0; background: var(--bg); z-index: 10; 
        }
        .header a { color: inherit; text-decoration: none; font-size: 1.2rem; }
        
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        
        /* Giriş edilməyibse və ya Siyahı boşdursa stil */
        .empty-state { 
            text-align: center; padding: 80px 20px; color: #888; 
        }
        .empty-state i { font-size: 4rem; margin-bottom: 20px; color: var(--border); }
        .login-btn {
            display: inline-block; background: #db4437; color: white; 
            padding: 12px 25px; border-radius: 50px; text-decoration: none; 
            margin-top: 20px; font-weight: 500; transition: 0.3s;
        }
        .login-btn:hover { background: #c53727; transform: scale(1.05); }

        /* Favori Elementləri */
        .fav-card { 
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: 12px; margin-bottom: 15px; display: flex; 
            align-items: center; transition: 0.2s; overflow: hidden;
        }
        .fav-card:hover { border-color: var(--p-green); }
        
        .fav-link { 
            flex: 1; padding: 15px; text-decoration: none; color: inherit; 
            display: flex; justify-content: space-between; align-items: center;
        }
        .fav-info h4 { margin: 0; color: var(--p-green); font-size: 1.1rem; }
        .fav-info span { font-size: 0.85rem; color: #888; margin-top: 5px; display: block; }
        
        .remove-btn { 
            padding: 20px; color: #ff7675; cursor: pointer; 
            transition: 0.2s; border-left: 1px solid var(--border);
        }
        .remove-btn:hover { background: #fff5f5; color: #d63031; }
    </style>
</head>
<body id="pageBody">

<div class="header">
    <a href="index.php"><i class="fas fa-arrow-left"></i></a>
    <h3 style="margin:0;">Yer İşarələri</h3>
</div>

<div class="container">
    <?php if (!$user): ?>
        <div class="empty-state">
            <i class="fas fa-user-lock"></i>
            <h2>Giriş Lazımdır</h2>
            <p>Yadda saxladığınız ayələri görmək üçün Google hesabınızla giriş etməlisiniz.</p>
            <a href="<?= getGoogleLoginUrl(); ?>" class="login-btn">
                <i class="fab fa-google"></i> Google ilə Giriş Et
            </a>
        </div>

    <?php elseif (empty($favorites)): ?>
        <div class="empty-state">
            <i class="far fa-bookmark"></i>
            <h2>Hələ heç nə yoxdur</h2>
            <p>Ayələri oxuyarkən yer işarəsi ikonuna <i class="far fa-bookmark"></i> toxunaraq bura əlavə edə bilərsiniz.</p>
            <a href="index.php" style="color: var(--p-green); text-decoration:none; display:block; margin-top:15px;">Mütaliəyə başla</a>
        </div>

    <?php else: ?>
        <?php foreach ($favorites as $f): ?>
            <div class="fav-card" id="fav-row-<?= $f['id'] ?>">
                <a href="verse.php?sure=<?= $f['sure_no'] ?>&ayet=<?= $f['ayet_no'] ?>" class="fav-link">
                    <div class="fav-info">
                        <h4><?= $f['SureAdi'] ?></h4>
                        <span>Surə: <?= $f['sure_no'] ?>, Ayə: <?= $f['ayet_no'] ?></span>
                    </div>
                    <i class="fas fa-chevron-right" style="color: #ccc;"></i>
                </a>
                <div class="remove-btn" title="Sil" onclick="removeFav(<?= $f['sure_no'] ?>, <?= $f['ayet_no'] ?>, <?= $f['id'] ?>)">
                    <i class="fas fa-trash-alt"></i>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
// Tema tətbiqi
if (localStorage.getItem('theme') === 'dark') document.body.classList.add('dark');

function removeFav(sure, ayet, rowId) {
    if(!confirm('Bu ayəni yer işarələrindən silmək istəyirsiniz?')) return;
    
    fetch('toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `sure=${sure}&ayet=${ayet}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'removed') {
            const row = document.getElementById('fav-row-' + rowId);
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => {
                row.remove();
                if (document.querySelectorAll('.fav-card').length === 0) location.reload();
            }, 300);
        }
    });
}
</script>

</body>
</html>