<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixPenggajianStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Perbaiki enum status dengan SQL langsung
        try {
            DB::statement("ALTER TABLE penggajians MODIFY COLUMN status ENUM('pending', 'approved', 'paid') DEFAULT 'pending'");
            $this->command->info('Status enum berhasil diperbaiki!');
        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}
