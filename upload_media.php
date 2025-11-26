<?php
session_start();
require 'db.php';

if(!isset($_SESSION['guest_name'])){ header("Location: index.php"); exit; }

if(isset($_FILES['media'])){
    $total = count($_FILES['media']['name']);
    for( $i=0 ; $i < $total ; $i++ ) {
        $tmpFilePath = $_FILES['media']['tmp_name'][$i];
        if ($tmpFilePath != ""){
            $fileName = $_FILES['media']['name'][$i];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid() . "." . $ext;
            
            // Dosya tipini anla
            $allowed_img = ['jpg','jpeg','png','gif'];
            $allowed_vid = ['mp4','mov','avi'];
            
            $type = '';
            $folder = '';
            
            if(in_array(strtolower($ext), $allowed_img)){
                $type = 'image';
                $folder = 'uploads/images/';
            } elseif(in_array(strtolower($ext), $allowed_vid)){
                $type = 'video';
                $folder = 'uploads/videos/';
            }
            
            if($type != ''){
                if(move_uploaded_file($tmpFilePath, $folder . $newFileName)) {
                    $stmt = $pdo->prepare("INSERT INTO uploads (guest_name, file_type, file_path) VALUES (?, ?, ?)");
                    $stmt->execute([$_SESSION['guest_name'], $type, $folder . $newFileName]);
                }
            }
        }
    }
    header("Location: success.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fotoğraf/Video Yükle</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Anıları Paylaş</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="file" style="cursor:pointer; display:block; padding:30px; border:2px dashed var(--khaki); margin-bottom:20px; color: var(--khaki);">
                Dosyaları Seçmek İçin Dokun
                <input type="file" name="media[]" id="file" multiple accept="image/*,video/*" style="display:none;" onchange="document.getElementById('count').innerText = this.files.length + ' dosya seçildi'">
            </label>
            <p id="count"></p>
            <button type="submit" class="btn">Gönder</button>
            <a href="menu.php" style="color:var(--orange); text-decoration:none;">Geri Dön</a>
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