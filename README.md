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

## 📂 Dosya Yapısı

```text
nisan-anilar/
├── admin/
│   ├── index.php        # Yönetim paneli ve Dashboard
│   ├── download_zip.php # Toplu indirme scripti
│   ├── script.js 
│   ├── style.css 
│   └── (Yönetici işlemleri burada döner)
├── assets/
│   ├── css/             # Tüm projenin stil dosyası
│   ├── audio/           # Tüm projenin ses dosyası
│   ├── js/              # Tüm projenin js dosyası
│   └── img/             # background resimleri buraya yüklenir
├── uploads/             # Yüklenen dosyaların toplandığı alan
│   ├── audios/
│   ├── images/
│   └── videos/
├── db.php               # Veritabanı bağlantı ayarları
├── index.php            # Misafir giriş ekranı
├── menu.php             # Seçim menüsü (Foto/Ses/Not)
├── slayt.php            # Canlı slayt gösterisi sayfası
├── upload_media.php     # Fotoğraf/Video yükleme formu
├── upload_audio.php     # Ses kayıt arayüzü
├── upload_note.php      # Not yazma formu
└── success.php          # Gönderim tamamlanınca çıkan karşılama ekranı
