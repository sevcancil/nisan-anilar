<?php
session_start();
require 'db.php';
if(!isset($_SESSION['guest_name'])){ header("Location: index.php"); exit; }

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $note = htmlspecialchars($_POST['note']);
    $stmt = $pdo->prepare("INSERT INTO uploads (guest_name, file_type, note_content) VALUES (?, 'note', ?)");
    $stmt->execute([$_SESSION['guest_name'], $note]);
    header("Location: success.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Yaz</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Güzel Dileklerin</h2>
        <form method="POST">
            <textarea name="note" rows="6" placeholder="Bize bir anı veya dilek yaz..." required></textarea>
            <button type="submit" class="btn">Notu Kaydet</button>
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