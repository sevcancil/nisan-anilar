<?php
session_start();
// Veritabanı bağlantısını ekliyoruz (Sayaçlar için şart)
require 'db.php'; 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_SESSION['guest_name'] = htmlspecialchars($_POST['name']);
    header("Location: menu.php");
    exit;
}

// --- SAYAÇ SORGULARI ---
// Hata oluşursa sayfa patlamasın diye try-catch bloğu da eklenebilir ama
// basitlik adına direkt sorguları yazıyorum:
$totalPhotos = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type IN ('image', 'video')")->fetchColumn();
$totalAudios = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type = 'audio'")->fetchColumn();
$totalNotes  = $pdo->query("SELECT COUNT(*) FROM uploads WHERE file_type = 'note'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sevcan & Eray Nişan Hatırası</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Sayaçlar için minik ekstra CSS */
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px auto;
            max-width: 400px;
        }
        .stat-box {
            background: white;
            border: 2px solid #556B2F; /* Haki Çerçeve */
            border-radius: 12px;
            padding: 10px;
            flex: 1;
            text-align: center;
            color: #556B2F;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        .stat-number {
            display: block;
            font-size: 1.4rem;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Sevcan & Eray</h1>
    <h4>Nişan Hatırası 🌿</h4>
    <p class="date-text">20.12.2025</p> 
    
    <p>Nişanımızdaki en güzel anları bizimle paylaşır mısınız?<br>
        <br>Telefonunuzdan çektiğiniz fotoğrafları ve videoları bizimle paylaşabilir, bize sesli mesaj ve not bırakabilirsiniz.
    </p>

    <div class="stats-container">
        <div class="stat-box">
            <span class="stat-number"><?= $totalPhotos ?></span>
            <span class="stat-label">Fotoğraf</span>
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

    <p style="font-size: 0.9rem; margin-top: 10px;">
        Nişan törenimize katıldığınız için teşekkürler. İyi ki varsınız!
    </p>

    <form method="POST">
        <input type="text" name="name" placeholder="Adınız ve Soyadınız" required>
        <button type="submit" class="btn" onclick="playMusic()">Giriş Yap ve Paylaş</button>
    </form>
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
}, { once: true }); // sadece ilk tıklamada çalışır
</script>

</body>
</html>