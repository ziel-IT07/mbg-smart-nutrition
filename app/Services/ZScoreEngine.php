<?php

namespace App\Services;

class ZScoreEngine
{
    /**
     * Hitung nilai Z-Score berdasarkan rumus dasar epidemiologi gizi:
     * Z = (Nilai Riil - Nilai Median Standar) / Nilai Standar Deviasi Referensi
     */
    public static function calculate(float $actualValue, float $median, float $sdLower, float $sdUpper): float
    {
        $diff = $actualValue - $median;
        
        if ($diff >= 0) {
            // Jika nilai anak di atas atau sama dengan median, pembaginya adalah jarak ke +1 SD
            $sdDistance = $sdUpper - $median;
        } else {
            // Jika nilai anak di bawah median, pembaginya adalah jarak ke -1 SD
            $sdDistance = $median - $sdLower;
        }

        if ($sdDistance == 0) {
            return 0.0;
        }
        
        return round($diff / $sdDistance, 2);
    }

    /**
     * Interpretasi status stunting (Indikator TB/U) berdasarkan standar Kemenkes RI
     */
    public static function interpretStunting(float $zScore): string
    {
        if ($zScore < -3.0) {
            return 'Sangat Pendek (Severely Stunted)';
        }
        if ($zScore >= -3.0 && $zScore < -2.0) {
            return 'Pendek (Stunted)';
        }
        if ($zScore >= -2.0 && $zScore <= 3.0) {
            return 'Normal';
        }
        return 'Tinggi';
    }

    /**
     * Interpretasi status gizi (Indikator IMT/U) berdasarkan standar Kemenkes RI
     */
    public static function interpretGizi(float $zScore): string
    {
        if ($zScore < -3.0) {
            return 'Gizi Buruk (Severely Wasted)';
        }
        if ($zScore >= -3.0 && $zScore < -2.0) {
            return 'Gizi Kurang (Wasted)';
        }
        if ($zScore >= -2.0 && $zScore <= 1.0) {
            return 'Gizi Baik (Normal)';
        }
        if ($zScore > 1.0 && $zScore <= 2.0) {
            return 'Berisiko Gizi Lebih';
        }
        if ($zScore > 2.0 && $zScore <= 3.0) {
            return 'Gizi Lebih (Overweight)';
        }
        return 'Obesitas';
    }
}