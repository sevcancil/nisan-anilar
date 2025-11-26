<?php
session_start();
if(!isset($_SESSION['guest_name'])){ header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paylaşım Seçin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Merhaba, <?php echo $_SESSION['guest_name']; ?></h2>
        <p>Ne paylaşmak istersin?</p>
        
        <a href="upload_media.php" class="btn">📸 Fotoğraf / Video Yükle</a>
        <a href="upload_audio.php" class="btn">🎤 Ses Kaydı Bırak</a>
        <a href="upload_note.php" class="btn">📝 Not / Dilek Yaz</a>
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