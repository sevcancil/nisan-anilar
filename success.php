<?php session_start(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teşekkürler</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1 style="color:var(--orange)">Teşekkürler!</h1>
        <p>Katılımınız ve anılarımıza ortak olduğunuz için teşekkürler.</p>
        <div style="font-size: 50px;">🌸</div>
        <br>
        <a href="menu.php" class="btn">Daha Fazla Anı Paylaş</a>
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