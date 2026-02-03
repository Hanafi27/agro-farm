# 🚀 Panduan Setup Ciwidey Agro Farm

Panduan lengkap untuk setup project Ciwidey Agro Farm dari awal hingga siap digunakan.

## 📋 Checklist Setup

- [ ] Install dependencies
- [ ] Setup environment
- [ ] Konfigurasi database
- [ ] Jalankan migration
- [ ] Jalankan seeder
- [ ] Setup assets
- [ ] Test aplikasi

## 🔧 Langkah-langkah Setup

### 1. **Persiapan Awal**

Pastikan sistem sudah terinstall:
- PHP 8.1+
- Composer 2.0+
- MySQL 8.0+
- Node.js 16.0+
- Git

### 2. **Clone & Install Dependencies**

```bash
# Clone repository
git clone <repository-url>
cd agro-farm

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. **Setup Environment**

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. **Konfigurasi Database**

Edit file `.env`:
```env
APP_NAME="Ciwidey Agro Farm"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agro_farm_db
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 5. **Buat Database**

```sql
-- Login ke MySQL
mysql -u root -p

-- Buat database
CREATE DATABASE agro_farm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Verifikasi database dibuat
SHOW DATABASES;
```

### 6. **Jalankan Migration**

```bash
# Jalankan semua migration
php artisan migrate

# Jika ada error, coba fresh migration
php artisan migrate:fresh
```

### 7. **Jalankan Seeder**

```bash
# Jalankan semua seeder
php artisan db:seed

# Atau jalankan seeder tertentu
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=PengajuanDanaTestSeeder
php artisan db:seed --class=PendapatanSusuTestSeeder
```

### 8. **Setup Assets**

```bash
# Build assets untuk production
npm run build

# Atau jalankan dalam mode development
npm run dev
```

### 9. **Setup Storage**

```bash
# Buat symbolic link untuk storage
php artisan storage:link

# Set permission (Linux/Mac)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Set permission (Windows)
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T
```

### 10. **Install Additional Packages**

```bash
# Install Excel export package
composer require maatwebsite/excel

# Install PDF export package
composer require barryvdh/laravel-dompdf

# Publish config files
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 11. **Clear Cache**

```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache untuk production
php artisan config:cache
php artisan route:cache
```

### 12. **Test Aplikasi**

```bash
# Jalankan development server
php artisan serve

# Buka browser dan akses
# http://localhost:8000
```

## 👤 Akun Default

Setelah menjalankan seeder, tersedia akun default:

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Owner** | owner@agrofarm.com | password | Full access, approval |
| **Admin** | admin@agrofarm.com | password | Management, input data |
| **Keuangan** | keuangan@agrofarm.com | password | Finance operations |
| **Pegawai 1** | pegawai1@agrofarm.com | password | View personal data |
| **Pegawai 2** | pegawai2@agrofarm.com | password | View personal data |

## 🎯 Verifikasi Setup

### 1. **Test Login**
- Buka http://localhost:8000
- Login dengan akun default
- Pastikan dashboard muncul

### 2. **Test Fitur Utama**
- [ ] Manajemen pegawai
- [ ] Input absensi
- [ ] Generate penggajian
- [ ] Input pendapatan susu
- [ ] Buat pengajuan dana
- [ ] Export PDF/Excel

### 3. **Test Export**
- [ ] Export absensi ke PDF
- [ ] Export absensi ke Excel
- [ ] Export slip gaji ke PDF
- [ ] Export laporan realisasi ke Excel

## 🔧 Konfigurasi Tambahan

### **Email Configuration**
Jika ingin menggunakan email, edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### **Queue Configuration**
Untuk background jobs, edit `.env`:
```env
QUEUE_CONNECTION=database
```

Lalu jalankan:
```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

### **File Upload Configuration**
Pastikan folder upload bisa diakses:
```bash
# Buat folder upload
mkdir -p public/uploads
chmod 775 public/uploads
```

## 🚨 Troubleshooting

### **Error: "Class not found"**
```bash
composer dump-autoload
```

### **Error: "Database connection failed"**
- Periksa MySQL server berjalan
- Periksa konfigurasi database di `.env`
- Pastikan database `agro_farm_db` sudah dibuat

### **Error: "Permission denied"**
```bash
# Linux/Mac
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Windows
icacls storage /grant Everyone:F /T
```

### **Error: "Excel/PDF export not working"**
```bash
# Install packages
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf

# Publish config
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### **Error: "Assets not loading"**
```bash
# Build assets
npm run build

# Atau jalankan dev server
npm run dev
```

## 📞 Support

Jika mengalami masalah:
1. Periksa error log di `storage/logs/laravel.log`
2. Pastikan semua langkah setup sudah benar
3. Hubungi tim support

---

**Setup selesai! Aplikasi siap digunakan.** 🎉 