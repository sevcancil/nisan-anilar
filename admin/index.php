<?php
session_start();
// db.php'nin bir üst dizinde olduğunu varsayarak
require '../db.php'; 

// Çıkış Yap
if(isset($_GET['logout'])){ 
    session_destroy(); 
    // Yönlendirme adresi, admin klasöründen ana dizine çıkıp oradaki admin.php'ye gider
    header("Location: ../admin.php"); 
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
            // Klasör içindeki ana dosyaya yönlendir
            header("Location: index.php"); 
            exit;
        } else {
            $error = "Yanlış şifre!";
        }
    }
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css" 
          integrity="sha512-eHbuBnmqN+c7sQjY1I40Yp6Qp6e4pXf7r5+2/bKkE2J6iQ9XQW2y5r3Zt1z5Y2X/b/rL3QpQd/rQ==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container login-container">
        <h2>Yönetici Girişi</h2>
        <form method="POST">
            <input type="password" name="password" placeholder="Şifre" required>
            <button type="submit" class="btn">Giriş</button>
            <?php if(isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        </form>
    </div>
</body>
</html>
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
    <link rel="stylesheet" href="style.css"> 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.css" 
          integrity="sha512-eHbuBnmqN+c7sQjY1I40Yp6Qp6e4pXf7r5+2/bKkE2J6iQ9XQW2y5r3Zt1z5Y2X/b/rL3QpQd/rQ==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <h1>Anı Yönetim Paneli</h1>
        <a href="?logout=1" class="btn logout-btn">Çıkış Yap</a>
        <div style="clear:both"></div>

        <div class="content-section">
            <h2>📸 Fotoğraflar ve Videolar 
                <a href="../download_zip.php?type=media" class="btn download-all-btn">Tümünü İndir (Zip)</a>
            </h2>
            <div class="gallery-grid">
                <?php 
                $i = 0;
                foreach($photos as $item): 
                    $i++;
                    $is_video = $item['file_type'] == 'video';
                    
                    // DOSYA YOLU DÜZELTİLDİ: "../" ile bir üst klasöre çıkıp uploads'ı buluyoruz.
                    $file_url = '../' . htmlspecialchars($item['file_path']);
                ?>
                    <div class="gallery-item">
                        <a href="<?= $file_url ?>" 
                           data-lightbox="nişan-galeri" 
                           data-title="Gönderen: <?= htmlspecialchars($item['guest_name']) ?> | Tarih: <?= $item['created_at'] ?>">
                            
                            <?php if($is_video): ?>
                                <video src="<?= $file_url ?>" muted preload="metadata"></video>
                            <?php else: ?>
                                <img src="<?= $file_url ?>" alt="Resim <?php echo $i; ?>">
                            <?php endif; ?>
                            <div class="media-info">
                                <span class="media-type-tag"><?= $is_video ? 'VIDEO' : 'FOTO' ?></span>
                                <span class="media-name-tag"><?= htmlspecialchars($item['guest_name']) ?></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="content-section">
            <h2>🎤 Ses Kayıtları 
                <a href="../download_zip.php?type=audio" class="btn download-all-btn">Tümünü İndir (Zip)</a>
            </h2>
            <?php foreach($audios as $audio): ?>
                <?php 
                    // DOSYA YOLU DÜZELTİLDİ: "../" ile bir üst klasöre çıkıp uploads'ı buluyoruz.
                    $audio_url = '../' . htmlspecialchars($audio['file_path']);
                ?>
                <div class="audio-item">
                    <p class="audio-meta">
                        Gönderen: <strong><?= !empty($audio['guest_name']) ? htmlspecialchars($audio['guest_name']) : 'Anonim' ?></strong>
                        | Tarih: <?= date('d.m.Y H:i', strtotime($audio['created_at'])) ?>
                    </p>
                    <audio controls src="<?= $audio_url ?>"></audio>
                    <a href="<?= $audio_url ?>" download class="download-link">⬇ İndir</a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="content-section">
            <h2>📜 Misafir Notları</h2>
            
            <div class="note-container">
                <?php 
                $i = 0;
                foreach($notes as $note): 
                    $i++;
                ?>
                    <div class="note-item" style="--i: <?php echo $i; ?>;">
                        <h4><?= !empty($note['guest_name']) ? htmlspecialchars($note['guest_name']) : 'Anonim' ?></h4>
                        <small><?= date('d.m.Y H:i', strtotime($note['created_at'])) ?></small>
                        <p><?= nl2br(htmlspecialchars($note['note_content'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/simplelightbox/2.14.2/simple-lightbox.min.js" 
            integrity="sha512-Vd7x+Q2mY1EwU6r8y+2/bKkE2J6iQ9XQW2y5r3Zt1z5Y2X/b/rL3QpQd/rQ==" 
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            
    <script src="script.js"></script>

</body>
</html>