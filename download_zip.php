<?php
require 'db.php';
$type = $_GET['type'] ?? 'media';
$zipFileName = "arsiv_" . date('Ymd_His') . ".zip";
$zip = new ZipArchive;

if ($zip->open('uploads/zips/' . $zipFileName, ZipArchive::CREATE) === TRUE) {
    if($type == 'media'){
        $query = "SELECT file_path FROM uploads WHERE file_type IN ('image', 'video')";
    } else {
        $query = "SELECT file_path FROM uploads WHERE file_type = 'audio'";
    }
    
    $files = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($files as $file) {
        if(file_exists($file)){
            $zip->addFile($file, basename($file));
        }
    }
    $zip->close();
    
    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename='.$zipFileName);
    header('Content-Length: ' . filesize('uploads/zips/' . $zipFileName));
    readfile('uploads/zips/' . $zipFileName);
    // İndirdikten sonra zip dosyasını silebilirsin (isteğe bağlı)
    unlink('uploads/zips/' . $zipFileName);
} else {
    echo 'Zip oluşturulamadı.';
}
?>