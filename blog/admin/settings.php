<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_admin();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Logo/Favicon Upload
    $upload_dir_abs = dirname(__DIR__) . '/assets/img/';
    if (!is_dir($upload_dir_abs)) mkdir($upload_dir_abs, 0777, true);

    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION));
        $new_name = 'logo_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_dir_abs . $new_name)) {
            update_setting('site_logo', 'assets/img/' . $new_name);
        }
    }

    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION));
        $new_name = 'favicon_' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $upload_dir_abs . $new_name)) {
            update_setting('site_favicon', 'assets/img/' . $new_name);
        }
    }

    update_setting('site_title', $_POST['site_title']);
    update_setting('site_slogan', $_POST['site_slogan']);
    update_setting('hero_text', $_POST['hero_text']);
    update_setting('primary_button_text', $_POST['primary_button_text']);
    update_setting('primary_button_url', $_POST['primary_button_url']);
    update_setting('secondary_button_text', $_POST['secondary_button_text']);
    update_setting('secondary_button_url', $_POST['secondary_button_url']);
    
    // Dinamik Menü İşleme
    if (isset($_POST['menu_title'])) {
        $new_menu = [];
        foreach ($_POST['menu_title'] as $index => $title) {
            if (!empty(trim($title))) {
                $new_menu[] = [
                    'title' => trim($title), 
                    'url' => trim($_POST['menu_url'][$index] ?? '')
                ];
            }
        }
        update_setting('menu_links', json_encode($new_menu));
    }

    update_setting('seo_description', $_POST['seo_description'] ?? '');
    update_setting('seo_keywords', $_POST['seo_keywords']);
    update_setting('custom_head_code', $_POST['custom_head_code'] ?? '');
    update_setting('custom_footer_code', $_POST['custom_footer_code'] ?? '');
    update_setting('maintenance_mode', isset($_POST['maintenance_mode']) ? '1' : '0');
    update_setting('global_comments', isset($_POST['global_comments']) ? '1' : '0');
    update_setting('show_author_bio', isset($_POST['show_author_bio']) ? '1' : '0');
    update_setting('accent_color', $_POST['accent_color']);
    update_setting('border_radius', $_POST['border_radius']);
    
    // Genişletilmiş Sosyal Medya İşleme
    $socials = [
        'github' => $_POST['social_github'] ?? '',
        'email' => $_POST['social_email'] ?? '',
        'twitter' => $_POST['social_twitter'] ?? '',
        'linkedin' => $_POST['social_linkedin'] ?? '',
        'instagram' => $_POST['social_instagram'] ?? '',
        'youtube' => $_POST['social_youtube'] ?? ''
    ];
    update_setting('social_links', json_encode($socials));
    
    $message = "Ayarlar başarıyla güncellendi.";
}

$site_title = get_setting('site_title');
$site_slogan = get_setting('site_slogan');
$hero_text = get_setting('hero_text');
$primary_btn_text = get_setting('primary_button_text');
$primary_btn_url = get_setting('primary_button_url');
$secondary_btn_text = get_setting('secondary_button_text');
$secondary_btn_url = get_setting('secondary_button_url');
$menu_links_json = get_setting('menu_links', '[]');
$seo_description = get_setting('seo_description');
$seo_keywords = get_setting('seo_keywords');
$custom_head = get_setting('custom_head_code', '');
$custom_footer = get_setting('custom_footer_code', '');
$maintenance = get_setting('maintenance_mode');
$global_comments = get_setting('global_comments', '1');
$show_author_bio = get_setting('show_author_bio', '1');
$accent = get_setting('accent_color', '#2c2c2c');
$radius = get_setting('border_radius', '20px');
$social_links = json_decode(get_setting('social_links', '{}'), true);

