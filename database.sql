-- Artado Database Schema

CREATE DATABASE IF NOT EXISTS `artado_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `artado_db`;

-- Users Table (for Admin Panel)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Supporters Table
CREATE TABLE IF NOT EXISTS `supporters` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `image_url` VARCHAR(255),
    `github_link` VARCHAR(255),
    `description` TEXT,
    `contributions` TEXT, -- Bullet points or description
    `type` ENUM('supporter', 'developer', 'sponsor') DEFAULT 'supporter',
    `order_priority` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default admin (password: admin123)
INSERT IGNORE INTO `users` (`username`, `password`, `email`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@artado.xyz');

-- Insert existing supporters with descriptions and contributions
INSERT IGNORE INTO `supporters` (`name`, `slug`, `image_url`, `github_link`, `type`, `order_priority`, `description`, `contributions`) VALUES
('Ardatdev', 'ardatdev', 'https://avatars.githubusercontent.com/u/47920304?v=4', 'https://github.com/ardatdev', 'supporter', 10, 'Bu kişi Artado projesine değerli katkılarda bulunmuştur. Yazılım geliştirme konusundaki uzmanlığı ve yenilikçi fikirleriyle projeye önemli katkılar sağlamıştır.', 'Proje geliştirme ve kod iyileştirmeleri\nHata düzeltmeleri ve performans optimizasyonları\nYeni özellikler ve kullanıcı deneyimi geliştirmeleri'),
('Sxinar', 'sxinar', 'https://avatars.githubusercontent.com/u/130384567?v=4', 'https://github.com/sxinar', 'supporter', 9, 'Artado projesinin gelişimine tasarım ve kod desteği sağlamıştır.', 'Arayüz geliştirmeleri\nCSS optimizasyonları'),
('Çınar Yılmaz', 'cinaryilmaz', 'https://avatars.githubusercontent.com/u/79412062?v=4', 'https://github.com/cinaryilmaz', 'supporter', 8, 'Topluluk yönetimi ve teknik destek konularında katkıda bulunmuştur.', 'Topluluk desteği\nDökümantasyon hazırlama'),
('Yusif Atakishiyev', 'yusif-atakishiyev', 'https://avatars.githubusercontent.com/u/136915671?v=4', '#', 'supporter', 7, 'Proje sürecinde fikirleri ve testleriyle yer almıştır.', 'Beta test süreçleri\nHata raporları'),
('LinuxUsersLinuxMint', 'linuxuserslinuxmint', 'https://avatars.githubusercontent.com/u/143949134?v=4', '#', 'supporter', 6, 'Açık kaynak dünyasından projeye destek veren değerli bir katkıcı.', 'Açık kaynak dökümantasyonu'),
('Arda Akkaya', 'arda-akkaya', 'https://avatars.githubusercontent.com/u/179171041?v=4', '#', 'supporter', 5, 'İletişim ve organizasyon konularında destek vermiştir.', 'Proje tanıtımı'),
('kerimcan05', 'kerimcan05', 'https://avatars.githubusercontent.com/u/51877047?v=4', 'https://github.com/kerimcan05', 'developer', 10, 'Eklenti ve tema ekosisteminin gelişmesine katkı sağlayan geliştirici.', 'Özel eklenti geliştirme\nTema tasarımları'),
('Oyunlayıcı', 'oyunlayici', 'https://www.mc-tr.com/data/avatars/o/168/168877.jpg?1702035958', '#', 'sponsor', 10, 'Artado projesinin ana sponsorlarından biridir.', 'Altyapı desteği\nFinansal sponsorluk');
