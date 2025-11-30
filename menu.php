<?php
session_start();

// Veritabanı bağlantısı (Sayaçlar için şart)
require 'db.php';

if(!isset($_SESSION['guest_name'])){ header("Location: index.php"); exit; }

// --- SAYAÇ SORGULARI ---
$totalPhotos = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type IN ('image', 'video')")->fetchColumn();
$totalAudios = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type = 'audio'")->fetchColumn();
$totalNotes  = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type = 'note'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paylaşım Seçin</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* İstatistik Kutuları Tasarımı */
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 15px auto;
            max-width: 100%;
        }
        .stat-box {
            background: white;
            border: 2px solid #556B2F; /* Haki Çerçeve */
            border-radius: 10px;
            padding: 8px;
            flex: 1;
            text-align: center;
            color: #556B2F;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stat-number {
            display: block;
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        /* Butonları biraz daha belirginleştirelim */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Merhaba, <?php echo htmlspecialchars($_SESSION['guest_name']); ?> 🌿</h2>
        
        <div class="stats-container">
            <div class="stat-box">
                <span class="stat-number"><?= $totalPhotos ?></span>
                <span class="stat-label">Medya</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?= $totalAudios ?></span>
                <span class="stat-label">Ses</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?= $totalNotes ?></span>
                <span class="stat-label">Not</span>
            </div>
        </div>

        <p style="margin-bottom: 5px;">Ne paylaşmak istersin?</p>
        
        <div class="btn-group">
            <a href="upload_media.php" class="btn">📸 Fotoğraf / Video Yükle</a>
            <a href="upload_audio.php" class="btn">🎤 Ses Kaydı Bırak</a>
            <a href="upload_note.php" class="btn">📝 Not / Dilek Yaz</a>
        </div>
    </div>

    <audio id="bg-music" loop>
        <source src="assets/audio/background.mp3" type="audio/mpeg">
    </audio>

    <script>
    document.addEventListener("click", function () {
        var audio = document.getElementById("bg-music");

        if (audio.paused) {
            audio.play().then(() => {
                localStorage.setItem("musicPlaying", "true");
            }).catch(e => {
                console.log("Autoplay engellendi:", e);
            });
        }
    }, { once: true }); 
    </script>

</body>
</html>