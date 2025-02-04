<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seri extends Model
{
    use HasFactory;

    protected $table = 'seri_berkas'; 

    protected $fillable = ['divisi_id_divisi', 'bulan', 'tahun', 'seri_bulanan', 'seri_tahunan'];

    public static function getNextSeries($divisiId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $divisiId = auth()->user()->divisi_id_divisi;

        // Cek apakah sudah ada nomor seri untuk divisi ini
        $seri = self::where('divisi_id_divisi', $divisiId)
            ->where('tahun', $currentYear)
            ->latest()
            ->first();

        if (!$seri) {
            // Jika belum ada, buat yang baru dengan nomor 1
            $series = self::create([
                'divisi_id_divisi' => $divisiId,
                'bulan' => $currentMonth,
                'tahun' => $currentYear,
                'seri_bulanan' => 1,
                'seri_tahunan' => 1,
            ]);
        } else {
            // Cek apakah bulan sudah berubah
            if ($seri->bulan != $currentMonth) {
                $seri->seri_bulanan = 1; // Reset nomor bulanan
                $seri->bulan = $currentMonth;
            } else {
                $seri->seri_bulanan += 1;
            }

            // Tambah nomor tahunan
            $seri->seri_tahunan += 1;

            $seri->save();
        }

        return [
            'seri_bulanan' => $seri->seri_bulanan,
            'seri_tahunan' => $seri->seri_tahunan
        ];
    }
}
