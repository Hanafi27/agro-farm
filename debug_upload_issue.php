<?php

require_once 'vendor/autoload.php';

use App\Models\PengajuanDana;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG UPLOAD ISSUE ===\n\n";

// Test file upload simulation
$originalName = 'test_upload_' . date('Y-m-d_H-i-s') . '.jpg';
$timestamp = time();
$fileName = $timestamp . '_' . $originalName;

echo "=== TEST PARAMETERS ===\n";
echo "Original Name: {$originalName}\n";
echo "Timestamp: {$timestamp}\n";
echo "File Name: {$fileName}\n\n";

// Test different storage paths
echo "=== TEST STORAGE PATHS ===\n";

// Method 1: Using storeAs
echo "Method 1: Using storeAs\n";
$stored = 'public/bukti_transfer/' . $fileName;
$filePath1 = storage_path('app/' . $stored);
echo "Stored Path: {$stored}\n";
echo "Full Path: {$filePath1}\n";

// Method 2: Direct path construction
echo "\nMethod 2: Direct path construction\n";
$filePath2 = storage_path('app/public/bukti_transfer/' . $fileName);
echo "Direct Path: {$filePath2}\n";

// Check if directories exist
echo "\n=== DIRECTORY CHECK ===\n";
$storageDir = storage_path('app/public/bukti_transfer');
$publicDir = public_path('storage/bukti_transfer');

echo "Storage Directory: {$storageDir}\n";
echo "Public Directory: {$publicDir}\n\n";

if (is_dir($storageDir)) {
    echo "✅ Storage directory exists\n";
    echo "   Writable: " . (is_writable($storageDir) ? 'Yes' : 'No') . "\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($storageDir)), -4) . "\n";
} else {
    echo "❌ Storage directory does not exist\n";
}

if (is_dir($publicDir)) {
    echo "✅ Public directory exists\n";
    echo "   Writable: " . (is_writable($publicDir) ? 'Yes' : 'No') . "\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($publicDir)), -4) . "\n";
} else {
    echo "❌ Public directory does not exist\n";
}

// Check symlink
echo "\n=== SYMLINK CHECK ===\n";
if (is_link($publicDir)) {
    echo "✅ Public directory is a symlink\n";
    echo "   Target: " . readlink($publicDir) . "\n";
} else {
    echo "❌ Public directory is not a symlink\n";
}

// Create test file
echo "\n=== CREATE TEST FILE ===\n";
$testFilePath = $storageDir . '/' . $fileName;

// Create image
$width = 300;
$height = 200;
$image = imagecreate($width, $height);

// Set colors
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);

// Fill background
imagefill($image, 0, 0, $white);

// Add text
imagestring($image, 5, 50, 80, "TEST UPLOAD FILE", $black);
imagestring($image, 3, 50, 120, "Created: " . date('Y-m-d H:i:s'), $black);

// Save image
$saved = imagejpeg($image, $testFilePath, 90);
imagedestroy($image);

if ($saved) {
    echo "✅ Test file created successfully\n";
    echo "   Path: {$testFilePath}\n";
    echo "   Size: " . filesize($testFilePath) . " bytes\n";
    echo "   Exists: " . (file_exists($testFilePath) ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Failed to create test file\n";
}

// Test Laravel's storeAs method simulation
echo "\n=== LARAVEL STOREAS SIMULATION ===\n";

// Simulate what Laravel's storeAs does
$storedPath = 'public/bukti_transfer/' . $fileName;
$laravelPath = storage_path('app/' . $storedPath);

echo "Laravel Stored Path: {$storedPath}\n";
echo "Laravel Full Path: {$laravelPath}\n";
echo "File exists at Laravel path: " . (file_exists($laravelPath) ? 'Yes' : 'No') . "\n";

// Test public access
echo "\n=== PUBLIC ACCESS TEST ===\n";
$publicPath = public_path('storage/bukti_transfer/' . $fileName);
echo "Public Path: {$publicPath}\n";
echo "File exists at public path: " . (file_exists($publicPath) ? 'Yes' : 'No') . "\n";

// List files in storage
echo "\n=== STORAGE CONTENTS ===\n";
$files = glob($storageDir . '/*');
echo "Files in storage directory:\n";
foreach ($files as $file) {
    $filename = basename($file);
    $size = filesize($file);
    echo "  - {$filename} ({$size} bytes)\n";
}

echo "\n=== SELESAI ===\n";
