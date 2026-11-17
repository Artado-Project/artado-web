<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('editor');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page = null;
$message = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$id]);
    $page = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = $_POST['slug'] ?: create_slug($title);
    $content = $_POST['content'];
    $status = $_POST['status'];
    $lang = $_POST['lang'] ?? 'tr';
    $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, status = ?, lang = ?, allow_comments = ? WHERE id = ?");
        $stmt->execute([$title, $slug, $content, $status, $lang, $allow_comments, $id]);
        $message = "Page updated successfully.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO pages (title, slug, content, status, lang, allow_comments) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $content, $status, $lang, $allow_comments]);
        $id = $pdo->lastInsertId();
        header("Location: page-edit.php?id=$id&msg=Page published.");
        exit;
    }
    
    // Refresh
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$id]);
    $page = $stmt->fetch();
}

if (isset($_GET['msg'])) $message = $_GET['msg'];

admin_header($id ? 'Edit Page' : 'New Page');
admin_sidebar('pages');
?>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-toolbar { border: 1px solid #ebedf2 !important; border-radius: 4px 4px 0 0 !important; background: #fcfcfc; }
    .ql-container { border: 1px solid #ebedf2 !important; border-radius: 0 0 4px 4px !important; min-height: 500px; font-size: 16px; }
    .ql-editor { padding: 30px !important; line-height: 1.6; }
    .editor-title { border: none !important; border-bottom: 2px solid #ebedf2 !important; border-radius: 0 !important; font-size: 2rem !important; font-weight: 800 !important; padding: 15px 0 !important; margin-bottom: 30px !important; box-shadow: none !important; }
    .editor-title:focus { border-bottom-color: #3b82f6 !important; }
    .sidebar-widget { background: #fff; border: 1px solid #ebedf2; border-radius: 4px; margin-bottom: 25px; }
    .widget-header { padding: 15px 20px; border-bottom: 1px solid #ebedf2; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #b5b5c3; }
    .widget-body { padding: 20px; }
</style>

<main class="p-10">
    <div class="flex items-center justify-between mb-10">
        <h1 class="text-2xl font-bold text-gray-900"><?php echo $id ? 'Edit Page' : 'Add New Page'; ?></h1>
        <button type="submit" form="page-form" class="btn-primary-pro">Publish / Update</button>
    </div>

    <?php if($message): ?><div class="p-4 bg-green-50 text-green-700 rounded border border-green-200 mb-8 text-xs font-bold uppercase tracking-widest"><?php echo $message; ?></div><?php endif; ?>

    <form method="POST" id="page-form" class="flex flex-col lg:flex-row gap-10">
        <div class="flex-1">
            <div class="card p-10">
                <input type="text" name="title" value="<?php echo e($page['title'] ?? ''); ?>" placeholder="Page Title" class="editor-title w-full" required>
                <div class="mb-4 flex items-center space-x-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    <span>Permalink:</span>
                    <input type="text" name="slug" value="<?php echo e($page['slug'] ?? ''); ?>" class="!p-0 !border-0 !text-blue-500 !font-bold w-1/2 !bg-transparent" placeholder="auto-generated">
                </div>
                <div id="editor-wrapper"></div>
                <input type="hidden" name="content" id="content-input" value="<?php echo e($page['content'] ?? ''); ?>">
            </div>
        </div>

        <div class="w-full lg:w-80">
            <div class="sidebar-widget">
                <div class="widget-header">Page Settings</div>
                <div class="widget-body space-y-6">
                    <div>
                        <label>Status</label>
                        <select name="status" class="w-full">
                            <option value="published" <?php echo ($page['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo ($page['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    <div>
                        <label>Language</label>
                        <select name="lang" class="w-full">
                            <option value="tr" <?php echo ($page['lang'] ?? '') === 'tr' ? 'selected' : ''; ?>>Türkçe</option>
                            <option value="en" <?php echo ($page['lang'] ?? '') === 'en' ? 'selected' : ''; ?>>English</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-3 pt-4 border-t border-gray-50">
                        <input type="checkbox" name="allow_comments" id="allow_comments" <?php echo ($page['allow_comments'] ?? 0) ? 'checked' : ''; ?> class="!w-4 !h-4 !p-0">
                        <label for="allow_comments" class="!mb-0">Allow Comments</label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    var quill = new Quill('#editor-wrapper', {
        theme: 'snow',
        modules: {
            toolbar: [[{ 'header': [1, 2, 3, false] }],['bold', 'italic', 'underline'],['link', 'image', 'video'],[{ 'list': 'ordered'}, { 'list': 'bullet' }],['clean']]
        },
        placeholder: 'Write page content...'
    });
    quill.root.innerHTML = document.getElementById('content-input').value;
    document.getElementById('page-form').onsubmit = function() {
        document.getElementById('content-input').value = quill.root.innerHTML;
    };
</script>

<?php admin_footer(); ?>