admin_header('Site Ayarları');
admin_sidebar('settings');
?>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50 min-h-screen text-slate-800">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Site Ayarları</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Küresel site tercihlerinizi, görünümü ve kimliğinizi yapılandırın.</p>
        </div>
        <button type="submit" form="settings-form" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold uppercase tracking-widest text-xs transition-all shadow-sm shadow-blue-600/30 self-start sm:self-auto">
            Değişiklikleri Kaydet
        </button>
    </div>

    <?php if($message): ?>
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 mb-8 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <span>✨</span> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="settings-form" enctype="multipart/form-data" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Site Kimliği -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 md:p-8 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 border-b border-slate-100 pb-3">Site Kimliği</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Site Başlığı</label>
                        <input type="text" name="site_title" value="<?php echo e($site_title); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Site Sloganı</label>
                        <input type="text" name="site_slogan" value="<?php echo e($site_slogan); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Site Logosu</label>
                        <input type="file" name="site_logo" class="text-[10px] w-full text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <img src="../<?php echo get_setting('site_logo', 'assets/img/logo.png'); ?>" class="h-8 mt-3 object-contain">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Favicon</label>
                        <input type="file" name="site_favicon" class="text-[10px] w-full text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                        <img src="../<?php echo get_setting('site_favicon', 'assets/img/logo.png'); ?>" class="w-6 h-6 mt-3 object-contain">
                    </div>
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label for="maintenance_mode" class="text-xs font-bold text-slate-700 cursor-pointer select-none">Bakım Modu</label>
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" <?php echo $maintenance === '1' ? 'checked' : ''; ?> class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    </div>
                    <div class="flex items-center justify-between">
                        <label for="global_comments" class="text-xs font-bold text-slate-700 cursor-pointer select-none">Global Yorumlar</label>
                        <input type="checkbox" name="global_comments" id="global_comments" <?php echo $global_comments === '1' ? 'checked' : ''; ?> class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    </div>
                    <div class="flex items-center justify-between">
                        <label for="show_author_bio" class="text-xs font-bold text-slate-700 cursor-pointer select-none">Yazar Biyografisi Göster</label>
                        <input type="checkbox" name="show_author_bio" id="show_author_bio" <?php echo $show_author_bio === '1' ? 'checked' : ''; ?> class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Görünüm & Tema -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 md:p-8 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 border-b border-slate-100 pb-3">Görünüm ve Tema</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Vurgu Rengi (Accent Color)</label>
                        <div class="flex items-center space-x-4">
                            <input type="color" name="accent_color" id="accent_color_input" value="<?php echo e($accent); ?>" class="w-12 h-12 p-1 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer">
                            <span class="text-xs font-mono font-bold text-slate-600" id="accent_color_text"><?php echo e($accent); ?></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Kenar Yumuşaklığı (Border Radius)</label>
                        <select name="border_radius" id="border_radius_select" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all cursor-pointer">
                            <option value="0px" <?php echo $radius === '0px' ? 'selected' : ''; ?>>Keskin (0px)</option>
                            <option value="4px" <?php echo $radius === '4px' ? 'selected' : ''; ?>>Profesyonel (4px)</option>
                            <option value="20px" <?php echo $radius === '20px' ? 'selected' : ''; ?>>Yumuşak (20px)</option>
                        </select>
                    </div>
                </div>

                <!-- Canlı Önizleme Kartı -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-2">Canlı Önizleme</label>
                    <div id="live-preview-box" class="p-4 border border-slate-200 bg-slate-50 transition-all space-y-3" style="border-radius: <?php echo e($radius); ?>;">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">Tema Önizleme Kartı</span>
                            <span class="w-3 h-3 rounded-full inline-block" id="preview-accent-dot" style="background-color: <?php echo e($accent); ?>;"></span>
                        </div>
                        <p class="text-[11px] text-slate-500">Seçtiğiniz renk ve köşe yarıçapı değerlerinin arayüze nasıl yansıyacağını buradan görebilirsiniz.</p>
                        <button type="button" id="preview-btn" class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-white transition-all" style="background-color: <?php echo e($accent); ?>; border-radius: <?php echo e($radius); ?>;">Örnek Buton</button>
                    </div>
                </div>
            </div>

            <!-- Hero Alanı -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 md:p-8 space-y-6 lg:col-span-2">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 border-b border-slate-100 pb-3">Hero Alanı (Ana Sayfa Başlığı)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Hero Metni</label>
                        <textarea name="hero_text" rows="5" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all"><?php echo e($hero_text); ?></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">1. Buton Metni</label>
                            <input type="text" name="primary_button_text" value="<?php echo e($primary_btn_text); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">1. Buton URL</label>
                            <input type="text" name="primary_button_url" value="<?php echo e($primary_btn_url); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">2. Buton Metni</label>
                            <input type="text" name="secondary_button_text" value="<?php echo e($secondary_btn_text); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">2. Buton URL</label>
                            <input type="text" name="secondary_button_url" value="<?php echo e($secondary_btn_url); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO & Sosyal Medya -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 md:p-8 space-y-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 border-b border-slate-100 pb-3">SEO ve Sosyal Medya</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">SEO Anahtar Kelimeler (Keywords)</label>
                        <input type="text" name="seo_keywords" value="<?php echo e($seo_keywords); ?>" placeholder="blog, teknoloji, yazılım" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-2">Sosyal Medya Hesapları</label>
                        <div class="grid grid-cols-2 gap-3">
                            <?php 
                            $socials_list = ['github' => 'GitHub', 'email' => 'E-posta', 'twitter' => 'X (Twitter)', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'youtube' => 'YouTube'];
                            foreach ($socials_list as $key => $label): 
                            ?>
                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1"><?php echo $label; ?></span>
                                <input type="text" name="social_<?php echo $key; ?>" value="<?php echo e($social_links[$key] ?? ''); ?>" placeholder="<?php echo $label; ?> URL" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dinamik Navigasyon Menüsü -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 md:p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">Navigasyon Menüsü</h3>
                    <button type="button" onclick="addNavItem()" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors">+ Yeni Link Ekle</button>
                </div>
                
                <div id="nav-container" class="space-y-3">
                    <?php 
                    $menu_items = json_decode($menu_links_json, true) ?: [['title' => '', 'url' => '']];
                    foreach ($menu_items as $item): 
                    ?>
                    <div class="nav-item flex gap-2 items-center">
                        <input type="text" name="menu_title[]" value="<?php echo e($item['title'] ?? ''); ?>" placeholder="Menü Adı" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                        <input type="text" name="menu_url[]" value="<?php echo e($item['url'] ?? ''); ?>" placeholder="URL (örn: /hakkimda)" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors text-xs font-bold">×</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-[10px] text-slate-400">Menü öğelerinin sırasını yukarıdaki alanlardan yönetebilir, artı butonunu kullanarak yeni linkler ekleyebilirsiniz.</p>
            </div>

        </div>
    </form>
