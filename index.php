<?php
session_start();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $_SESSION['guest_name'] = htmlspecialchars($_POST['name']);
    header("Location: menu.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sevcan & Eray Nişan Hatırası</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
    <h1>Sevcan & Eray</h1>
    <h4>Nişan Hatırası</h4>
    <p class="date-text">20.12.2025</p> 
    <p>Nişanımızdaki en güzel anları bizimle paylaşır mısınız?</p>
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