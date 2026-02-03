<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixPengajuanDanaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Check if table exists
            if (!Schema::hasTable('pengajuan_danas')) {
                $this->command->info('Table pengajuan_danas does not exist. Creating it...');
                
                // Create table with new structure
                DB::statement("
                    CREATE TABLE pengajuan_danas (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        tanggal DATE NOT NULL,
                        divisi ENUM('peternakan', 'perkebunan') NOT NULL,
                        minggu INT NOT NULL,
                        bulan INT NOT NULL,
                        tahun INT NOT NULL,
                        status ENUM('draft', 'pending', 'approved', 'rejected', 'realized') DEFAULT 'draft',
                        submitted_by BIGINT UNSIGNED NOT NULL,
                        approved_by BIGINT UNSIGNED NULL,
                        rejected_by BIGINT UNSIGNED NULL,
                        alasan_rejection TEXT NULL,
                        tanggal_approval TIMESTAMP NULL,
                        realized_by BIGINT UNSIGNED NULL,
                        tanggal_realisasi TIMESTAMP NULL,
                        nominal_diberikan DECIMAL(12,2) NULL,
                        keterangan TEXT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
                        FOREIGN KEY (rejected_by) REFERENCES users(id) ON DELETE SET NULL,
                        FOREIGN KEY (realized_by) REFERENCES users(id) ON DELETE SET NULL
                    )
                ");
                
                $this->command->info('Table pengajuan_danas created successfully!');
            } else {
                $this->command->info('Table pengajuan_danas exists. Checking structure...');
                
                // Check if we need to update the status column
                $columns = DB::select("SHOW COLUMNS FROM pengajuan_danas LIKE 'status'");
                if (!empty($columns)) {
                    $statusColumn = $columns[0];
                    if (strpos($statusColumn->Type, 'draft') === false) {
                        $this->command->info('Updating status column...');
                        DB::statement("ALTER TABLE pengajuan_danas MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'rejected', 'realized') DEFAULT 'draft'");
                        $this->command->info('Status column updated successfully!');
                    } else {
                        $this->command->info('Status column already has correct enum values.');
                    }
                } else {
                    $this->command->info('Adding status column...');
                    DB::statement("ALTER TABLE pengajuan_danas ADD COLUMN status ENUM('draft', 'pending', 'approved', 'rejected', 'realized') DEFAULT 'draft'");
                    $this->command->info('Status column added successfully!');
                }
                
                // Get first user ID for default values
                $firstUserId = DB::table('users')->value('id');
                if (!$firstUserId) {
                    $this->command->error('No users found in database. Please create a user first.');
                    return;
                }
                
                // Check and add missing columns with default values
                $requiredColumns = [
                    'tanggal' => "DATE NOT NULL DEFAULT '2025-01-01'",
                    'divisi' => "ENUM('peternakan', 'perkebunan') NOT NULL DEFAULT 'peternakan'",
                    'minggu' => "INT NOT NULL DEFAULT 1",
                    'bulan' => "INT NOT NULL DEFAULT 1",
                    'tahun' => "INT NOT NULL DEFAULT 2025",
                    'submitted_by' => "BIGINT UNSIGNED NOT NULL DEFAULT $firstUserId",
                    'approved_by' => "BIGINT UNSIGNED NULL",
                    'rejected_by' => "BIGINT UNSIGNED NULL",
                    'alasan_rejection' => "TEXT NULL",
                    'realized_by' => "BIGINT UNSIGNED NULL",
                    'nominal_diberikan' => "DECIMAL(12,2) NULL"
                ];
                
                foreach ($requiredColumns as $column => $definition) {
                    $columns = DB::select("SHOW COLUMNS FROM pengajuan_danas LIKE '$column'");
                    if (empty($columns)) {
                        $this->command->info("Adding column: $column");
                        DB::statement("ALTER TABLE pengajuan_danas ADD COLUMN $column $definition");
                    }
                }
                
                // Update existing records with valid data
                $this->command->info('Updating existing records with valid data...');
                DB::statement("UPDATE pengajuan_danas SET tanggal = '2025-01-01' WHERE tanggal = '0000-00-00' OR tanggal IS NULL");
                DB::statement("UPDATE pengajuan_danas SET divisi = 'peternakan' WHERE divisi IS NULL");
                DB::statement("UPDATE pengajuan_danas SET minggu = 1 WHERE minggu IS NULL");
                DB::statement("UPDATE pengajuan_danas SET bulan = 1 WHERE bulan IS NULL");
                DB::statement("UPDATE pengajuan_danas SET tahun = 2025 WHERE tahun IS NULL");
                DB::statement("UPDATE pengajuan_danas SET submitted_by = $firstUserId WHERE submitted_by IS NULL");
                
                // Add foreign key constraints if they don't exist
                $foreignKeys = [
                    'submitted_by' => 'users(id)',
                    'approved_by' => 'users(id)',
                    'rejected_by' => 'users(id)',
                    'realized_by' => 'users(id)'
                ];
                
                foreach ($foreignKeys as $column => $reference) {
                    $constraints = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_NAME = 'pengajuan_danas' 
                        AND COLUMN_NAME = '$column' 
                        AND REFERENCED_TABLE_NAME IS NOT NULL
                    ");
                    
                    if (empty($constraints)) {
                        $this->command->info("Adding foreign key constraint for: $column");
                        try {
                            DB::statement("ALTER TABLE pengajuan_danas ADD CONSTRAINT fk_pengajuan_danas_{$column} FOREIGN KEY ($column) REFERENCES $reference ON DELETE " . ($column === 'submitted_by' ? 'CASCADE' : 'SET NULL'));
                        } catch (\Exception $e) {
                            $this->command->warn("Could not add foreign key constraint for $column: " . $e->getMessage());
                        }
                    }
                }
            }
            
            $this->command->info('Pengajuan dana table structure fixed successfully!');
            
        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}
