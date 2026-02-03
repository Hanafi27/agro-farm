<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Create thumbnail for image file using PHP GD
     */
    public static function createThumbnail($sourcePath, $maxWidth = 300, $maxHeight = 300, $quality = 80)
    {
        try {
            // Check if source file exists
            if (!file_exists($sourcePath)) {
                return false;
            }

            // Get file info
            $pathInfo = pathinfo($sourcePath);
            $extension = strtolower($pathInfo['extension']);
            
            // Only process image files
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return false;
            }

            // Create image resource from file
            $sourceImage = null;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $sourceImage = imagecreatefromjpeg($sourcePath);
                    break;
                case 'png':
                    $sourceImage = imagecreatefrompng($sourcePath);
                    break;
                case 'gif':
                    $sourceImage = imagecreatefromgif($sourcePath);
                    break;
                case 'webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $sourceImage = imagecreatefromwebp($sourcePath);
                    }
                    break;
            }

            if (!$sourceImage) {
                return false;
            }

            // Get original dimensions
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // Calculate new dimensions
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = intval($originalWidth * $ratio);
            $newHeight = intval($originalHeight * $ratio);

            // Create thumbnail image
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG
            if ($extension === 'png') {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
                imagefill($thumbnail, 0, 0, $transparent);
            }

            // Resize image
            imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

            // Create thumbnail filename
            $thumbnailFilename = $pathInfo['filename'] . '_thumb.jpg';
            $thumbnailPath = storage_path('app/public/thumbnails/' . $thumbnailFilename);

            // Ensure thumbnails directory exists
            if (!file_exists(dirname($thumbnailPath))) {
                mkdir(dirname($thumbnailPath), 0755, true);
            }

            // Save thumbnail as JPEG
            $result = imagejpeg($thumbnail, $thumbnailPath, $quality);

            // Clean up memory
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);

            return $result ? $thumbnailFilename : false;

        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert image to base64 for PDF with resize
     */
    public static function imageToBase64($imagePath, $maxWidth = 400, $maxHeight = 400)
    {
        try {
            if (!file_exists($imagePath)) {
                return false;
            }

            $pathInfo = pathinfo($imagePath);
            $extension = strtolower($pathInfo['extension']);
            
            // Only process image files
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return false;
            }

            // Create image resource from file
            $sourceImage = null;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $sourceImage = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $sourceImage = imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $sourceImage = imagecreatefromgif($imagePath);
                    break;
                case 'webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $sourceImage = imagecreatefromwebp($imagePath);
                    }
                    break;
            }

            if (!$sourceImage) {
                return false;
            }

            // Get original dimensions
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);

            // Calculate new dimensions if image is too large
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
            
            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
                $newWidth = intval($originalWidth * $ratio);
                $newHeight = intval($originalHeight * $ratio);
            }

            // Create resized image if needed
            if ($newWidth !== $originalWidth || $newHeight !== $originalHeight) {
                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                
                // Preserve transparency for PNG
                if ($extension === 'png') {
                    imagealphablending($resizedImage, false);
                    imagesavealpha($resizedImage, true);
                    $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                    imagefill($resizedImage, 0, 0, $transparent);
                }

                // Resize image
                imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                
                // Use resized image
                $finalImage = $resizedImage;
            } else {
                $finalImage = $sourceImage;
            }

            // Convert to base64
            ob_start();
            imagejpeg($finalImage, null, 80);
            $imageData = ob_get_contents();
            ob_end_clean();
            
            $base64 = 'data:image/jpeg;base64,' . base64_encode($imageData);

            // Clean up memory
            imagedestroy($sourceImage);
            if (isset($resizedImage)) {
                imagedestroy($resizedImage);
            }

            return $base64;

        } catch (\Exception $e) {
            \Log::error('Base64 conversion failed: ' . $e->getMessage());
            return false;
        }
    }
}
