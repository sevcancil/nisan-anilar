<?php
session_start();
require 'db.php';
if(!isset($_SESSION['guest_name'])){ header("Location: index.php"); exit; }

// AJAX ile gelen veriyi işle
if(isset($_FILES['audio_data'])){
    // Tarayıcıdan gelen dosya adını alalım, uzantısı .webm olacak
    $ext = pathinfo($_FILES['audio_data']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '.' . $ext; // Örneğin: .webm
    $folder = 'uploads/audios/';
    if(move_uploaded_file($_FILES['audio_data']['tmp_name'], $folder.$fileName)){
        $stmt = $pdo->prepare("INSERT INTO uploads (guest_name, file_type, file_path) VALUES (?, 'audio', ?)");
        $stmt->execute([$_SESSION['guest_name'], $folder.$fileName]);
        echo "success";
    } else {
        echo "error";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ses Kaydı</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Ses Kaydı Bırak</h2>
        <p>Mikrofon butonuna bas ve dileklerini kaydet.</p>

        <div id="controls-initial">
            <button id="recordBtn" class="btn" style="background:var(--khaki)">🔴 Kaydı Başlat</button>
        </div>

        <div id="controls-recording" style="display:none;">
            <button id="stopBtn" class="btn" style="background:var(--orange)">⬛ Kaydı Bitir</button>
            <p id="status">Kaydediliyor...</p>
        </div>
        
        <div id="controls-preview" style="display:none;">
            <audio id="audioPlayer" controls style="width:100%; margin-bottom: 20px;"></audio>
            <button id="sendBtn" class="btn" style="background:var(--dark-green)">✅ Gönder</button>
            <button id="reRecordBtn" class="btn" style="background:var(--orange)">❌ Tekrar Kaydet</button>
            <p id="status-preview"></p>
        </div>
        
        <a href="menu.php" style="color:var(--khaki); text-decoration:none;">Geri Dön</a>
    </div>

    <script>
        // DOM Elementlerini al
        const recordBtn = document.getElementById("recordBtn");
        const stopBtn = document.getElementById("stopBtn");
        const sendBtn = document.getElementById("sendBtn");
        const reRecordBtn = document.getElementById("reRecordBtn");
        const audioPlayer = document.getElementById("audioPlayer");
        const statusText = document.getElementById("status");
        const statusPreviewText = document.getElementById("status-preview");
        
        const controlsInitial = document.getElementById("controls-initial");
        const controlsRecording = document.getElementById("controls-recording");
        const controlsPreview = document.getElementById("controls-preview");

        let mediaRecorder;
        let audioChunks = [];
        let audioBlob;
        
        // Mobil ve tarayıcı uyumluluğu için en iyi formatı seçelim
        const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 
                         'audio/webm;codecs=opus' : 
                         'audio/webm';

        function resetUI() {
            controlsInitial.style.display = "block";
            controlsRecording.style.display = "none";
            controlsPreview.style.display = "none";
            audioChunks = [];
            audioBlob = null;
            statusText.innerText = "Kaydediliyor...";
            statusPreviewText.innerText = "";
            // Her sıfırlamada eski URL'i temizleyelim
            if(audioPlayer.src) {
                URL.revokeObjectURL(audioPlayer.src);
                audioPlayer.src = '';
            }
        }

        // 1. KAYDI BAŞLAT
        recordBtn.addEventListener("click", async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                // Yeni MediaRecorder oluştur
                mediaRecorder = new MediaRecorder(stream, { mimeType: mimeType });
                
                // Olay dinleyicileri SADECE bir kez burada tanımlayalım
                mediaRecorder.addEventListener("dataavailable", event => {
                    audioChunks.push(event.data);
                });
                
                // KAYDI BİTİRME (Stop) Olayı - En kritik kısım
                mediaRecorder.addEventListener("stop", () => {
                    // Kayıt bitti, stream'i kapat (bu, mikrofon ışığını söndürür)
                    stream.getTracks().forEach(track => track.stop());

                    // Ses parçalarından Blob'u oluştur
                    audioBlob = new Blob(audioChunks, { type: mimeType });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    
                    audioPlayer.src = audioUrl;

                    controlsRecording.style.display = "none";
                    controlsPreview.style.display = "block";
                    statusPreviewText.innerText = "Kaydınızı dinleyebilir veya tekrar kaydedebilirsiniz.";
                });

                // Kaydı başlat
                mediaRecorder.start();

                controlsInitial.style.display = "none";
                controlsRecording.style.display = "block";
                statusText.innerText = "Kaydediliyor... Kaydı bitirmek için bas.";


            } catch (err) {
                alert("Mikrofon izni verilmedi veya bir hata oluştu: " + err);
                resetUI();
            }
        });

        // 2. KAYDI BİTİR BUTONU
        stopBtn.addEventListener("click", () => {
            // Sadece durdurma komutunu gönderiyoruz, kalan işlemler stop olayında yapılacak.
            mediaRecorder.stop();
        });
        
        // 3. KAYDI GÖNDER (Bu kısım aynı kaldı)
        sendBtn.addEventListener("click", () => {
            if (!audioBlob) return;

            const formData = new FormData();
            const file = new File([audioBlob], 'audio_record.webm', { type: mimeType });
            formData.append("audio_data", file);

            statusPreviewText.innerText = "Yükleniyor... Lütfen bekleyin.";
            
            fetch("upload_audio.php", { 
                method: "POST", 
                body: formData 
            })
            .then(response => response.text())
            .then(result => {
                if(result.trim() == "success"){
                    window.location.href = "success.php";
                } else {
                    alert("Yükleme sırasında hata oluştu. Tekrar deneyin.");
                    resetUI();
                }
            })
            .catch(error => {
                 alert("Ağ hatası: " + error);
                 resetUI();
            });
        });

        // 4. TEKRAR KAYDET
        reRecordBtn.addEventListener("click", () => {
            resetUI();
        });

        // Sayfa yüklendiğinde başlangıç durumunu ayarla
        document.addEventListener('DOMContentLoaded', resetUI);
    </script>
</body>
</html>