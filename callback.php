<?php
include 'config.php';
session_start();

if (isset($_GET['code'])) {
    // 1. Token alırıq
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'code' => $_GET['code'],
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URL,
        'grant_type' => 'authorization_code'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($ch), true);
    
    if (isset($response['access_token'])) {
        // 2. İstifadəçi məlumatlarını alırıq
        $info = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $response['access_token']), true);
        
        // 3. Bazada yoxlayırıq və ya qeyd edirik
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([$info['id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $ins = $pdo->prepare("INSERT INTO users (google_id, email, ad_soyad) VALUES (?, ?, ?)");
            $ins->execute([$info['id'], $info['email'], $info['name']]);
            $userId = $pdo->lastInsertId();
            $user = ['id' => $userId, 'show_arabic' => 1, 'show_transcription' => 1, 'show_translation' => 1];
        }
        
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    }
}
