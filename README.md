# Kampüs Etkinlik & Kulüp Yönetim Sistemi

Web Programlama dersi dönem projesi. PHP, MySQL, HTML, CSS ve JavaScript kullanılarak geliştirilmiş tam çalışan bir kampüs etkinlik yönetim sistemi.

## Özellikler

- Kullanıcı kayıt / giriş / çıkış
- Etkinlik listesi, arama, filtreleme, sıralama
- Etkinliğe başvuru ve kontenjan kontrolü
- Profilim sayfası ve başvuru iptali
- Admin paneli: kulüp/etkinlik CRUD, başvuru listesi, rapor ekranı
- Etkinlik afişi yükleme
- CSV dışa aktarma
- QR kod üretme

## Kurulum

1. Laragon veya XAMPP kurun ve başlatın
2. `kampus` klasörünü `www` dizinine kopyalayın
3. phpMyAdmin'de `kampus_db` adında veritabanı oluşturun
4. `veritabani.sql` dosyasını import edin
5. `http://localhost/kampus` adresine gidin

## Admin Girişi

- E-posta: admin@kampus.com
- Şifre: password

## Kullanılan Teknolojiler

- PHP 8
- MySQL (PDO + Prepared Statements)
- HTML5, CSS3, JavaScript
- QR Server API