<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haqqında - My Quran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #27ae60;
            --bg-color: #ffffff;
            --text-main: #333;
            --card-bg: #f9f9f9;
        }
        body.dark {
            --bg-color: #121212;
            --text-main: #e0e0e0;
            --card-bg: #1e1e1e;
        }
        body {
            font-family: sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .about-container { max-width: 600px; width: 100%; margin-top: 50px; }
        .back-btn { text-decoration: none; color: var(--text-main); font-size: 1.2rem; display: block; margin-bottom: 30px; }
        .about-header { text-align: center; margin-bottom: 40px; }
        .icon-seed { font-size: 4rem; color: var(--primary-green); margin-bottom: 20px; }
        .quote-box {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            font-style: italic;
            text-align: center;
            margin-bottom: 30px;
            color: #777;
        }
        .main-content h1 { font-size: 1.5rem; margin-bottom: 15px; }
        .main-content p { margin-bottom: 20px; opacity: 0.9; }
        .patreon-btn {
            display: inline-block;
            background: #ff424d;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<script>
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark');
    }
</script>

<div class="about-container">
    <a href="index.php" class="back-btn"><i class="fas fa-home"></i></a>

    <div class="about-header">
        <i class="fas fa-seedling icon-seed"></i>
    </div>

    <div class="quote-box">
        "Beləcə sən, batil olan hər şeydən arınmış olaraq, üzünü qərarlı bir şəkildə Allahın, insanları üzərində yaratdığı fitrətə çevir!..."
        <br><br>— Rum, 30
    </div>

    <div class="main-content">
        <h1>My Quran</h1>
        <p><strong>My Quran</strong>; insanların internet üzərindən Qurana sadə, bəsit, reklamsız və ən asan yoldan çalışmalarını məqsəd qoyan bir projedir.</p>
        <p>Heç bir qrup, icma və ya siyasi təşkilatla əlaqəsi yoxdur. Tamamilə fərdi bir təşəbbüsdür.</p>
        
        <a href="https://patreon.com/ketebe" class="patreon-btn"><i class="fab fa-patreon"></i> Bizə dəstək ol</a>
    </div>
</div>

</body>
</html>