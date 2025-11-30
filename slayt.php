<?php
// slayt.php (Ana dizine kaydet)
require 'db.php';
// Tüm resim yollarını çekip JS dizisine aktaracağız
$images = $pdo->query("SELECT file_path FROM uploads WHERE file_type = 'image' ORDER BY id DESC")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canlı Akış 💖</title>
    <style>
        body { margin: 0; background: #000; overflow: hidden; display: flex; justify-content: center; align-items: center; height: 100vh; }
        #slide-img { 
            max-width: 100vw; 
            max-height: 100vh; 
            object-fit: contain; 
            animation: fadeIn 1s ease-in-out;
            box-shadow: 0 0 50px rgba(255,255,255,0.1);
        }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .loading { color: white; font-family: sans-serif; letter-spacing: 2px; }
    </style>
</head>
<body>

    <img id="slide-img" src="" alt="Slayt" style="display:none;">
    <div id="loading-text" class="loading">ANILAR YÜKLENİYOR...</div>

    <script>
        // PHP'den gelen veriyi JS dizisine çeviriyoruz
        let images = <?php echo json_encode($images); ?>;
        let currentIndex = 0;
        const imgElement = document.getElementById('slide-img');
        const loadingElement = document.getElementById('loading-text');

        function showNextImage() {
            if (images.length === 0) return;

            loadingElement.style.display = 'none';
            imgElement.style.display = 'block';

            // Animasyonu sıfırla (her resimde fade-in olsun)
            imgElement.style.animation = 'none';
            imgElement.offsetHeight; /* trigger reflow */
            imgElement.style.animation = 'fadeIn 1s ease-in-out';

            // Resmi güncelle
            imgElement.src = images[currentIndex];

            // Bir sonraki resme geç, sona geldiysek başa dön (Döngü Mantığı)
            currentIndex++;
            if (currentIndex >= images.length) {
                currentIndex = 0;
            }
        }

        // 5 saniyede bir değiştir
        setInterval(showNextImage, 5000);

        // Sayfa ilk açıldığında başlat
        if(images.length > 0) showNextImage();
        else loadingElement.innerHTML = "HENÜZ FOTOĞRAF YOK :)";

        // OPSİYONEL: Her 30 saniyede bir sayfayı yenile ki yeni gelen resimler listeye eklensin
        // (Daha profesyonel AJAX yöntemi var ama en kolayı budur)
        setTimeout(function(){
            window.location.reload();
        }, 30000); 

    </script>
</body>
</html>