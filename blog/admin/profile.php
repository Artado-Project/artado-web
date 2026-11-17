<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'layout.php';

require_role('author'); // All staff roles can manage their profile

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $bio = trim($_POST['bio']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $profile_pic = $_POST['current_pic'];

    // Ensure uploads directory exists
    $upload_dir_abs = dirname(__DIR__) . '/uploads/';
    if (!is_dir($upload_dir_abs)) {
        mkdir($upload_dir_abs, 0777, true);
    }

    // Handle Base64 Upload (Compressed via JS)
    if (!empty($_POST['profile_pic_base64'])) {
        $data = $_POST['profile_pic_base64'];
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, etc

            $data = base64_decode($data);
            if ($data !== false) {
                $new_name = 'user_' . $user_id . '_' . time() . '.jpg';
                $upload_path_abs = $upload_dir_abs . $new_name;
                
                if (file_put_contents($upload_path_abs, $data)) {
                    $profile_pic = 'uploads/' . $new_name;
                } else {
                    $error = "Dosya sunucuya kaydedilemedi.";
                }
            }
        }
    }
    // Fallback to traditional upload if JS compression failed or not used
    elseif (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['profile_pic']['name'];
        $filesize = $_FILES['profile_pic']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $error = "Geçersiz dosya formatı.";
        } else {
            $new_name = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path_abs = $upload_dir_abs . $new_name;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path_abs)) {
                $profile_pic = 'uploads/' . $new_name;
            }
        }
    }

    if ($password && $password !== $password_confirm) {
        $error = "Şifreler eşleşmiyor.";
    } elseif (!$error && $username) {
        if ($password) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, bio = ?, profile_pic = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $bio, $profile_pic, $hashed, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, bio = ?, profile_pic = ? WHERE id = ?");
            $stmt->execute([$username, $bio, $profile_pic, $user_id]);
        }
        $_SESSION['username'] = $username;
        $message = "Profil başarıyla güncellendi.";
    } elseif (!$username) {
        $error = "Kullanıcı adı boş bırakılamaz.";
    }
}

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

admin_header('Profilim');
admin_sidebar('profile');
?>

<main class="p-4 md:p-8 lg:p-10 bg-slate-50 min-h-screen text-slate-800">
    <div class="mb-8 md:mb-10">
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Profil Ayarları</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Biyografinizi, fotoğrafınızı ve giriş bilgilerinizi yönetin.</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Sol Bölüm: Bilgiler -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 md:p-8">
                <h2 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-6 border-b border-slate-100 pb-4">Hesap Bilgileri</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Kullanıcı Adı</label>
                        <input type="text" name="username" value="<?php echo e($user['username']); ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" required>
                    </div>
                    
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Yeni Şifre <span class="text-slate-400 font-normal lowercase">(isteğe bağlı)</span></label>
                        <input type="password" name="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="••••••••">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Biyografi</label>
                        <textarea name="bio" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="Kısaca kendinizden bahsedin..."><?php echo e($user['bio']); ?></textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-600 mb-1.5">Şifre Tekrar</label>
                        <input type="password" name="password_confirm" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold uppercase tracking-widest text-xs transition-all shadow-sm shadow-blue-600/30">Profili Güncelle</button>
            </div>
        </div>

        <!-- Sağ Bölüm: Profil Resmi ve Uyarılar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/50 p-6 text-center">
                <h2 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-6 border-b border-slate-100 pb-4">Profil Fotoğrafı</h2>
                
                <div class="mb-6">
                    <div class="w-32 h-32 rounded-full bg-slate-100 mx-auto overflow-hidden border-4 border-white shadow-md ring-1 ring-slate-200">
                        <img src="../<?php echo !empty($user['profile_pic']) ? $user['profile_pic'] : 'assets/img/default-avatar.png'; ?>" class="w-full h-full object-cover" alt="">
                    </div>
                </div>
                
                <input type="hidden" name="current_pic" value="<?php echo e($user['profile_pic']); ?>">
                
                <div class="mb-4">
                    <input type="file" id="profile-upload" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>
                
                <input type="hidden" name="profile_pic_base64" id="profile-pic-base64">
                
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                    JPG, PNG veya WebP (Otomatik Sıkıştırılır)
                </p>
            </div>

            <?php if($message): ?>
                <div class="p-4 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-200 flex items-center gap-2 shadow-sm">
                    <span>✨</span> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="p-4 bg-red-50 text-red-700 text-xs font-semibold rounded-xl border border-red-200 flex items-center gap-2 shadow-sm">
                    <span>⚠️</span> <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </div>
    </form>
</main>

<script>
    document.getElementById('profile-upload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                // Max dimensions 800x800
                const max_size = 800;
                if (width > height) {
                    if (width > max_size) {
                        height *= max_size / width;
                        width = max_size;
                    }
                } else {
                    if (height > max_size) {
                        width *= max_size / height;
                        height = max_size;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Compress to JPEG 0.7 quality
                const dataUrl = canvas.toDataURL('image/jpeg', 0.7);
                document.getElementById('profile-pic-base64').value = dataUrl;
                
                // Update preview
                document.querySelector('.w-32 img').src = dataUrl;
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>

<?php admin_footer(); ?>