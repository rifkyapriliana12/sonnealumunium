<?php
require_once 'config.php';
requireLogin();

$pdo = getDB();
$flash = getFlash();

// Get statistics
$stats = [
    'sliders' => $pdo->query("SELECT COUNT(*) FROM sliders")->fetchColumn(),
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'partners' => $pdo->query("SELECT COUNT(*) FROM partners")->fetchColumn(),
    'blog_posts' => $pdo->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn(),
    'testimonials' => $pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn(),
    'media' => $pdo->query("SELECT COUNT(*) FROM media")->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Sonne Aluminium</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="https://sonnealuminium.com/wp-content/uploads/2025/07/LOGO-SONNE-2025-FINAL-GOLD-980x319.png" alt="Logo">
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="sliders.php" class="nav-item">
                <i class="fas fa-images"></i>
                <span>Slider</span>
            </a>
            <a href="products.php" class="nav-item">
                <i class="fas fa-box"></i>
                <span>Produk</span>
            </a>
            <a href="projects.php" class="nav-item">
                <i class="fas fa-building"></i>
                <span>Proyek</span>
            </a>
            <a href="partners.php" class="nav-item">
                <i class="fas fa-handshake"></i>
                <span>Partner</span>
            </a>
            <a href="blog.php" class="nav-item">
                <i class="fas fa-blog"></i>
                <span>Blog</span>
            </a>
            <a href="testimonials.php" class="nav-item">
                <i class="fas fa-quote-right"></i>
                <span>Testimoni</span>
            </a>
            <a href="media.php" class="nav-item">
                <i class="fas fa-photo-video"></i>
                <span>Media</span>
            </a>
            <a href="settings.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Pengaturan</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.html" target="_blank" class="nav-item">
                <i class="fas fa-external-link-alt"></i>
                <span>Lihat Website</span>
            </a>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-right">
                <span class="admin-name">
                    <i class="fas fa-user-circle"></i>
                    <?php echo $_SESSION['admin_name']; ?>
                </span>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="content">
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Selamat datang di panel admin Sonne Aluminium</p>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #3498db;">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['sliders']; ?></h3>
                        <p>Slider</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #2ecc71;">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['products']; ?></h3>
                        <p>Produk</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e74c3c;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['projects']; ?></h3>
                        <p>Proyek</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #9b59b6;">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['partners']; ?></h3>
                        <p>Partner</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f39c12;">
                        <i class="fas fa-blog"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['blog_posts']; ?></h3>
                        <p>Blog</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #1abc9c;">
                        <i class="fas fa-quote-right"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $stats['testimonials']; ?></h3>
                        <p>Testimoni</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="section">
                <h2 class="section-title">Aksi Cepat</h2>
                <div class="quick-actions">
                    <a href="sliders.php?action=add" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Slider</span>
                    </a>
                    <a href="products.php?action=add" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Produk</span>
                    </a>
                    <a href="projects.php?action=add" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Proyek</span>
                    </a>
                    <a href="blog.php?action=add" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tulis Blog</span>
                    </a>
                    <a href="media.php" class="action-card">
                        <i class="fas fa-upload"></i>
                        <span>Upload Media</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Toggle Sidebar
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Auto-hide alert after 5 seconds
        setTimeout(function() {
            var alert = document.querySelector('.alert');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 5000);
    </script>
</body>
</html>
