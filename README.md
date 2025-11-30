# 🌿 Nişan Hatırası - Dijital Anı Platformu

Bu proje, nişan/düğün organizasyonlarında misafirlerin **fotoğraf, video, ses kaydı ve yazılı notlarını** paylaşabilmeleri için geliştirilmiş, mobil uyumlu bir web uygulamasıdır.

Mekan içerisindeki büyük ekranlar için **Canlı Slayt Akışı** ve organizasyon sahipleri için gelişmiş bir **Yönetim Paneli** içerir.

![Project Status](https://img.shields.io/badge/Status-Completed-success)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![Responsive](https://img.shields.io/badge/Design-Responsive-orange)

## 🚀 Özellikler

### 👤 Misafir Arayüzü
* **Kolay Giriş:** Sadece isim soyisim ile hızlı giriş.
* **Çoklu Medya Desteği:** Fotoğraf ve Video yükleme.
* **Sesli Mesaj:** Tarayıcı üzerinden direkt ses kaydı ve upload.
* **Dijital Anı Defteri:** Yazılı not ve dilek paylaşımı.
* **Sosyal Kanıt:** Ana ekranda toplam paylaşılan anı sayaçları.
* **Mobil Uyumlu Tasarım:** Haki yeşil ve turuncu tonlarında minimalist arayüz.
* **Arka Plan Müziği:** Sayfa gezintisi boyunca çalan otomatik müzik.

### 🛠 Yönetim Paneli (Admin)
* **Dashboard:** Toplam fotoğraf, ses ve not istatistikleri.
* **Sekmeli Yapı:** İçerikleri türe göre (Medya, Ses, Not) filtreleme.
* **Toplu İndirme:** Tüm anıları veya sadece sesleri tek tıkla `.zip` olarak indirme.
* **Önizleme:** Fotoğrafları yeni sekmede açma, sesleri panelden dinleme.
* **Güvenlik:** Şifreli giriş sistemi.

### 📺 Canlı Slayt (Mekan Ekranı)
* **Otomatik Akış:** Yeni yüklenen fotoğrafları sayfa yenilemeye gerek kalmadan algılar.
* **Döngü Modu:** Fotoğraf yüklenmediğinde mevcut havuzdan rastgele gösterime devam eder.
* **Full Screen:** Projektör ve TV ekranları için optimize edilmiştir.

---

## ⚙️ Kurulum (Installation)

Projeyi çalıştırmak için aşağıdaki adımları takip edin.

### 1. Veritabanı Kurulumu (SQL)
Hosting panelinizde (phpMyAdmin) boş bir SQL penceresi açın ve aşağıdaki kodları çalıştırın:

```sql
/* Veritabanını Oluştur */
CREATE DATABASE IF NOT EXISTS nisan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nisan_db;

/* Yüklemeler Tablosu */
CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type ENUM('image', 'video', 'audio', 'note') NOT NULL,
    note_content TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* Yönetici Ayarları Tablosu */
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_pass VARCHAR(255) NOT NULL
);

/* Varsayılan Admin Şifresi: 1234 */
/* Not: Şifre MD5 ile hashlenmiştir. */
INSERT INTO settings (admin_pass) VALUES (MD5('1234')); 
```
### 1. Veritabanı Bağlantısı
db.php dosyasını açarak sunucu bilgilerinizi girin:

```
$host = 'localhost';
$dbname = 'nisan_db';
$user = 'root'; /* Sunucu kullanıcı adı */
$pass = '';     /* Sunucu şifresi */
```
3. Klasör İzinleri
uploads klasörü ve alt klasörlerinin yazılabilir olduğundan emin olun (Linux sunucular için):
```
chmod -R 777 uploads/
```

## 📂 Dosya Yapısı
```text
nisan-anilar/
├── admin/
│   ├── index.php        # Yönetim paneli ve Dashboard
│   ├── download_zip.php # Toplu indirme scripti
│   ├── script.js 
│   └── style.css 
├── assets/
│   ├── css/             # Tüm projenin stil dosyası
│   ├── audio/           # Tüm projenin ses dosyası
│   ├── js/              # Tüm projenin js dosyası
│   └── img/             # Background resimleri
├── uploads/             # Yüklenen dosyaların toplandığı alan
│   ├── audios/
│   ├── images/
│   ├── videos/
├── db.php               # Veritabanı bağlantı ayarları
├── index.php            # Misafir giriş ekranı
├── menu.php             # Seçim menüsü (Foto/Ses/Not)
├── slayt.php            # Canlı slayt gösterisi sayfası
├── upload_media.php     # Fotoğraf/Video yükleme formu
├── upload_audio.php     # Ses kayıt arayüzü
├── upload_note.php      # Not yazma formu
└── success.php          # Başarılı işlem sonrası karşılama ekranı
```
Geliştirici: Sevcan

📅 Tarih: 30.11.2025
