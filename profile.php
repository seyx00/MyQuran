<?php
include 'config.php';
session_start();

// Giriş edilməyibsə index-ə at
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$uId = $user['id'];

// Ən son ayarları bazadan yenidən çəkək
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uId]);
$userData = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?= htmlspecialchars($userData['ad_soyad']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root { --p-green: #27ae60; --bg: #f4f7f6; --card: #ffffff; --text: #333; }
        body.dark { --bg: #121212; --card: #1e1e1e; --text: #e0e0e0; }
        
        body { font-family: 'Roboto', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; transition: 0.3s; }
        .profile-container { max-width: 500px; margin: 40px auto; background: var(--card); border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; }
        
        .avatar { width: 100px; height: 100px; background: var(--p-green); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; }
        
        h2 { margin-bottom: 5px; }
        .email { color: #888; margin-bottom: 30px; font-size: 0.9rem; }
        
        .settings-group { text-align: left; background: rgba(0,0,0,0.03); padding: 20px; border-radius: 15px; margin-bottom: 25px; }
        .setting-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .setting-item:last-child { margin-bottom: 0; }
        
        .btn { display: inline-block; width: 100%; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: 500; margin-top: 10px; transition: 0.2s; border: none; cursor: pointer; }
        .btn-home { background: var(--p-green); color: white; }
        .btn-logout { background: #e74c3c; color: white; margin-top: 15px; }
        
        /* Switch Dizaynı */
        .switch { position: relative; display: inline-block; width: 45px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--p-green); }
        input:checked + .slider:before { transform: translateX(21px); }
    </style>
</head>
<body id="profileBody">

<div class="profile-container">
    <div class="avatar">
        <i class="fas fa-user"></i>
    </div>
    <h2><?= htmlspecialchars($userData['ad_soyad']) ?></h2>
    <div class="email"><?= htmlspecialchars($userData['email']) ?></div>

    <div class="settings-group">
        <h4 style="margin-top:0; margin-bottom:15px; color: var(--p-green);">Xüsusi Ayarlar</h4>
        
        <div class="setting-item">
            <span>Ərəbcə Mətn</span>
            <label class="switch">
                <input type="checkbox" <?= $userData['show_arabic'] ? 'checked' : '' ?> onchange="updateProfile('show_arabic', this.checked)">
                <span class="slider"></span>
            </label>
        </div>

        <div class="setting-item">
            <span>Transkripsiya</span>
            <label class="switch">
                <input type="checkbox" <?= $userData['show_transcription'] ? 'checked' : '' ?> onchange="updateProfile('show_transcription', this.checked)">
                <span class="slider"></span>
            </label>
        </div>

        <div class="setting-item">
            <span>Tərcümə</span>
            <label class="switch">
                <input type="checkbox" <?= $userData['show_translation'] ? 'checked' : '' ?> onchange="updateProfile('show_translation', this.checked)">
                <span class="slider"></span>
            </label>
        </div>
    </div>

    <a href="index.php" class="btn btn-home"><i class="fas fa-home"></i> Ana Səhifə</a>
    <a href="logout.php" class="btn btn-logout"><i class="fas fa-sign-out-alt"></i> Çıxış Et</a>
</div>

<script>
    // Tema yoxlaması
    if (localStorage.getItem('theme') === 'dark') document.body.classList.add('dark');

    function updateProfile(field, value) {
        const val = value ? 1 : 0;
        fetch('update_settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `field=${field}&value=${val}`
        });
    }
</script>

</body>
</html>
