<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format angka ke format mata uang Indonesia
     * 
     * @param float|int $amount
     * @return string
     */
    public static function formatCurrency($amount)
    {
        if ($amount == 0) {
            return 'Rp 0';
        }
        
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
    
    /**
     * Format angka ke format mata uang Indonesia tanpa prefix Rp
     * 
     * @param float|int $amount
     * @return string
     */
    public static function formatNumber($amount)
    {
        if ($amount == 0) {
            return '0';
        }
        
        return number_format($amount, 0, ',', '.');
    }
}
