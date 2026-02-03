<?php

namespace App\Helpers;

class MonthHelper
{
    /**
     * Get Indonesian month name
     */
    public static function getIndonesianMonthName($month)
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari', 
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        return $months[$month] ?? 'Bulan ' . $month;
    }
    
    /**
     * Get all Indonesian month names
     */
    public static function getAllIndonesianMonthNames()
    {
        return [
            1 => 'Januari',
            2 => 'Februari', 
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    }

    /**
     * Backward compatible alias.
     * Some parts of the code call getMonthLabel(); map it here.
     */
    public static function getMonthLabel($month)
    {
        return self::getIndonesianMonthName($month);
    }
}
