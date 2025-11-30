<?php
session_start();
require '../db.php';

// Çıkış Yap
if(isset($_GET['logout'])){ 
    session_destroy(); 
    header("Location: index.php"); 
    exit; 
}

// Giriş Kontrolü
if(!isset($_SESSION['admin_logged_in'])){
    if(isset($_POST['password'])){
        $pass = md5($_POST['password']);
        $stmt = $pdo->prepare("SELECT * FROM settings WHERE admin_pass = ?");
        $stmt->execute([$pass]);
        if($stmt->rowCount() > 0){
            $_SESSION['admin_logged_in'] = true;
            header("Location: index.php"); exit;
        } else {
            $error = "Yanlış şifre!";
        }
    }

    // --- İSTATİSTİKLER (Giriş Ekranında Görünecek) ---
    $totalPhotos = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type IN ('image', 'video')")->fetchColumn();
    $totalAudios = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type = 'audio'")->fetchColumn();
    $totalNotes  = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type = 'note'")->fetchColumn();
?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Giriş Yap</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
        <style>
            body { display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4; margin: 0; font-family: 'Montserrat', sans-serif; }
            .login-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; width: 100%; max-width: 350px; }
            input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
            button { width: 100%; padding: 12px; background: #556B2F; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; }
            button:hover { background: #3e4f22; }
            
            /* Giriş Sayfası İstatistikleri */
            .stats-preview { display: flex; justify-content: space-around; width: 100%; max-width: 350px; margin-bottom: 20px; }
            .stat-bubble { background: #fff; padding: 10px; border-radius: 50%; width: 60px; height: 60px; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); font-size: 0.8rem; color: #556B2F; font-weight: bold; }
            .stat-bubble span { font-size: 1.2rem; color: #333; }
        </style>
    </head>
    <body>

        <div class="login-box">
            <h2 style="margin-top:0; color:#556B2F;">Yönetici Girişi</h2>
            <form method="POST">
                <input type="password" name="password" placeholder="Şifre" required>
                <button type="submit">Panele Gir</button>
                <?php if(isset($error)) echo "<p style='color:red; margin-top:10px;'>$error</p>"; ?>
            </form>
        </div>
    </body>
    </html>
<?php
    exit;
}

// Verileri Çek
$photos = $pdo->query("SELECT * FROM uploads WHERE file_type IN ('image', 'video') ORDER BY id DESC")->fetchAll();
$audios = $pdo->query("SELECT * FROM uploads WHERE file_type = 'audio' ORDER BY id DESC")->fetchAll();
$notes = $pdo->query("SELECT * FROM uploads WHERE file_type = 'note' ORDER BY id DESC")->fetchAll();

// Admin İçi İstatistikler
$countPhoto = count($photos);
$countAudio = count($audios);
$countNote  = count($notes);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anı Yönetim Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">

    <style>
        /* RESET & GENEL AYARLAR */
        body { margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; background: #f8f9fa; color: #333; }
        * { box-sizing: border-box; }
        
        /* 1. HEADER */
        .top-header {
            background-color: #556B2F; color: white; padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .top-header h1 { margin: 0; font-size: 1.2rem; font-weight: 600; }
        .logout-btn { background: rgba(255,255,255,0.2); color: white; padding: 8px 15px; text-decoration: none; border-radius: 20px; font-size: 0.85rem; transition: 0.3s; }
        .logout-btn:hover { background: rgba(255,255,255,0.4); }

        .container { max-width: 1200px; margin: 20px auto; padding: 0 15px; }

        /* 2. İSTATİSTİK KARTLARI (DASHBOARD) */
        .stats-container { display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap; }
        .stat-card { flex: 1; min-width: 150px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center; border-bottom: 4px solid #ddd; }
        .stat-card.c-green { border-color: #556B2F; }
        .stat-card.c-orange { border-color: #FF8C00; }
        .stat-card.c-yellow { border-color: #F4C430; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #333; display: block; }
        .stat-label { color: #777; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }

        /* 3. TABS */
        .tabs { display: flex; justify-content: center; gap: 10px; margin-bottom: 25px; flex-wrap: wrap; }
        .tab-btn { background: white; border: 2px solid #556B2F; color: #556B2F; padding: 10px 25px; border-radius: 25px; font-weight: bold; cursor: pointer; transition: all 0.3s; }
        .tab-btn.active, .tab-btn:hover { background: #556B2F; color: white; }
        .tab-content { display: none; animation: fadeIn 0.4s ease-in-out; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* 4. MEDYA GRID */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; }
        .media-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.08); transition: transform 0.2s; }
        .media-card:hover { transform: translateY(-3px); }
        .media-img-box { height: 180px; overflow: hidden; background: #eee; position: relative; }
        .media-img-box img, .media-img-box video { width: 100%; height: 100%; object-fit: cover; }
        .media-info { padding: 10px; text-align: center; }
        .guest-name { font-weight: bold; font-size: 0.9rem; color: #2d3e18; margin: 5px 0; }
        .date-time { font-size: 0.75rem; color: #888; display: block; margin-bottom: 8px; }
        .download-link { display: inline-block; background: #f1f1f1; padding: 5px 10px; border-radius: 4px; color: #555; text-decoration: none; font-size: 0.8rem; }

        /* DİĞER LİSTELER */
        .audio-list { display: flex; flex-direction: column; gap: 10px; }
        .audio-item { background: white; padding: 15px; border-radius: 8px; display: flex; align-items: center; gap: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .audio-item audio { flex: 1; height: 36px; }

        .notes-grid { column-count: 3; column-gap: 20px; }
        .note-card { break-inside: avoid; background: #fff9c4; padding: 20px; border-radius: 0 0 10px 0; box-shadow: 2px 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; font-size: 0.95rem; line-height: 1.5; }
        .note-header { display: flex; justify-content: space-between; border-bottom: 1px solid rgba(0,0,0,0.1); padding-bottom: 8px; margin-bottom: 10px; }
        .note-author { font-weight: bold; color: #d35400; }
        
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .btn-zip { background: #333; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px; }
        .btn-zip:hover { background: #555; }

        @media (max-width: 768px) {
            .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .media-img-box { height: 130px; }
            .notes-grid { column-count: 1; }
            .audio-item { flex-direction: column; align-items: flex-start; }
            .audio-item audio { width: 100%; margin-top: 5px; }
            .top-header h1 { font-size: 1rem; }
            .tab-btn { padding: 8px 15px; font-size: 0.85rem; }
        }
    </style>
</head>
<body>

    <header class="top-header">
        <h1>🌿 Anı Paneli</h1>
        <a href="?logout=1" class="logout-btn">Çıkış Yap</a>
    </header>

    <div class="container">
        
        <div class="stats-container">
            <div class="stat-card c-green">
                <span class="stat-number"><?= $countPhoto ?></span>
                <span class="stat-label">Fotoğraf & Video</span>
            </div>
            <div class="stat-card c-orange">
                <span class="stat-number"><?= $countAudio ?></span>
                <span class="stat-label">Sesli Mesaj</span>
            </div>
            <div class="stat-card c-yellow">
                <span class="stat-number"><?= $countNote ?></span>
                <span class="stat-label">Notlar</span>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="openTab('tab-media')">Fotoğraflar</button>
            <button class="tab-btn" onclick="openTab('tab-audio')">Ses Kayıtları</button>
            <button class="tab-btn" onclick="openTab('tab-notes')">Notlar</button>
        </div>

        <div id="tab-media" class="tab-content active">
            <div class="section-header">
                <h3>Galeri</h3>
                <a href="download_zip.php?type=media" class="btn-zip">📥 Tümünü İndir (.zip)</a>
            </div>
            
            <div class="gallery-grid">
                <?php foreach($photos as $item): $path = '../' . $item['file_path']; ?>
                    <div class="media-card">
                        <div class="media-img-box">
                            <?php if($item['file_type'] == 'image'): ?>
                                <a href="<?= $path ?>" target="_blank"><img src="<?= $path ?>" loading="lazy"></a>
                            <?php else: ?>
                                <video controls src="<?= $path ?>"></video>
                            <?php endif; ?>
                        </div>
                        <div class="media-info">
                            <div class="guest-name"><?= htmlspecialchars($item['guest_name']) ?></div>
                            <span class="date-time"><?= date('d.m H:i', strtotime($item['created_at'])) ?></span>
                            <a href="<?= $path ?>" download class="download-link">⬇</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($photos)) echo "<p style='text-align:center; width:100%;'>Henüz paylaşılan fotoğraf yok.</p>"; ?>
            </div>
        </div>

        <div id="tab-audio" class="tab-content">
            <div class="section-header">
                <h3>Ses Kayıtları</h3>
                <a href="download_zip.php?type=audio" class="btn-zip">📥 Sesleri İndir (.zip)</a>
            </div>
            <div class="audio-list">
                <?php foreach($audios as $audio): $path = '../' . $audio['file_path']; ?>
                    <div class="audio-item">
                        <div style="font-size: 1.4rem;">🎤</div>
                        <div style="flex:1;">
                            <div style="font-weight:bold;"><?= htmlspecialchars($audio['guest_name']) ?></div>
                            <small style="color:#888;"><?= date('d.m.Y H:i', strtotime($audio['created_at'])) ?></small>
                        </div>
                        <audio controls src="<?= $path ?>"></audio>
                        <a href="<?= $path ?>" download style="font-size:1.2rem; text-decoration:none; color:#555;">⬇</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="tab-notes" class="tab-content">
            <div class="notes-grid">
                <?php foreach($notes as $note): ?>
                    <div class="note-card">
                        <div class="note-header">
                            <span class="note-author"><?= htmlspecialchars($note['guest_name']) ?></span>
                            <small style="color:#999;"><?= date('d.m.Y', strtotime($note['created_at'])) ?></small>
                        </div>
                        <div><?= nl2br(htmlspecialchars($note['note_content'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <script>
        function openTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(div => div.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>