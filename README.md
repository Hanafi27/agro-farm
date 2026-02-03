# 🚜 Ciwidey Agro Farm Management System

Sistem manajemen pertanian dan peternakan untuk Ciwidey Agro Farm yang mencakup pengelolaan pegawai, absensi, penggajian, pendapatan susu, pengajuan dana, dan laporan realisasi.

## 📋 Daftar Isi

- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi Database](#konfigurasi-database)
- [Migration & Seeding](#migration--seeding)
- [Perintah Penting](#perintah-penting)
- [Struktur Project](#struktur-project)
- [Fitur Utama](#fitur-utama)
- [Role & Akses](#role--akses)
- [Troubleshooting](#troubleshooting)

## 🖥️ Persyaratan Sistem

- **PHP**: 8.1 atau lebih tinggi
- **Composer**: 2.0 atau lebih tinggi
- **MySQL**: 8.0 atau lebih tinggi
- **Node.js**: 16.0 atau lebih tinggi (untuk asset compilation)
- **Web Server**: Apache/Nginx atau Laravel Sail

## ⚙️ Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd agro-farm
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (jika menggunakan Vite)
npm install
```

### 3. Setup Environment
```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agro_farm_db
DB_USERNAME=root
DB_PASSWORD=
```

## 🗄️ Konfigurasi Database

### Membuat Database
```sql
CREATE DATABASE agro_farm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Menjalankan Migration
```bash
# Jalankan semua migration
php artisan migrate

# Reset database dan jalankan ulang migration
php artisan migrate:fresh

# Rollback migration terakhir
php artisan migrate:rollback
```

### Menjalankan Seeder
```bash
# Jalankan semua seeder
php artisan db:seed

# Jalankan seeder tertentu
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=PengajuanDanaTestSeeder
php artisan db:seed --class=PendapatanSusuTestSeeder
```

## 🔧 Perintah Penting

### Development Server
```bash
# Menjalankan development server
php artisan serve

# Menjalankan dengan host dan port tertentu
php artisan serve --host=127.0.0.1 --port=8000
```

### Cache Management
```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Asset Compilation
```bash
# Development mode
npm run dev

# Production build
npm run build

# Watch mode untuk development
npm run watch
```

### Database Commands
```bash
# Lihat status migration
php artisan migrate:status

# Reset dan seed database
php artisan migrate:fresh --seed

# Backup database
php artisan db:backup

# Restore database
php artisan db:restore
```

### Excel & PDF Export
```bash
# Install dependencies untuk Excel export
composer require maatwebsite/excel

# Install dependencies untuk PDF export
composer require barryvdh/laravel-dompdf

# Publish config files
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### Queue & Jobs
```bash
# Menjalankan queue worker
php artisan queue:work

# Menjalankan queue dengan timeout
php artisan queue:work --timeout=60

# Clear failed jobs
php artisan queue:flush
```

### Maintenance Mode
```bash
# Aktifkan maintenance mode
php artisan down

# Nonaktifkan maintenance mode
php artisan up

# Maintenance mode dengan secret token
php artisan down --secret="1630542a-246b-4b66-afa1-dd72a4c43515"
```

## 📁 Struktur Project

```
agro-farm/
├── app/
│   ├── Http/Controllers/     # Controller untuk semua modul
│   ├── Models/              # Eloquent models
│   ├── Exports/             # Excel export classes
│   └── Providers/           # Service providers
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── views/              # Blade templates
│   ├── css/               # Stylesheets
│   └── js/                # JavaScript files
├── routes/
│   └── web.php            # Web routes
├── public/
│   └── asset/             # Public assets (logo, images)
└── storage/
    └── logs/              # Application logs
```

## 🎯 Fitur Utama

### 1. **Manajemen Pegawai**
- CRUD pegawai dengan role-based access
- Data pribadi dan jabatan
- Gaji pokok per pegawai

### 2. **Sistem Absensi**
- Input absensi harian (hadir/izin/alfa)
- Filter berdasarkan tanggal, pegawai, dan status
- Export PDF dan Excel
- Statistik kehadiran

### 3. **Sistem Penggajian**
- Perhitungan gaji otomatis berdasarkan absensi
- Generate penggajian massal
- Slip gaji dengan logo perusahaan
- Export PDF slip gaji

### 4. **Pendapatan Susu**
- Input pendapatan dari peternakan
- Kategori: susu sapi, susu kambing, teh
- Perhitungan otomatis (jumlah × harga)
- Filter berdasarkan bulan dan kategori

### 5. **Pengajuan Dana**
- Workflow: Draft → Submit → Approved → Realized
- Item-based pengajuan dengan detail kebutuhan
- Role-based approval (Admin → Owner → Keuangan)
- Mass actions untuk setiap role

### 6. **Laporan Realisasi**
- Kombinasi data pendapatan dan pengajuan dana
- Filter berdasarkan divisi, bulan, dan minggu
- Export PDF dan Excel dengan multiple sheets
- Auto-include pendapatan berdasarkan bulan

## 👥 Role & Akses

### **Admin**
- Manajemen pegawai
- Input absensi
- Generate penggajian
- Input pendapatan susu
- Buat pengajuan dana
- Kirim pengajuan ke owner
- Lihat semua laporan

### **Owner**
- Approval pengajuan dana
- Lihat laporan realisasi
- Export PDF/Excel laporan
- Approve laporan realisasi

### **Keuangan**
- Pencairan dana (realize)
- Lihat laporan realisasi
- Export PDF/Excel laporan

### **Pegawai**
- Lihat data pribadi
- Lihat slip gaji
- Lihat absensi pribadi

## 🔍 Troubleshooting

### Error: "Could not open input file: artisan"
```bash
# Pastikan berada di direktori yang benar
cd agro-farm
ls artisan  # Pastikan file artisan ada
```

### Error: "Class not found"
```bash
# Clear autoload cache
composer dump-autoload
```

### Error: "Database connection failed"
```bash
# Periksa konfigurasi database di .env
# Pastikan MySQL server berjalan
# Periksa username dan password database
```

### Error: "Permission denied"
```bash
# Set permission untuk storage dan bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Error: "Excel export not working"
```bash
# Install Excel package
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

### Error: "PDF export not working"
```bash
# Install DomPDF package
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## 📞 Support

Untuk bantuan teknis atau pertanyaan, silakan hubungi:
- **Email**: hanafiilham333@gmail.com- 