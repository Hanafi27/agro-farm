<?php

echo "========================================" . PHP_EOL;
echo "    AGRO FARM - DEPENDENCY CHECKER" . PHP_EOL;
echo "========================================" . PHP_EOL;
echo PHP_EOL;

$errors = [];
$warnings = [];

// Check PHP Version
echo "Checking PHP Version..." . PHP_EOL;
$phpVersion = PHP_VERSION;
echo "PHP Version: " . $phpVersion . PHP_EOL;
if (version_compare($phpVersion, '8.2.0', '<')) {
    $errors[] = "PHP version must be 8.2 or higher. Current: " . $phpVersion;
} else {
    echo "✓ PHP version OK" . PHP_EOL;
}
echo PHP_EOL;

// Check Required PHP Extensions
echo "Checking PHP Extensions..." . PHP_EOL;
$requiredExtensions = [
    'gd' => 'Image processing (required for PDF)',
    'mbstring' => 'Multibyte string handling',
    'zip' => 'ZIP archive handling (required for Excel)',
    'xml' => 'XML processing',
    'curl' => 'HTTP client',
    'openssl' => 'SSL/TLS support',
    'pdo_mysql' => 'MySQL database support',
    'fileinfo' => 'File type detection',
    'iconv' => 'Character encoding conversion'
];

foreach ($requiredExtensions as $ext => $description) {
    if (extension_loaded($ext)) {
        echo "✓ $ext - $description" . PHP_EOL;
    } else {
        $errors[] = "Missing extension: $ext - $description";
        echo "✗ $ext - $description (MISSING)" . PHP_EOL;
    }
}
echo PHP_EOL;

// Check Composer
echo "Checking Composer..." . PHP_EOL;
if (file_exists('composer.json')) {
    echo "✓ composer.json found" . PHP_EOL;
} else {
    $errors[] = "composer.json not found";
    echo "✗ composer.json not found" . PHP_EOL;
}

if (file_exists('vendor/autoload.php')) {
    echo "✓ Composer autoload found" . PHP_EOL;
} else {
    $errors[] = "Composer dependencies not installed. Run: composer install";
    echo "✗ Composer dependencies not installed" . PHP_EOL;
}
echo PHP_EOL;

// Check Required Packages
echo "Checking Required Packages..." . PHP_EOL;
$requiredPackages = [
    'barryvdh/laravel-dompdf' => 'PDF generation',
    'maatwebsite/excel' => 'Excel import/export',
    'intervention/image' => 'Image processing'
];

foreach ($requiredPackages as $package => $description) {
    $packagePath = "vendor/" . str_replace('/', '/', $package);
    if (is_dir($packagePath)) {
        echo "✓ $package - $description" . PHP_EOL;
    } else {
        $errors[] = "Missing package: $package - $description";
        echo "✗ $package - $description (MISSING)" . PHP_EOL;
    }
}
echo PHP_EOL;

// Check Laravel Files
echo "Checking Laravel Files..." . PHP_EOL;
$laravelFiles = [
    '.env' => 'Environment configuration',
    'artisan' => 'Laravel command line tool',
    'app/Http/Controllers' => 'Controllers directory',
    'resources/views' => 'Views directory',
    'storage' => 'Storage directory',
    'bootstrap/cache' => 'Bootstrap cache directory'
];

foreach ($laravelFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✓ $file - $description" . PHP_EOL;
    } else {
        $warnings[] = "Missing file/directory: $file - $description";
        echo "✗ $file - $description (MISSING)" . PHP_EOL;
    }
}
echo PHP_EOL;

// Check Storage Permissions
echo "Checking Storage Permissions..." . PHP_EOL;
$storageDirs = ['storage', 'bootstrap/cache', 'public/storage'];

foreach ($storageDirs as $dir) {
    if (file_exists($dir)) {
        if (is_writable($dir)) {
            echo "✓ $dir is writable" . PHP_EOL;
        } else {
            $warnings[] = "Directory $dir is not writable";
            echo "✗ $dir is not writable" . PHP_EOL;
        }
    } else {
        $warnings[] = "Directory $dir does not exist";
        echo "✗ $dir does not exist" . PHP_EOL;
    }
}
echo PHP_EOL;

// Check Database Connection
echo "Checking Database Connection..." . PHP_EOL;
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    if (strpos($envContent, 'DB_CONNECTION=mysql') !== false) {
        echo "✓ Database connection configured for MySQL" . PHP_EOL;
    } else {
        $warnings[] = "Database connection not configured for MySQL";
        echo "✗ Database connection not configured for MySQL" . PHP_EOL;
    }
} else {
    $warnings[] = ".env file not found";
    echo "✗ .env file not found" . PHP_EOL;
}
echo PHP_EOL;

// Summary
echo "========================================" . PHP_EOL;
echo "    SUMMARY" . PHP_EOL;
echo "========================================" . PHP_EOL;

if (empty($errors) && empty($warnings)) {
    echo "✓ All checks passed! Your setup is ready." . PHP_EOL;
} else {
    if (!empty($errors)) {
        echo "ERRORS (must be fixed):" . PHP_EOL;
        foreach ($errors as $error) {
            echo "✗ $error" . PHP_EOL;
        }
        echo PHP_EOL;
    }
    
    if (!empty($warnings)) {
        echo "WARNINGS (should be fixed):" . PHP_EOL;
        foreach ($warnings as $warning) {
            echo "⚠ $warning" . PHP_EOL;
        }
        echo PHP_EOL;
    }
}

echo "========================================" . PHP_EOL;
echo "    RECOMMENDED ACTIONS" . PHP_EOL;
echo "========================================" . PHP_EOL;

if (!empty($errors)) {
    echo "1. Fix all ERRORS above" . PHP_EOL;
    echo "2. Run: composer install" . PHP_EOL;
    echo "3. Run: php artisan key:generate" . PHP_EOL;
    echo "4. Run: php artisan storage:link" . PHP_EOL;
    echo "5. Run: php artisan migrate" . PHP_EOL;
} else {
    echo "1. Run: php artisan serve" . PHP_EOL;
    echo "2. Open: http://127.0.0.1:8000" . PHP_EOL;
    echo "3. Test PDF and Excel export functionality" . PHP_EOL;
}

echo PHP_EOL;
echo "For detailed installation guide, see: PANDUAN_INSTALASI_TEMAN.md" . PHP_EOL;
echo "========================================" . PHP_EOL;
