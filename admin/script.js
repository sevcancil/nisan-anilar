document.addEventListener('DOMContentLoaded', function() {
    
    // Lightbox'ı, '.gallery-grid' içindeki **TÜM** 'a' etiketleri üzerinde başlat.
    // Bu seçici, sadece galeri elemanlarını seçtiği için gezilebilir grup oluşturur.
    var lightbox = new SimpleLightbox('.gallery-grid a', {
        captionAttribute: 'data-title', 
        loop: true, 
        
        // Bu ayar SimpleLightbox'a video dosyalarını da açması gerektiğini söyler.
        // Senin projen için gerekli olan video formatlarını ekledim.
        fileExt: 'png|jpg|jpeg|gif|webm|mp4', 
    });
});