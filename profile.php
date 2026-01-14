<?php
require_once 'core/db.php';
require_once 'core/functions.php';

$slug = isset($_GET['slug']) ? clean($_GET['slug']) : redirect('katki.php');

$stmt = $pdo->prepare("SELECT * FROM supporters WHERE slug = ?");
$stmt->execute([$slug]);
$supporter = $stmt->fetch();

if (!$supporter) {
    redirect('katki.php');
}

$pageTitle = $supporter['name'] . " - Profil";
$pageDescription = $supporter['description'] ?: $supporter['name'] . " - Artado projesine katkıda bulunan değerli bir destekçi.";
$pageUrl = BASE_URL . "profile.php?slug=" . $supporter['slug'];
$extraCss = '
<style>
    .profile-section {
        padding: 80px 20px;
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @media (max-width: 768px) {
        .profile-section {
            padding: 40px 15px;
            min-height: auto;
        }
    }
    
    @media (max-width: 480px) {
        .profile-section {
            padding: 30px 10px;
        }
    }
    .profile-card {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--card-border);
        border-radius: 30px;
        padding: 50px;
        max-width: 800px;
        width: 100%;
        text-align: center;
        box-shadow: var(--card-shadow);
        animation: profileFadeUp 0.8s ease-out;
    }
    
    @media (max-width: 768px) {
        .profile-card {
            padding: 30px 20px;
            border-radius: 20px;
            margin: 0 10px;
        }
    }
    
    @media (max-width: 480px) {
        .profile-card {
            padding: 25px 15px;
            border-radius: 18px;
            margin: 0 5px;
        }
    }
    @keyframes profileFadeUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .profile-avatar {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 30px;
        border: 4px solid var(--link-color);
        box-shadow: 0 0 30px rgba(var(--link-color-rgb), 0.3);
        transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .profile-avatar:hover {
        transform: scale(1.05) rotate(5deg);
    }
    
    @media (max-width: 768px) {
        .profile-avatar {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
            border-width: 3px;
        }
    }
    
    @media (max-width: 480px) {
        .profile-avatar {
            width: 100px;
            height: 100px;
            margin-bottom: 15px;
            border-width: 2px;
        }
    }
    .profile-name {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        background: linear-gradient(to right, var(--text-color), var(--link-color));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    @media (max-width: 768px) {
        .profile-name {
            font-size: 1.8rem;
            margin-bottom: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .profile-name {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
    }
    .profile-description {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--paragraph-color);
        margin-bottom: 40px;
    }
    
    @media (max-width: 768px) {
        .profile-description {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            padding: 0 5px;
        }
    }
    
    @media (max-width: 480px) {
        .profile-description {
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 25px;
        }
    }
    .contribution-box {
        text-align: left;
        background: rgba(var(--hover-color-rgb), 0.03);
        padding: 30px;
        border-radius: 20px;
        border: 1px solid var(--card-border);
        margin-bottom: 40px;
    }
    
    @media (max-width: 768px) {
        .contribution-box {
            padding: 20px 15px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
    }
    
    @media (max-width: 480px) {
        .contribution-box {
            padding: 15px 12px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
    }
    .contribution-box h3 {
        color: var(--link-color);
        margin-bottom: 20px;
        font-size: 1.3rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    @media (max-width: 768px) {
        .contribution-box h3 {
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
    }
    
    @media (max-width: 480px) {
        .contribution-box h3 {
            font-size: 1rem;
            margin-bottom: 12px;
        }
        
        .contribution-box h3 i {
            font-size: 0.9rem;
        }
    }
    .contribution-list {
        list-style: none;
        padding: 0;
    }
    .contribution-list li {
        padding: 10px 0;
        padding-left: 30px;
        position: relative;
        color: var(--text-color);
        opacity: 0.9;
    }
    
    @media (max-width: 768px) {
        .contribution-list li {
            padding: 8px 0;
            padding-left: 25px;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 480px) {
        .contribution-list li {
            padding: 6px 0;
            padding-left: 22px;
            font-size: 0.85rem;
        }
    }
    .contribution-list li::before {
        content: "\f058";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        position: absolute;
        left: 0;
        color: var(--link-color);
    }
    .profile-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    @media (max-width: 768px) {
        .profile-actions {
            flex-direction: column;
            gap: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .profile-actions {
            gap: 10px;
        }
    }
    .social-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 25px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    @media (max-width: 768px) {
        .social-btn {
            width: 100%;
            justify-content: center;
            padding: 12px 20px;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 480px) {
        .social-btn {
            padding: 10px 18px;
            font-size: 0.85rem;
        }
        
        .social-btn i {
            font-size: 0.9rem;
        }
    }
    .btn-github {
        background: #24292e;
        color: white;
    }
    .btn-github:hover {
        background: #444;
        transform: translateY(-3px);
    }
    .btn-back {
        background: rgba(var(--link-color-rgb), 0.1);
        color: var(--text-color);
        border: 1px solid var(--card-border);
    }
    .btn-back:hover {
        background: var(--link-color);
        color: white;
        transform: translateY(-3px);
    }
</style>';

include 'includes/header.php';
?>

    <div class="profile-section">
        <div class="profile-card">
            <img src="<?php echo $supporter['image_url']; ?>" alt="<?php echo $supporter['name']; ?>" class="profile-avatar">
            <h2 class="profile-name"><?php echo $supporter['name']; ?></h2>
            <p class="profile-description"><?php echo $supporter['description'] ?: 'Bu kişi Artado projesine değerli katkılarda bulunmuştur.'; ?></p>
            
            <?php if ($supporter['contributions']): ?>
            <div class="contribution-box">
                <h3><i class="fas fa-award"></i> Katkılar</h3>
                <ul class="contribution-list">
                    <?php 
                    $lines = explode("\n", $supporter['contributions']);
                    foreach($lines as $line) {
                        if(trim($line)) echo "<li>" . htmlspecialchars($line) . "</li>";
                    }
                    ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="profile-actions">
                <?php if ($supporter['github_link'] && $supporter['github_link'] != '#'): ?>
                <a href="<?php echo $supporter['github_link']; ?>" target="_blank" class="social-btn btn-github">
                    <i class="fab fa-github"></i> GitHub Profilini Görüntüle
                </a>
                <?php endif; ?>
                
                <a href="<?php echo BASE_URL; ?>katki.php" class="social-btn btn-back">
                    <i class="fas fa-arrow-left"></i> Tümüne Dön
                </a>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
