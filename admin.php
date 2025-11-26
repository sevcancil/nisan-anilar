<?php
session_start();
require 'db.php';

// Çıkış Yap
if(isset($_GET['logout'])){ session_destroy(); header("Location: admin.php"); exit; }

// Giriş Kontrolü
if(!isset($_SESSION['admin_logged_in'])){
    if(isset($_POST['password'])){
        $pass = md5($_POST['password']);
        $stmt = $pdo->prepare("SELECT * FROM settings WHERE admin_pass = ?");
        $stmt->execute([$pass]);
        if($stmt->rowCount() > 0){
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin.php"); exit;
        } else {
            $error = "Yanlış şifre!";
        }
    }
?>
    <link rel="stylesheet" href="assets/css/style.css">
    <div class="container">
        <h2>Yönetici Girişi</h2>
        <form method="POST">
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit" class="btn">Giriş</button>
            <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        </form>
    </div>
<?php
    exit;
}

// Admin İçeriği Buradan Başlıyor
// Verileri Çek
$photos = $pdo->query("SELECT * FROM uploads WHERE file_type IN ('image', 'video') ORDER BY id DESC")->fetchAll();
$audios = $pdo->query("SELECT * FROM uploads WHERE file_type = 'audio' ORDER BY id DESC")->fetchAll();
$notes = $pdo->query("SELECT * FROM uploads WHERE file_type = 'note' ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container { max-width: 1200px; width: 95%; }
        .section-title { border-bottom: 2px solid var(--khaki); padding-bottom: 10px; margin-top: 40px; }
        .download-link { font-size: 0.8rem; color: var(--orange); text-decoration: none; display:block; margin-top:5px;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Anı Yönetim Paneli</h1>
        <a href="?logout=1" style="color:red; float:right;">Çıkış Yap</a>
        <div style="clear:both"></div>

        <h3 class="section-title">📸 Fotoğraflar ve Videolar 
            <a href="download_zip.php?type=media" class="btn" style="width:auto; display:inline-block; padding:5px 20px; font-size:0.8rem;">Tümünü İndir (Zip)</a>
        </h3>
        <div class="gallery">
            <?php foreach($photos as $item): ?>
                <div class="media-item">
                    <?php if($item['file_type'] == 'image'): ?>
                        <a href="<?= $item['file_path'] ?>" target="_blank"><img src="<?= $item['file_path'] ?>"></a>
                    <?php else: ?>
                        <video controls src="<?= $item['file_path'] ?>"></video>
                    <?php endif; ?>
                    <p><strong><?= htmlspecialchars($item['guest_name']) ?></strong></p>
                    <a href="<?= $item['file_path'] ?>" download class="download-link">⬇ İndir</a>
                </div>
            <?php endforeach; ?>
        </div>

        <h3 class="section-title">🎤 Ses Kayıtları 
             <a href="download_zip.php?type=audio" class="btn" style="width:auto; display:inline-block; padding:5px 20px; font-size:0.8rem;">Tümünü İndir (Zip)</a>
        </h3>
        <div style="display:flex; flex-wrap:wrap; gap:10px;">
            <?php foreach($audios as $audio): ?>
                <div class="media-item" style="width:300px;">
                    <audio controls src="<?= $audio['file_path'] ?>" style="width:100%"></audio>
                    <p><strong><?= htmlspecialchars($audio['guest_name']) ?></strong> - <?= $audio['created_at'] ?></p>
                    <a href="<?= $audio['file_path'] ?>" download class="download-link">⬇ İndir</a>
                </div>
            <?php endforeach; ?>
        </div>

        <h3 class="section-title">📝 Anı Defteri</h3>
        <div class="notebook-container">
            <?php foreach($notes as $note): ?>
                <div class="note-card">
                    <h4><?= htmlspecialchars($note['guest_name']) ?></h4>
                    <small><?= $note['created_at'] ?></small>
                    <hr style="border:0; border-top:1px dashed #ccc;">
                    <p><?= nl2br(htmlspecialchars($note['note_content'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>