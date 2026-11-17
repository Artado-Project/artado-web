<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('admin');

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($username && $password) {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = "Bu kullanıcı adı zaten alınmış.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed, $role]);
            $msg = "Yeni kullanıcı başarıyla oluşturuldu.";
        }
    } else {
        $error = "Lütfen tüm alanları doldurun.";
    }
}

if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    if ($delete_id === $_SESSION['user_id']) {
        $error = "Kendinizi silemezsiniz.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$delete_id]);
        $msg = "Kullanıcı başarıyla silindi.";
    }
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

admin_header('Kullanıcı Yönetimi');
admin_sidebar('users');
?>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50 min-h-screen text-slate-800">
    <div class="mb-8 md:mb-10">
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Kullanıcı Yönetimi</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Sistemdeki yetkili hesapları ve erişim seviyelerini kontrol edin.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Yeni Kullanıcı Ekle Formu -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6">
                <h2 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-6 border-b border-slate-100 pb-4">Yeni Kullanıcı Ekle</h2>
                
                <form method="POST" class="space-y-5">
                    <?php if($error): ?>
                        <div class="p-3.5 bg-red-50 text-red-700 text-xs font-semibold rounded-xl border border-red-200 flex items-center gap-2">
                            <span>⚠️</span> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($msg): ?>
                        <div class="p-3.5 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-200 flex items-center gap-2">
                            <span>✨</span> <?php echo $msg; ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Kullanıcı Adı</label>
                        <input type="text" name="username" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Şifre</label>
                        <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Rol</label>
                        <select name="role" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all cursor-pointer">
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="author">Author</option>
                        </select>
                    </div>

                    <button type="submit" name="add_user" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold uppercase tracking-widest text-xs transition-all shadow-sm shadow-blue-600/30">Hesap Oluştur</button>
                </form>
            </div>
        </div>

        <!-- Kullanıcı Listesi Tablosu -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/75">
                                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-slate-500 w-16">ID</th>
                                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-slate-500">Kullanıcı</th>
                                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-slate-500 text-center">Rol</th>
                                <th class="py-4 px-6 text-[10px] font-black uppercase tracking-widest text-slate-500 text-right">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach($users as $user): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-4 px-6 text-slate-400 font-mono text-xs">#<?php echo $user['id']; ?></td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                                                <img src="../<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'assets/img/default-avatar.png'; ?>" class="w-full h-full object-cover" alt="">
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm"><?php echo e($user['username']); ?></div>
                                                <?php if($user['id'] === $_SESSION['user_id']): ?>
                                                    <span class="inline-block text-[9px] font-black uppercase text-blue-600 tracking-wider bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 mt-0.5">Aktif Oturum</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border <?php 
                                            echo $user['role'] === 'admin' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200'; ?>">
                                            <?php echo $user['role']; ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-3">
                                        <a href="user-edit.php?id=<?php echo $user['id']; ?>" class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200 text-xs font-bold rounded-lg transition-all">Düzenle</a>
                                        
                                        <?php if($user['id'] !== $_SESSION['user_id']): ?>
                                            <a href="users.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')" class="inline-flex items-center px-2.5 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 text-xs font-bold rounded-lg transition-all">Sil</a>
                                        <?php else: ?>
                                            <span class="text-[10px] font-bold text-slate-400 italic px-2">Koruma Altında</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php admin_footer(); ?>