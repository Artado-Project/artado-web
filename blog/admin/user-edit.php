<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('admin');

$user_id = (int)($_GET['id'] ?? 0);
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if ($username) {
        try {
            if ($password) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, password = ? WHERE id = ?");
                $stmt->execute([$username, $role, $hashed, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
                $stmt->execute([$username, $role, $user_id]);
            }
            $msg = "Kullanıcı başarıyla güncellendi.";
        } catch (Exception $e) {
            $error = "Hata: " . $e->getMessage();
        }
    } else {
        $error = "Kullanıcı adı boş bırakılamaz.";
    }
}

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: users.php");
    exit;
}

admin_header('Kullanıcı Düzenle');
admin_sidebar('users');
?>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50 min-h-screen text-slate-800">
    
    <div class="mb-8 md:mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Kullanıcı Düzenle: <?php echo e($user['username']); ?></h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Hesap bilgilerini ve sistem yetkilerini güncelleyin.</p>
        </div>
        <a href="users.php" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-sm transition-all self-start sm:self-auto">
            <span>←</span> Kullanıcılara Dön
        </a>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 md:p-8">
            
            <!-- Profil Bilgilendirme Kartı Üst Kısım -->
            <div class="flex items-center gap-4 pb-6 mb-6 border-b border-slate-100">
                <div class="w-16 h-16 rounded-full bg-slate-100 border-2 border-slate-200 overflow-hidden shrink-0 shadow-inner">
                    <img src="../<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'assets/img/default-avatar.png'; ?>" class="w-full h-full object-cover" alt="">
                </div>
                <div>
                    <div class="text-xs font-black uppercase tracking-wider text-slate-400">Kullanıcı ID: #<?php echo $user['id']; ?></div>
                    <div class="text-lg font-bold text-slate-900 mt-0.5"><?php echo e($user['username']); ?></div>
                    <div class="text-xs text-slate-500 font-medium">Kayıtlı Rol: <span class="uppercase font-bold text-blue-600"><?php echo $user['role']; ?></span></div>
                </div>
            </div>

            <form method="POST" class="space-y-6">
                <?php if($error): ?>
                    <div class="p-4 bg-red-50 text-red-700 text-xs font-semibold rounded-xl border border-red-200 flex items-center gap-2">
                        <span>⚠️</span> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($msg): ?>
                    <div class="p-4 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-200 flex items-center gap-2">
                        <span>✨</span> <?php echo $msg; ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Kullanıcı Adı</label>
                        <input type="text" name="username" value="<?php echo e($user['username']); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Rol</label>
                        <select name="role" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all cursor-pointer">
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="editor" <?php echo $user['role'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
                            <option value="author" <?php echo $user['role'] === 'author' ? 'selected' : ''; ?>>Author</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Yeni Şifre <span class="text-slate-400 font-normal lowercase">(mevcut şifreyi korumak için boş bırakın)</span></label>
                    <input type="password" name="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="••••••••">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="users.php" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold uppercase tracking-widest text-xs transition-all">İptal</a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold uppercase tracking-widest text-xs transition-all shadow-sm shadow-blue-600/30">Hesabı Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php admin_footer(); ?>