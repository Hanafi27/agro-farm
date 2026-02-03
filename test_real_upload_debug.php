<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST REAL UPLOAD DEBUG ===\n\n";

// Simulate the exact upload process
$originalName = 'telu.png';
$timestamp = time();
$fileName = $timestamp . '_' . $originalName;

echo "=== UPLOAD PARAMETERS ===\n";
echo "Original Name: {$originalName}\n";
echo "Timestamp: {$timestamp}\n";
echo "File Name: {$fileName}\n\n";

// Test storage paths
$storageDir = storage_path('app/public/bukti_transfer');
$publicDir = public_path('storage/bukti_transfer');

echo "=== DIRECTORY CHECK ===\n";
echo "Storage Directory: {$storageDir}\n";
echo "Public Directory: {$publicDir}\n\n";

if (is_dir($storageDir)) {
    echo "✅ Storage directory exists\n";
    echo "   Writable: " . (is_writable($storageDir) ? 'Yes' : 'No') . "\n";
    echo "   Permissions: " . substr(sprintf('%o', fileperms($storageDir)), -4) . "\n";
} else {
    echo "❌ Storage directory does not exist\n";
}

// Create test file
$testFilePath = $storageDir . '/' . $fileName;

echo "\n=== CREATE TEST FILE ===\n";
echo "Test File Path: {$testFilePath}\n";

// Create simple image
$image = imagecreate(300, 200);
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
imagefill($image, 0, 0, $white);
imagestring($image, 5, 50, 80, "TEST UPLOAD DEBUG", $black);
imagestring($image, 3, 50, 120, "File: {$fileName}", $black);
$saved = imagejpeg($image, $testFilePath, 90);
imagedestroy($image);

if ($saved) {
    echo "✅ Test file created successfully\n";
    echo "   Path: {$testFilePath}\n";
    echo "   Size: " . filesize($testFilePath) . " bytes\n";
    echo "   Exists: " . (file_exists($testFilePath) ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Failed to create test file\n";
    echo "   Error: " . error_get_last()['message'] . "\n";
}

// Test Laravel's storeAs simulation
echo "\n=== LARAVEL STOREAS SIMULATION ===\n";

// Simulate what Laravel's storeAs does
$storedPath = 'public/bukti_transfer/' . $fileName;
$laravelPath = storage_path('app/' . $storedPath);

echo "Stored Path: {$storedPath}\n";
echo "Laravel Path: {$laravelPath}\n";
echo "File exists at Laravel path: " . (file_exists($laravelPath) ? 'Yes' : 'No') . "\n";

// Test the exact path from error message
echo "\n=== ERROR PATH TEST ===\n";
$errorPath = 'D:\\xampp\\htdocs\\TA\\agro-farm\\storage\\app\\public\\bukti_transfer\\1755869013_telu.png';
echo "Error Path: {$errorPath}\n";
echo "File exists at error path: " . (file_exists($errorPath) ? 'Yes' : 'No') . "\n";

// Test current working directory
echo "\n=== WORKING DIRECTORY ===\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Storage Path Function: " . storage_path('app/public/bukti_transfer') . "\n";

// Test file operations
echo "\n=== FILE OPERATIONS TEST ===\n";

// Try to create a file directly
$directPath = $storageDir . '/test_direct.txt';
$content = "Test content created at " . date('Y-m-d H:i:s');
$written = file_put_contents($directPath, $content);

if ($written !== false) {
    echo "✅ Direct file write successful\n";
    echo "   Path: {$directPath}\n";
    echo "   Bytes written: {$written}\n";
    echo "   File exists: " . (file_exists($directPath) ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Direct file write failed\n";
    echo "   Error: " . error_get_last()['message'] . "\n";
}

// List files in directory
echo "\n=== DIRECTORY CONTENTS ===\n";
$files = glob($storageDir . '/*');
echo "Files in storage directory (" . count($files) . " files):\n";
foreach (array_slice($files, 0, 5) as $file) {
    $filename = basename($file);
    $size = filesize($file);
    echo "  - {$filename} ({$size} bytes)\n";
}
if (count($files) > 5) {
    echo "  ... and " . (count($files) - 5) . " more files\n";
}

echo "\n=== SELESAI ===\n";
