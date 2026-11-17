<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

// Check if any user exists to determine the first admin
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$user_count = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = "Şifreler eşleşmiyor.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Bu kullanıcı adı zaten alınmış.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // First user is Admin, others are Author
                $role = ($user_count == 0) ? 'admin' : 'author';
                
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $hashed_password, $role])) {
                    $success = "Kayıt başarılı! Şimdi <a href='login.php' class='text-blue-400 hover:underline font-extrabold'>giriş yapabilirsiniz</a>.";
                } else {
                    $error = "Kayıt sırasında bir hata oluştu.";
                }
            }
        }
    } else {
        $error = "Lütfen tüm alanları doldurun.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Artado CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0b0f19;
            background-image: radial-gradient(circle at 50% 0%, #1e1b4b 0%, #0b0f19 75%);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6 antialiased selection:bg-blue-600 selection:text-white">
    <div class="w-full max-w-md">
        
        <!-- Logo & Üst Başlık -->
        <div class="flex flex-col items-center mb-8">
            <img src="https://artado.xyz/blog/assets/img/logo.png" alt="Artado Logo" class="h-16 w-auto mb-6 drop-shadow-2xl">
            
            <h1 class="text-2xl font-black tracking-tight text-white">Artado <span class="text-blue-500 font-medium">CMS</span></h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">
                <?php echo ($user_count == 0) ? '✨ İlk Kurulum (Yönetici Hesabı)' : ' Ekibe Katılın'; ?>
            </p>
        </div>

        <!-- Ana Kayıt Kutusu -->
        <div class="bg-slate-900/85 backdrop-blur-xl p-8 md:p-10 rounded-3xl border border-slate-800 shadow-2xl shadow-slate-950/50">
            
            <?php if($error): ?>
                <div class="bg-red-500/10 text-red-400 p-4 rounded-2xl mb-6 text-xs font-bold text-center border border-red-500/20 uppercase tracking-wider flex items-center justify-center gap-2">
                    <span>⚠️</span> <?php echo e($error); ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="bg-emerald-500/10 text-emerald-400 p-4 rounded-2xl mb-6 text-xs font-bold text-center border border-emerald-500/20 uppercase tracking-wider flex items-center justify-center gap-2">
                    <span>🎉</span> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Kullanıcı Adı</label>
                    <input type="text" name="username" placeholder="Kullanıcı adınızı girin" 
                           class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none text-sm transition-all font-medium" required>
                </div>
                
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Şifre</label>
                    <input type="password" name="password" placeholder="••••••••" 
                           class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none text-sm transition-all font-medium" required>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Şifre Tekrar</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" 
                           class="w-full px-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-100 placeholder-slate-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none text-sm transition-all font-medium" required>
                </div>
                
                <button type="submit" 
                        class="w-full py-3.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold uppercase tracking-wider text-xs transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-500/30 active:scale-[0.98] mt-2">
                    Hesap Oluştur
                </button>
            </form>
        </div>
        
        <!-- Alt Giriş Bağlantısı -->
        <div class="mt-8 text-center">
            <a href="login.php" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-400 hover:text-white uppercase tracking-widest transition-colors group">
                Zaten bir hesabınız var mı? <span class="text-blue-400 group-hover:text-blue-300 font-extrabold transition-colors ml-1">Giriş Yap</span>
            </a>
        </div>
    </div>
</body>
</html>