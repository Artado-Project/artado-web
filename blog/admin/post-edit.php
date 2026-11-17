<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('author');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;
$message = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    // Security: Non-admins can only edit their own posts
    if (!is_admin() && $post['author_id'] != $_SESSION['user_id']) {
        header("Location: posts.php?error=Bu yazıyı düzenleme yetkiniz yok.");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']) ?: create_slug($title);
    $content = $_POST['content'];
    $category = trim($_POST['category']);
    $status = $_POST['status'];
    $lang = $_POST['lang'] ?? 'tr';

    if ($title) {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, content = ?, category = ?, status = ?, lang = ?, allow_comments = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $content, $category, $status, $lang, isset($_POST['allow_comments']) ? 1 : 0, $id]);
            $message = "Yazı başarıyla güncellendi.";
        } else {
            $author_id = $_SESSION['user_id'];
            $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, category, status, lang, author_id, allow_comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $content, $category, $status, $lang, $author_id, isset($_POST['allow_comments']) ? 1 : 0]);
            $id = $pdo->lastInsertId();
            header("Location: post-edit.php?id=$id&msg=Yazı başarıyla yayınlandı.");
            exit;
        }
        
        // Refresh
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
    }
}

if (isset($_GET['msg'])) $message = $_GET['msg'];

admin_header($id ? 'Yazıyı Düzenle' : 'Yeni Yazı Ekle');
admin_sidebar('posts');
?>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar.ql-snow { border: 1px solid #e2e8f0 !important; border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important; background: #f8fafc !important; padding: 12px !important; }
    .ql-container.ql-snow { border: 1px solid #e2e8f0 !important; border-bottom-left-radius: 1rem !important; border-bottom-right-radius: 1rem !important; min-height: 450px; font-size: 15px; background: #fff; font-family: inherit; }
    .ql-editor { padding: 24px !important; line-height: 1.7; color: #1e293b; }
    .ql-editor.ql-blank::before { color: #94a3b8; font-style: normal; }
</style>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50 min-h-screen text-slate-800">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight"><?php echo $id ? 'Yazıyı Düzenle' : 'Yeni Yazı Ekle'; ?></h1>
            <p class="text-sm text-slate-500 font-medium mt-1">İçeriğinizi oluşturun, özelleştirin ve okuyucularınızla paylaşın.</p>
        </div>
        <div class="flex items-center gap-3 self-start sm:self-auto">
            <a href="posts.php" class="px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-sm transition-all">← Yazılara Dön</a>
            <button type="submit" form="post-form" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold uppercase tracking-widest text-xs transition-all shadow-sm shadow-blue-600/30">
                <?php echo $id ? 'Değişiklikleri Kaydet' : 'Yayınla'; ?>
            </button>
        </div>
    </div>

    <?php if($message): ?>
        <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 mb-8 text-xs font-semibold flex items-center gap-2 shadow-sm">
            <span>✨</span> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="post-form" class="flex flex-col lg:flex-row gap-8">
        
        <!-- Ana İçerik Alanı -->
        <div class="flex-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 md:p-8 space-y-6">
                <div>
                    <input type="text" name="title" value="<?php echo e($post['title'] ?? ''); ?>" placeholder="Yazı başlığını buraya girin..." class="w-full text-xl md:text-2xl font-black text-slate-900 border-0 border-b-2 border-slate-100 pb-3 focus:border-blue-600 focus:ring-0 outline-none transition-all placeholder:text-slate-300" required>
                </div>
                
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                    <span class="uppercase tracking-wider text-[10px]">Permalink:</span>
                    <input type="text" name="slug" value="<?php echo e($post['slug'] ?? ''); ?>" class="bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 text-xs text-blue-600 font-semibold w-full max-w-md focus:bg-white focus:border-blue-600 outline-none transition-all" placeholder="otomatik-olusturulur">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-2">İçerik</label>
                    <div id="editor-wrapper"></div>
                    <input type="hidden" name="content" id="content-input" value="<?php echo e($post['content'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <!-- Sağ Yan Panel Widget'ları -->
        <div class="w-full lg:w-80 space-y-6">
            
            <!-- Yayın Ayarları -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 space-y-5">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 border-b border-slate-100 pb-3">Yayın Ayarları</h3>
                
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Durum</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all cursor-pointer" <?php echo !is_admin() && ($post['status'] ?? 'draft') === 'draft' ? 'disabled' : ''; ?>>
                        <?php if(is_admin() || ($post['status'] ?? '') === 'published'): ?>
                        <option value="published" <?php echo ($post['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Yayınlandı</option>
                        <?php endif; ?>
                        <option value="draft" <?php echo ($post['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Taslak</option>
                    </select>
                    <?php if(!is_admin() && ($post['status'] ?? 'draft') === 'draft'): ?>
                        <input type="hidden" name="status" value="draft">
                        <p class="text-[10px] text-amber-600 font-bold mt-2 leading-relaxed">⚠️ Bu içerik yayınlanmak için Admin onayı gerektirir.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Dil</label>
                    <select name="lang" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all cursor-pointer">
                        <option value="tr" <?php echo ($post['lang'] ?? '') === 'tr' ? 'selected' : ''; ?>>Türkçe</option>
                        <option value="en" <?php echo ($post['lang'] ?? '') === 'en' ? 'selected' : ''; ?>>English</option>
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-slate-100">
                    <input type="checkbox" name="allow_comments" id="allow_comments" <?php echo ($post['allow_comments'] ?? 1) ? 'checked' : ''; ?> class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                    <label for="allow_comments" class="text-xs font-bold text-slate-700 cursor-pointer select-none">Yorumlara İzin Ver</label>
                </div>
            </div>

            <!-- Kategorilendirme -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 space-y-4">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 border-b border-slate-100 pb-3">Kategorilendirme</h3>
                
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Kategori</label>
                    <input type="text" name="category" value="<?php echo e($post['category'] ?? ''); ?>" placeholder="örn. Teknoloji" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm text-slate-800 focus:bg-white focus:border-blue-600 outline-none transition-all">
                    <p class="text-[10px] text-slate-400 mt-1.5">Birden fazla kategori için virgül kullanabilirsiniz.</p>
                </div>
            </div>

            <!-- Yazı İşlemleri -->
            <?php if($id): ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-500 border-b border-slate-100 pb-3 mb-4">İşlemler</h3>
                <a href="posts.php?delete=<?php echo $id; ?>" class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-700 transition-colors" onclick="return confirm('Bu yazıyı silmek istediğinizden emin misiniz?')">
                    <span>🗑️</span> Çöpe Taşı
                </a>
            </div>
            <?php endif; ?>

        </div>
    </form>
</main>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    var quill = new Quill('#editor-wrapper', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                ['link', 'image', 'video'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ]
        },
        placeholder: 'Hikayenizi yazmaya başlayın...'
    });

    quill.root.innerHTML = document.getElementById('content-input').value;

    document.getElementById('post-form').onsubmit = function() {
        document.getElementById('content-input').value = quill.root.innerHTML;
    };
</script>

<?php admin_footer(); ?>