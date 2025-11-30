<?php
// admin/download_zip.php
require '../db.php';

$type = $_GET['type'] ?? 'media';
$zipFileName = "nisan_arsiv_" . date('d.m.Y_H-i') . ".zip";
$zip = new ZipArchive;

// Geçici dosyayı bir üst dizindeki (kök) uploads klasörüne oluşturalım (yazma izni garanti olsun)
$tempZipPath = "../uploads/" . $zipFileName;

if ($zip->open($tempZipPath, ZipArchive::CREATE) === TRUE) {
    
    if($type == 'media'){
        $query = "SELECT file_path FROM uploads WHERE file_type IN ('image', 'video')";
    } else {
        $query = "SELECT file_path FROM uploads WHERE file_type = 'audio'";
    }
    
    $files = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);
    
    $count = 0;
    foreach ($files as $file) {
        // Veritabanında 'uploads/resim.jpg' yazar ama biz admin'deyiz, o yüzden başına ../ ekliyoruz
        $realPath = "../" . $file;
        
        if(file_exists($realPath)){
            // Zip'in içine dosya adıyla ekle (klasör yolu olmadan)
            $zip->addFile($realPath, basename($file));
            $count++;
        }
    }
    $zip->close();
    
    if($count > 0) {
        // İndirme işlemini başlat
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipFileName);
        header('Content-Length: ' . filesize($tempZipPath));
        readfile($tempZipPath);
        
        // İş bitince sunucudan sil (yer kaplamasın)
        unlink($tempZipPath);
    } else {
        echo "İndirilecek dosya bulunamadı veya dosyalar sunucuda yok.";
        // Boş zip oluştuysa silelim
        if(file_exists($tempZipPath)) unlink($tempZipPath);
    }
} else {
    echo 'Zip dosyası oluşturulamadı. Klasör izinlerini kontrol et.';
}
?>