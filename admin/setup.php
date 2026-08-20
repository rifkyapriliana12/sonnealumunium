<?php
// Database Setup Script
// Run this file once to create database and tables

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$user;host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sonne_aluminium CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE sonne_aluminium");

    // Admin Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Sliders Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS sliders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        subtitle TEXT,
        button_text VARCHAR(100),
        button_url VARCHAR(255),
        image VARCHAR(255),
        overlay_opacity DECIMAL(3,2) DEFAULT 0.40,
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Products Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(50) NOT NULL,
        label VARCHAR(50),
        title VARCHAR(255) NOT NULL,
        description TEXT,
        button_text VARCHAR(100),
        button_url VARCHAR(255),
        image VARCHAR(255),
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Projects Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        url VARCHAR(255),
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Partners Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS partners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        logo VARCHAR(255),
        url VARCHAR(255),
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Blog Posts Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        excerpt TEXT,
        content LONGTEXT,
        image VARCHAR(255),
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Testimonials Table (drop old schema if exists)
    $pdo->exec("DROP TABLE IF EXISTS testimonials");
    $pdo->exec("CREATE TABLE testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(100) NOT NULL,
        company VARCHAR(150),
        message TEXT NOT NULL,
        photo VARCHAR(255),
        rating INT DEFAULT 5,
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Pages Table (for static content)
    $pdo->exec("CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(100) NOT NULL UNIQUE,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT,
        meta_title VARCHAR(255),
        meta_description TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Media Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255),
        file_type VARCHAR(50),
        file_size INT,
        uploaded_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uploaded_by) REFERENCES admin_users(id)
    )");

    // Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        setting_type VARCHAR(20) DEFAULT 'text',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Insert Default Admin User (password: admin123)
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', $hashedPassword, 'Administrator', 'admin@sonnealuminium.com']);

    // Insert Default Settings
    $settings = [
        ['site_name', 'Sonne Aluminium', 'text'],
        ['site_email', 'info@sonnealuminium.com', 'text'],
        ['site_phone', '+62 21 2252 5999', 'text'],
        ['site_whatsapp', '6281282006363', 'text'],
        ['site_address', 'Jalan Raya Narogong Km7, Bekasi, Indonesia 17116', 'textarea'],
        ['instagram_url', 'https://www.instagram.com/sonnealuminium/', 'text'],
        ['facebook_url', '#', 'text'],
        ['youtube_url', 'https://youtube.com/@sonnealuminium', 'text'],
        ['tiktok_url', 'https://www.tiktok.com/@sonne.aluminium/', 'text'],
        ['linkedin_url', '#', 'text'],
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)");
    foreach ($settings as $setting) {
        $stmt->execute($setting);
    }

    // Insert Sample Data
    // Sample Sliders
    $pdo->exec("INSERT INTO sliders (title, subtitle, button_text, button_url, image, sort_order) VALUES
        ('HEATPROOF AND SOUNDPROOF : DOORS AND WINDOW', 'Kami menawarkan teknologi thermal-break yang terinspirasi dari Jerman', 'LIHAT PRODUCT', '/produk/', 'slider-1.jpg', 1),
        ('EXQUISITES', 'Sliding Door, Folding Door, Swing Door', 'EXPLORE THE COLLECTION', '/produk/pintu/', 'slider-2.jpg', 2),
        ('PERFECT MATCH', 'Sidehung Window, Tophung Window, Sliding Window', 'EXPLORE THE COLLECTION', '/produk/jendela/', 'slider-3.jpg', 3)
    ");

    // Sample Products
    $pdo->exec("INSERT INTO products (category, label, title, description, button_text, button_url, image, sort_order) VALUES
        ('door', 'EXQUISITES', 'Sliding Door, Folding Door, Swing Door', 'Sliding Door dan Folding Door yang dirancang dengan stainless steel rel yang anti karat dan smooth', 'EXPLORE THE COLLECTION', '/produk/pintu/', 'product-door.jpg', 1),
        ('window', 'PERFECT MATCH', 'Sidehung Window, Tophung Window, Sliding Window', 'Dengan desain yang elegan dan fungsional, sudut adu manis 45 derajat', 'EXPLORE THE COLLECTION', '/produk/jendela/', 'product-window.jpg', 2),
        ('other', 'FRESH', 'Gerage, Bathroom Cubicle, Canopy & Railing', 'Kita mengenalkan produk inovatif dengan cepat untuk beradaptasi dengan pasar', 'EXPLORE THE COLLECTION', '/produk/lainnya/', 'product-other.jpg', 3)
    ");

    // Sample Testimonials
    $pdo->exec("INSERT INTO testimonials (client_name, company, message, rating, sort_order) VALUES
        ('Ms. M', 'Perusahaan FMCG', 'SONNE service is very pleasant. Product education is detailed, information is transparent, and their price is competitive for above-average product specifications.', 5, 1),
        ('Mrs. S', 'Startup Dekakorn', 'Saya sangat mementingkan estetika produk dan tim SONNE sangat senang dengan hal tersebut. Mereka menunjukan pada saya berbagai opsi warna.', 5, 2),
        ('Mr. H', 'Perusahaan Import', 'Saya sangat senang dengan tim operasional SONNE. Mereka membantu berdiskusi kesulitan-kesulitan konstruksi sipil.', 5, 3)
    ");

    // Create uploads directory
    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        mkdir($uploadDir . '/sliders', 0755, true);
        mkdir($uploadDir . '/products', 0755, true);
        mkdir($uploadDir . '/projects', 0755, true);
        mkdir($uploadDir . '/partners', 0755, true);
        mkdir($uploadDir . '/blog', 0755, true);
        mkdir($uploadDir . '/media', 0755, true);
    }

    echo "<h1>Database Setup Complete!</h1>";
    echo "<p>Database 'sonne_aluminium' has been created with all tables.</p>";
    echo "<p>Default admin account:</p>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> admin</li>";
    echo "<li><strong>Password:</strong> admin123</li>";
    echo "</ul>";
    echo "<p><a href='login.php'>Go to Admin Login</a></p>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
