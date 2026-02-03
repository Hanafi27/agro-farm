<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\ImageHelper;
use App\Models\LaporanRealisasiItemAttachment;

class CreateThumbnails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:create-thumbnails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create thumbnails for all attachment images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🖼️  Creating thumbnails for attachment images...');

        // Get all attachments
        $attachments = LaporanRealisasiItemAttachment::all();
        $this->info("Found {$attachments->count()} attachments");

        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($attachments as $attachment) {
            $path = storage_path('app/public/' . $attachment->path);
            
            if (!file_exists($path)) {
                $this->warn("File not found: {$attachment->filename}");
                $errors++;
                continue;
            }

            $pathInfo = pathinfo($path);
            $extension = strtolower($pathInfo['extension']);
            
            // Only process image files
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $this->line("Skipping non-image file: {$attachment->filename}");
                $skipped++;
                continue;
            }

            // Check if thumbnail already exists
            $thumbnailFilename = $pathInfo['filename'] . '_thumb.' . $extension;
            $thumbnailPath = storage_path('app/public/thumbnails/' . $thumbnailFilename);
            
            if (file_exists($thumbnailPath)) {
                $this->line("Thumbnail already exists: {$attachment->filename}");
                $skipped++;
                continue;
            }

            // Create thumbnail
            $result = ImageHelper::createThumbnail($path, 300, 300, 80);
            
            if ($result) {
                $this->info("✅ Created thumbnail for: {$attachment->filename}");
                $created++;
            } else {
                $this->error("❌ Failed to create thumbnail for: {$attachment->filename}");
                $errors++;
            }
        }

        $this->info("\n📊 Summary:");
        $this->info("   - Created: {$created}");
        $this->info("   - Skipped: {$skipped}");
        $this->info("   - Errors: {$errors}");
        
        if ($created > 0) {
            $this->info("\n🎉 Thumbnails created successfully!");
        }

        return Command::SUCCESS;
    }
}