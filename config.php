<?php
// Məlumat bazası parametrləri
$host     = 'localhost';
$dbname   = 'myquran_myquran'; 
$username = 'myquran_myquran'; 
$password = '123123123Ee@'; 
$charset  = 'utf8mb4';

// PDO bağlantı ayarları
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    die("Bazaya qoşulma xətası: " . $e->getMessage());
}

// --- GOOGLE LOGIN PARAMETRLƏRİ (YENİLƏNDİ) ---
define('GOOGLE_CLIENT_ID', '901493057926-70mg0nq268g8c9sfum1hh1dcar9akj9u.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');

// DİQQƏT: Bu link Google Console-da yazdığın "Authorized redirect URIs" ilə eyni olmalıdır!
define('GOOGLE_REDIRECT_URL', 'https://myquran.site/callback.php');

/**
 * Google Giriş Linkini Hazırlayan Funksiya
 */
function getGoogleLoginUrl() {
    $params = [
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URL,
        'response_type' => 'code',
        'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        'access_type'   => 'offline',
        'prompt'        => 'select_account'
    ];
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}
?>