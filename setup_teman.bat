@echo off
echo ========================================
echo    AGRO FARM - SETUP OTOMATIS
echo ========================================
echo.

echo [1/8] Installing Composer dependencies...
composer install
if %errorlevel% neq 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)

echo.
echo [2/8] Setting up environment...
if not exist .env (
    copy .env.example .env
    echo File .env created from .env.example
) else (
    echo File .env already exists
)

echo.
echo [3/8] Generating application key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo ERROR: Key generation failed!
    pause
    exit /b 1
)

echo.
echo [4/8] Creating storage link...
php artisan storage:link
if %errorlevel% neq 0 (
    echo WARNING: Storage link failed, trying to remove existing link...
    rmdir /s /q public\storage
    php artisan storage:link
)

echo.
echo [5/8] Running database migrations...
php artisan migrate
if %errorlevel% neq 0 (
    echo ERROR: Database migration failed!
    echo Please check your database configuration in .env file
    pause
    exit /b 1
)

echo.
echo [6/8] Clearing cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo.
echo [7/8] Testing export functionality...
php artisan tinker --execute="try { \$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test PDF</h1>'); echo 'PDF Export: OK'; } catch (Exception \$e) { echo 'PDF Export: FAILED - ' . \$e->getMessage(); }"
php artisan tinker --execute="try { \$excel = \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LabaRugiExport(1, 2024), 'test.xlsx'); echo 'Excel Export: OK'; } catch (Exception \$e) { echo 'Excel Export: FAILED - ' . \$e->getMessage(); }"

echo.
echo [8/8] Setup completed!
echo.
echo ========================================
echo    SETUP BERHASIL!
echo ========================================
echo.
echo Untuk menjalankan aplikasi:
echo   php artisan serve
echo.
echo Kemudian buka: http://127.0.0.1:8000
echo.
echo Default login:
echo   Email: admin@agro.com
echo   Password: password
echo.
echo Jika ada masalah, cek file PANDUAN_INSTALASI_TEMAN.md
echo.
pause
