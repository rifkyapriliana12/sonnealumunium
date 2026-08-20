<?php
require_once 'config.php';
requireLogin();

$pdo = getDB();
$flash = getFlash();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
    $allowedImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowedVideo = ['mp4', 'webm', 'ogg', 'mov'];
    $uploadDir = __DIR__ . '/../uploads/media/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploaded = 0;
    $errors = 0;
    
    foreach ($_FILES['files']['name'] as $key => $name) {
        if ($_FILES['files']['error'][$key] == 0) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            
            if (in_array($ext, array_merge($allowedImage, $allowedVideo))) {
                $newFilename = 'media-' . time() . '-' . uniqid() . '.' . $ext;
                
                if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $uploadDir . $newFilename)) {
                    $fileType = in_array($ext, $allowedImage) ? 'image' : 'video';
                    $fileSize = $_FILES['files']['size'][$key];
                    
                    $stmt = $pdo->prepare("INSERT INTO media (filename, original_name, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$newFilename, $name, $fileType, $fileSize, $_SESSION['admin_id']]);
                    $uploaded++;
                } else {
                    $errors++;
                }
            } else {
                $errors++;
            }
        }
    }
    
    if ($uploaded > 0) {
        setFlash("$uploaded file berhasil diupload!");
    }
    if ($errors > 0) {
        setFlash("$errors file gagal diupload!", 'error');
    }
    
    header('Location: media.php');
    exit;
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $media = $stmt->fetch();
    
    if ($media) {
        $file = __DIR__ . '/../uploads/media/' . $media['filename'];
        if (file_exists($file)) {
            unlink($file);
        }
        
        $stmt = $pdo->prepare("DELETE FROM media WHERE id = ?");
        $stmt->execute([intval($_GET['id'])]);
        setFlash('File berhasil dihapus!', 'error');
    }
    
    header('Location: media.php');
    exit;
}

// Get all media
$media = $pdo->query("SELECT m.*, a.full_name FROM media m LEFT JOIN admin_users a ON m.uploaded_by = a.id ORDER BY m.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Media - Admin Sonne Aluminium</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .media-item {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .media-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .media-preview {
            height: 150px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .media-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .media-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .media-preview i {
            font-size: 40px;
            color: #ccc;
        }
        
        .media-info {
            padding: 15px;
        }
        
        .media-info h4 {
            font-size: 13px;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 5px;
        }
        
        .media-info p {
            font-size: 11px;
            color: #999;
        }
        
        .media-info .media-actions {
            display: flex;
            gap: 5px;
            margin-top: 10px;
        }
        
        .upload-area {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .upload-zone {
            border: 3px dashed #ddd;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .upload-zone:hover {
            border-color: #d59203;
            background: rgba(213,146,3,0.02);
        }
        
        .upload-zone i {
            font-size: 50px;
            color: #d59203;
            margin-bottom: 15px;
        }
        
        .upload-zone h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .upload-zone p {
            color: #666;
            font-size: 14px;
        }
        
        .upload-zone .btn {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="https://sonnealuminium.com/wp-content/uploads/2025/07/LOGO-SONNE-2025-FINAL-GOLD-980x319.png" alt="Logo">
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
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
            <a href="media.php" class="nav-item active">
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

        <div class="content">
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo $flash['message']; ?>
                </div>
            <?php endif; ?>

            <h1 class="page-title">Kelola Media</h1>
            <p class="page-subtitle">Upload dan kelola gambar serta video</p>

            <!-- Upload Area -->
            <div class="upload-area">
                <form method="POST" enctype="multipart/form-data" id="uploadForm">
                    <div class="upload-zone" id="dropZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <h3>Upload File</h3>
                        <p>Drag & drop file atau klik untuk memilih</p>
                        <p style="font-size: 12px; color: #999; margin-top: 10px;">
                            Format: JPG, PNG, GIF, WebP, MP4, WebM, MOV (Max 50MB per file)
                        </p>
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('files').click()">
                            <i class="fas fa-plus"></i> Pilih File
                        </button>
                        <input type="file" id="files" name="files[]" multiple accept="image/*,video/*" style="display: none;">
                    </div>
                    <div id="filePreview" style="margin-top: 20px;"></div>
                    <div id="uploadProgress" style="display: none; margin-top: 20px;">
                        <div style="background: #eee; border-radius: 10px; overflow: hidden; height: 20px;">
                            <div id="progressBar" style="background: #d59203; height: 100%; width: 0%; transition: width 0.3s;"></div>
                        </div>
                        <p id="progressText" style="margin-top: 10px; text-align: center; color: #666;">Mengupload...</p>
                    </div>
                </form>
            </div>

            <!-- Media Library -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Media Library (<?php echo count($media); ?> file)</h2>
                </div>

                <?php if (empty($media)): ?>
                    <div class="empty-state">
                        <i class="fas fa-photo-video"></i>
                        <p>Belum ada media</p>
                    </div>
                <?php else: ?>
                    <div class="media-grid">
                        <?php foreach ($media as $m): ?>
                            <div class="media-item">
                                <div class="media-preview">
                                    <?php if ($m['file_type'] == 'image'): ?>
                                        <img src="../uploads/media/<?php echo $m['filename']; ?>" alt="<?php echo $m['original_name']; ?>">
                                    <?php elseif ($m['file_type'] == 'video'): ?>
                                        <video src="../uploads/media/<?php echo $m['filename']; ?>" muted></video>
                                    <?php else: ?>
                                        <i class="fas fa-file"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="media-info">
                                    <h4 title="<?php echo $m['original_name']; ?>"><?php echo $m['original_name']; ?></h4>
                                    <p><?php echo number_format($m['file_size'] / 1024, 1); ?> KB</p>
                                    <p>Oleh: <?php echo $m['full_name'] ?? 'Unknown'; ?></p>
                                    <div class="media-actions">
                                        <button class="btn btn-primary btn-sm" onclick="copyUrl('<?php echo SITE_URL; ?>/uploads/media/<?php echo $m['filename']; ?>')">
                                            <i class="fas fa-link"></i> Copy URL
                                        </button>
                                        <a href="media.php?action=delete&id=<?php echo $m['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Drag and drop
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('files');
        const filePreview = document.getElementById('filePreview');
        const uploadForm = document.getElementById('uploadForm');

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#d59203';
            dropZone.style.background = 'rgba(213,146,3,0.05)';
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#ddd';
            dropZone.style.background = 'transparent';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#ddd';
            dropZone.style.background = 'transparent';
            
            const files = e.dataTransfer.files;
            fileInput.files = files;
            previewFiles(files);
        });

        fileInput.addEventListener('change', (e) => {
            previewFiles(e.target.files);
        });

        function previewFiles(files) {
            filePreview.innerHTML = '';
            
            Array.from(files).forEach(file => {
                const div = document.createElement('div');
                div.style.cssText = 'display: inline-block; margin: 5px; padding: 10px; background: #f5f5f5; border-radius: 5px;';
                
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.cssText = 'width: 100px; height: 100px; object-fit: cover; border-radius: 5px;';
                    div.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.style.cssText = 'width: 100px; height: 100px; object-fit: cover; border-radius: 5px;';
                    video.muted = true;
                    div.appendChild(video);
                }
                
                const name = document.createElement('p');
                name.textContent = file.name;
                name.style.cssText = 'font-size: 11px; margin-top: 5px; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
                div.appendChild(name);
                
                filePreview.appendChild(div);
            });
        }

        function copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('URL berhasil di-copy!');
            });
        }
    </script>
</body>
</html>
