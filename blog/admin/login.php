<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = $_GET['error'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Giriş bilgileri hatalı.";
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
    <title>Giriş Yap - Artado CMS</title>
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
            <!-- Artado Gerçek Logo -->
            <img src="https://artado.xyz/blog/assets/img/logo.png" alt="Artado Logo" class="h-16 w-auto mb-6 drop-shadow-2xl">
            
            <h1 class="text-2xl font-black tracking-tight text-white">Artado <span class="text-blue-500 font-medium">CMS</span></h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Artado Blog Yönetim Sistemi</p>
        </div>

        <!-- Ana Giriş Kutusu -->
        <div class="bg-slate-900/85 backdrop-blur-xl p-8 md:p-10 rounded-3xl border border-slate-800 shadow-2xl shadow-slate-950/50">
            
            <?php if($error): ?>
                <div class="bg-red-500/10 text-red-400 p-4 rounded-2xl mb-6 text-xs font-bold text-center border border-red-500/20 uppercase tracking-wider flex items-center justify-center gap-2">
                    <span>⚠️</span> <?php echo e($error); ?>
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
                
                <button type="submit" 
                        class="w-full py-3.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold uppercase tracking-wider text-xs transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-500/30 active:scale-[0.98] mt-2">
                    Giriş Yap
                </button>
                
                <div class="pt-4 border-t border-slate-800/80 mt-6 text-center">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        Hesabınız yok mu? 
                        <a href="register.php" class="text-blue-400 hover:text-blue-300 font-extrabold transition-colors ml-1">Kayıt Ol</a>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- Alt Siteye Dön Bağlantısı -->
        <div class="mt-8 text-center">
            <a href="../" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-400 hover:text-white uppercase tracking-widest transition-colors group">
                <span class="transform group-hover:-translate-x-1 transition-transform">←</span> Siteye Geri Dön
            </a>
        </div>
    </div>
</body>
</html>