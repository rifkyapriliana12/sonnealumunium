# Sonne Aluminium - Admin Panel Setup Guide

## Persyaratan Server

1. **PHP** versi 7.4 atau lebih tinggi
2. **MySQL** versi 5.7 atau lebih tinggi
3. **Web Server** (Apache/Nginx) atau gunakan PHP built-in server

## Cara Setup

### 1. Setup Database

Buka browser dan akses:
```
http://localhost/sonnealumunium/admin/setup.php
```

Ini akan membuat:
- Database `sonne_aluminium`
- Semua tabel yang diperlukan
- Akun admin default

### 2. Login Admin

Akses halaman login:
```
http://localhost/sonnealumunium/admin/login.php
```

**Default Credentials:**
- Username: `admin`
- Password: `admin123`

### 3. Gunakan Frontend Dinamis

Rename `index-dynamic.php` menjadi `index.php` untuk menggunakan versi yang terhubung ke database.

## Struktur Folder

```
sonnealumunium/
├── admin/
│   ├── config.php          # Konfigurasi database
│   ├── setup.php           # Setup database (jalankan sekali)
│   ├── login.php           # Halaman login
│   ├── dashboard.php       # Dashboard admin
│   ├── sliders.php         # Kelola slider
│   ├── products.php        # Kelola produk
│   ├── projects.php        # Kelola proyek
│   ├── partners.php        # Kelola partner
│   ├── blog.php            # Kelola blog
│   ├── testimonials.php    # Kelola testimoni
│   ├── media.php           # Upload gambar/video
│   ├── settings.php        # Pengaturan site
│   ├── logout.php          # Logout
│   └── admin-style.css     # Style admin
├── uploads/
│   ├── sliders/            # Gambar slider
│   ├── products/           # Gambar produk
│   ├── projects/           # Gambar proyek
│   ├── partners/           # Logo partner
│   ├── blog/               # Gambar blog
│   └── media/              # Semua media
├── index.html              # Frontend statis
├── index-dynamic.php       # Frontend dinamis
├── style.css               # Style utama
└── script.js               # JavaScript
```

## Fitur Admin Panel

### 1. **Dashboard**
- Statistik jumlah konten
- Aksi cepat untuk tambah konten

### 2. **Kelola Slider**
- Tambah/edit/hapus slider
- Upload gambar background
- Atur urutan dan status aktif

### 3. **Kelola Produk**
- Kategori: Door, Window, Garages, Shower, Canopy
- Upload gambar produk
- Deskripsi dan link

### 4. **Kelola Proyek**
- Upload gambar proyek
- Judul dan deskripsi

### 5. **Kelola Partner**
- Upload logo partner
- Link ke website partner

### 6. **Kelola Blog**
- Tulis artikel blog
- Upload gambar featured
- Slug untuk SEO

### 7. **Kelola Testimoni**
- Tambah/edit hapus testimoni
- Nama dan posisi penulis

### 8. **Media Library**
- Upload gambar dan video
- Drag & drop upload
- Copy URL file

### 9. **Pengaturan**
- Info kontak
- Social media links
- Konfigurasi lainnya

## Upload Gambar/Video

### Cara Upload:
1. Login ke admin panel
2. Buka menu **Media**
3. Klik **Pilih File** atau drag & drop file
4. File akan otomatis terupload
5. Klik **Copy URL** untuk menggunakan file

### Format yang Didukung:
- **Gambar:** JPG, PNG, GIF, WebP
- **Video:** MP4, WebM, MOV, OGG
- **Ukuran Maks:** 50MB per file

## Konfigurasi Database

Edit file `admin/config.php` untuk mengubah konfigurasi:

```php
define('DB_HOST', 'localhost');    // Host database
define('DB_NAME', 'sonne_aluminium'); // Nama database
define('DB_USER', 'root');         // Username database
define('DB_PASS', '');             // Password database
```

## Troubleshooting

### Error: Connection failed
- Pastikan MySQL running
- Cek username/password di `config.php`

### Error: File upload gagal
- Cek folder `uploads/` ada dan writable
- Cek `upload_max_filesize` di php.ini

### Gambar tidak muncul
- Pastikan file ada di folder yang benar
- Cek permission folder uploads (755)

## Catatan Penting

1. **Jalankan `setup.php` sekali saja** untuk membuat database
2. **Hapus `setup.php`** di production untuk keamanan
3. **Ganti password admin** setelah setup
4. **Backup database** secara berkala
