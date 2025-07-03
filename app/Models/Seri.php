<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Seri extends Model
{
    use HasFactory;

    protected $table = 'seri_berkas'; 
    protected $primaryKey = 'id_seri';

    protected $fillable = ['divisi_id_divisi', 'bulan', 'tahun', 'seri_bulanan', 'seri_tahunan'];

    public static function getNextSeri($save = false)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $divisiId = auth()->user()->divisi_id_divisi;

        // Cek apakah ada memo untuk divisi ini
        $memoCount = DB::table('memo')
            ->where('divisi_id_divisi', $divisiId)
            ->count();

        if ($memoCount === 0) {
            // Jika tidak ada memo, reset seri bulanan dan tahunan ke 1
            $seriBulanan = 1;
            $seriTahunan = 1;
        } else {
            // Ambil nomor seri terakhir berdasarkan tahun & divisi
            $lastSeri = self::where('divisi_id_divisi', $divisiId)
                ->where('tahun', $currentYear)
                ->latest()
                ->first();

            if (!$lastSeri) {
                // Jika tidak ada data sebelumnya, buat nomor seri pertama
                $seriBulanan = 1;
                $seriTahunan = 1;
            } else {
                // Reset bulanan jika bulan berubah
                if ($lastSeri->bulan != $currentMonth) {
                    $seriBulanan = 1;
                } else {
                    $seriBulanan = $lastSeri->seri_bulanan + 1;
                }

                // Reset tahunan jika tahun berubah
                if ($lastSeri->tahun != $currentYear) {
                    $seriTahunan = 1;
                } else {
                    $seriTahunan = $lastSeri->seri_tahunan + 1;
                }
            }
        }

        if (!$save) {
            return [
                'seri_bulanan' => $seriBulanan,
                'seri_tahunan' => $seriTahunan
            ];
        }

        if ($save) {
            // Simpan ke database hanya jika parameter $save = true
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

        return [
            'seri_bulanan' => $seriBulanan,
            'seri_tahunan' => $seriTahunan
        ];
    }
        public function divisi()
        {
            return $this->belongsTo(Divisi::class, 'divisi_id_divisi', 'id_divisi');
        }
}
