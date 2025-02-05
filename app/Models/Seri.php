<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seri extends Model
{
    use HasFactory;

    protected $table = 'seri_berkas'; 
    protected $primaryKey = 'id_seri';

    protected $fillable = ['divisi_id_divisi', 'bulan', 'tahun', 'seri_bulanan', 'seri_tahunan'];

    public static function getNextSeri($idSeri)
        {
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $divisiId = auth()->user()->divisi_id_divisi;
    
            // Cari nomor seri terakhir berdasarkan tahun & divisi
            $lastSeri = self::where('divisi_id_divisi', $divisiId)
                ->where('tahun', $currentYear)
                ->latest()
                ->first();
    
            if (!$lastSeri) {
                // Jika tidak ada data sebelumnya, buat nomor seri pertama
                $seriBulanan = 1;
                $seriTahunan = 1;
            } else {
                // Periksa apakah bulan berubah untuk reset nomor seri bulanan
                if ($lastSeri->bulan != $currentMonth) {
                    $seriBulanan = 1; // Reset seri bulanan
                } else {
                    $seriBulanan = $lastSeri->seri_bulanan + 1;
                }
    
                // Nomor seri tahunan terus bertambah
                $seriTahunan = $lastSeri->seri_tahunan + 1;
            }
    
            // Simpan nomor seri baru ke database
            $newSeri = self::create([
                'divisi_id_divisi' => $divisiId,
                'bulan' => $currentMonth,
                'tahun' => $currentYear,
                'seri_bulanan' => $seriBulanan,
                'seri_tahunan' => $seriTahunan,
            ]);
    
            return [
                'seri_bulanan' => $newSeri->seri_bulanan,
                'seri_tahunan' => $newSeri->seri_tahunan
            ];
        }

}
