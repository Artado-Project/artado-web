# Artado - Açık Kaynak Yazılım Projesi

Artado, açık kaynak kodlu, özelleştirilebilir ve mahremiyetinize önem veren yazılım projeleri sunan bir platformdur.

## 🚀 Özellikler

- Modern ve responsive tasarım
- Karanlık/Aydınlık tema desteği
- Admin paneli ile içerik yönetimi
- Destekçi ve katkıda bulunanlar profilleri
- SEO optimizasyonu
- Mobil uyumlu arayüz

## 📋 Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache/Nginx web sunucusu
- mod_rewrite (Apache için)

## 🔧 Kurulum

1. **Projeyi klonlayın:**
   ```bash
   git clone https://github.com/Artado-Project/artado.git
   cd artado
   ```

2. **Veritabanını oluşturun:**
   - `database.sql` dosyasını MySQL'de çalıştırın
   - Veya phpMyAdmin üzerinden import edin

3. **Yapılandırma dosyasını ayarlayın:**
   ```bash
   cp .env.example .env
   ```
   `.env` dosyasını düzenleyerek veritabanı bilgilerinizi girin.

4. **Veritabanı bağlantısını yapılandırın:**
   `core/db.php` dosyasındaki veritabanı bilgilerini güncelleyin.

5. **Dosya izinlerini ayarlayın:**
   ```bash
   chmod 755 -R .
   ```

## 🔐 Varsayılan Admin Girişi

- **Kullanıcı Adı:** admin
- **Şifre:** admin123

⚠️ **ÖNEMLİ:** İlk girişten sonra şifrenizi değiştirin!

## 📁 Proje Yapısı

```
artado/
├── admin/              # Admin paneli
├── assest/             # CSS, JS ve görseller
├── core/               # Çekirdek dosyalar (config, db, functions)
├── includes/           # Header ve footer dosyaları
├── index.php           # Ana sayfa
├── katki.php           # Destekçiler sayfası
├── profile.php         # Profil detay sayfası
└── database.sql        # Veritabanı şeması
```

## 🎨 Tema Özelleştirme

Tema renkleri ve stilleri `assest/css/styles.css` dosyasındaki CSS değişkenleri ile özelleştirilebilir:

```css
:root {
    --bg-color: #1a1a1a;
    --text-color: #ffffff;
    --link-color: #3498db;
    /* ... */
}
```

## 🔒 Güvenlik

- Tüm kullanıcı girişleri temizlenir ve doğrulanır
- SQL injection koruması (PDO prepared statements)
- XSS koruması (htmlspecialchars)
- Session güvenliği

## 📝 Katkıda Bulunma

1. Bu repository'yi fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje açık kaynak kodludur ve özgür yazılım felsefesiyle geliştirilmiştir.

## 🌐 Bağlantılar

- [Artado Search](https://artadosearch.com)
- [Artado Developers](https://devs.artado.xyz)
- [Discord Topluluğu](https://discord.com/invite/WXCsr8zTN6)
- [GitHub](https://github.com/Artado-Project)

## 👥 Destekçiler

Projeye katkıda bulunan tüm destekçilerimize teşekkürler! Detaylar için [katki.php](https://artado.xyz/katki.php) sayfasını ziyaret edin.

## 📧 İletişim

Sorularınız için: [sxi@artadosearch.com](mailto:sxi@artadosearch.com)

---

**Artado** - Tutarlı, sade ve özgür yazılım.

