<?php
include 'config.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($search)) {
    // Dictionary və Sentences cədvəllərini word_id üzərindən birləşdiririk
    $query = "SELECT d.*, s.sentence, s.translation 
              FROM dictionary d
              LEFT JOIN sentences s ON d.id = s.word_id
              WHERE d.word LIKE ? OR d.word_search LIKE ?
              ORDER BY d.id ASC";
    
    $stmt = $pdo->prepare($query);
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm]);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $wordId = $row['id'];
        if (!isset($results[$wordId])) {
            $results[$wordId] = [
                'word' => $row['word'],
                'mean' => $row['meanings_strong'],
                'weak_mean' => $row['meanings_weak'],
                'sentences' => []
            ];
        }
        if (!empty($row['sentence'])) {
            $results[$wordId]['sentences'][] = [
                'text' => $row['sentence'],
                'trans' => $row['translation']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lüğət Axtarışı</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* CSS Dəyişənləri - Index.php ilə eyni məntiqlə */
        :root { 
            --primary-green: #27ae60; 
            --bg-color: #f4f7f6; 
            --text-main: #333; 
            --card-bg: #ffffff;
            --border-color: #eee;
            --input-bg: #ffffff;
            --sentence-bg: #f9f9f9;
        }

        body.dark {
            --bg-color: #121212; 
            --text-main: #e0e0e0; 
            --card-bg: #1e1e1e;
            --border-color: #2c2c2c;
            --input-bg: #2a2a2a;
            --sentence-bg: #252525;
        }

        body { 
            font-family: 'Roboto', sans-serif; 
            background: var(--bg-color); 
            color: var(--text-main); 
            margin: 0; 
            padding: 20px; 
            transition: background 0.3s, color 0.3s;
        }

        .search-container { max-width: 600px; margin: 0 auto 30px; text-align: center; }
        
        .search-box { 
            width: 80%; padding: 12px; 
            border: 2px solid var(--primary-green); 
            border-radius: 25px; outline: none; 
            font-size: 16px; 
            background: var(--input-bg);
            color: var(--text-main);
        }

        .search-btn { 
            padding: 12px 25px; background: var(--primary-green); 
            color: white; border: none; border-radius: 25px; 
            cursor: pointer; margin-left: -50px; 
        }
        
        .result-card { 
            background: var(--card-bg); 
            max-width: 700px; margin: 20px auto; 
            padding: 20px; border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            border: 1px solid var(--border-color);
        }

        .word-header { 
            display: flex; justify-content: space-between; 
            align-items: center; border-bottom: 2px solid var(--border-color); 
            padding-bottom: 10px; 
        }

        .arabic-word { font-size: 2rem; color: var(--primary-green); font-family: 'Amiri', serif; }
        
        .meaning-text { margin-top: 15px; line-height: 1.6; }
        
        .sentence-section { 
            margin-top: 20px; background: var(--sentence-bg); 
            padding: 15px; border-radius: 8px; 
            border-left: 4px solid #3498db; 
        }

        .sentence-item { margin-bottom: 15px; direction: rtl; text-align: right; }
        .sentence-trans { 
            direction: ltr; text-align: left; font-size: 0.9rem; 
            color: #888; font-style: italic; display: block; 
        }

        .back-link { text-decoration: none; color: var(--text-main); font-size: 1.2rem; margin-bottom: 20px; display: inline-block; }
    </style>
</head>
<body id="pageBody">

<script>
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark');
    }
</script>

<div class="container">
    <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Geri</a>

    <div class="search-container">
        <h2><i class="fas fa-book"></i> Lüğət Axtarışı</h2>
        <form action="" method="GET">
            <input type="text" name="q" class="search-box" placeholder="Söz yazın..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <?php if (!empty($results)): ?>
        <?php foreach ($results as $item): ?>
            <div class="result-card">
                <div class="word-header">
                    <div class="arabic-word" dir="rtl"><?= $item['word'] ?></div>
                </div>
                
                <div class="meaning-text">
                    <strong>Məna:</strong> <?= $item['mean'] ?>
                    <?php if ($item['weak_mean']): ?>
                        <div style="margin-top: 5px; font-size: 0.85rem; opacity: 0.7;">
                            <i>Əlavə: <?= $item['weak_mean'] ?></i>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($item['sentences'])): ?>
                    <div class="sentence-section">
                        <strong style="color: #3498db; font-size: 0.8rem; text-transform: uppercase;">Nümunə Cümlə:</strong>
                        <?php foreach ($item['sentences'] as $s): ?>
                            <div class="sentence-item">
                                <div style="font-size: 1.2rem; font-family: 'Amiri', serif; margin-top: 5px;"><?= $s['text'] ?></div>
                                <span class="sentence-trans"><?= $s['trans'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php elseif (!empty($search)): ?>
        <p style="text-align: center; opacity: 0.6;">Nəticə tapılmadı.</p>
    <?php endif; ?>
</div>

</body>
</html>