</main>

<script>
    // Canlı Önizleme Etkileşimi
    const accentInput = document.getElementById('accent_color_input');
    const accentText = document.getElementById('accent_color_text');
    const radiusSelect = document.getElementById('border_radius_select');
    const previewBox = document.getElementById('live-preview-box');
    const previewBtn = document.getElementById('preview-btn');
    const previewDot = document.getElementById('preview-accent-dot');

    accentInput.addEventListener('input', (e) => {
        const val = e.target.value;
        accentText.textContent = val;
        previewDot.style.backgroundColor = val;
        previewBtn.style.backgroundColor = val;
    });

    radiusSelect.addEventListener('change', (e) => {
        const val = e.target.value;
        previewBox.style.borderRadius = val;
        previewBtn.style.borderRadius = val;
    });

    // Dinamik Menü Satırı Ekleme
    function addNavItem() {
        const container = document.getElementById('nav-container');
        const div = document.createElement('div');
        div.className = 'nav-item flex gap-2 items-center';
        div.innerHTML = `
            <input type="text" name="menu_title[]" placeholder="Menü Adı" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
            <input type="text" name="menu_url[]" placeholder="URL" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
            <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors text-xs font-bold">×</button>
        `;
        container.appendChild(div);
    }
</script>

<?php admin_footer(); ?>