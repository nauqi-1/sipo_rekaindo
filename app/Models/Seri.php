<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Seri extends Model
{
    use HasFactory;

    protected $table = 'seri_berkas';
    protected $primaryKey = 'id_seri';

    protected $fillable = ['kode', 'bulan', 'tahun', 'seri_bulanan', 'seri_tahunan'];

    public static function getNextSeri($save = false)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $user = Auth::user();
        $kode = null;

        // Coba ambil kode dari department
        if ($user->department_id_department) {
            $kode = \App\Models\Department::where('id_department', $user->department_id_department)->value('kode_department');
        }

        // Kalau kode masih null atau kosong, fallback ke divisi
        if (!$kode && $user->divisi_id_divisi) {
            $kode = \App\Models\Divisi::where('id_divisi', $user->divisi_id_divisi)->value('kode_divisi');
        }

        // Terakhir, baru error kalau memang tidak ada kode valid
        if (!$kode) {
            throw new \Exception('Kode tidak ditemukan. Pastikan user memiliki kode department atau divisi yang valid.');
        }

        // Cek apakah ada memo atau undangan untuk kode ini
        $memoCount = DB::table('memo')
            ->where('kode', $kode)
            ->count();

        $undanganCount = DB::table('undangan')
            ->where('kode', $kode)
            ->count();

        if ($memoCount === 0 && $undanganCount === 0) {
            $seriBulanan = 1;
            $seriTahunan = 1;
        } else {
            $lastSeri = self::where('kode', $kode)
                ->where('tahun', $currentYear)
                ->latest()
                ->first();

            if (!$lastSeri) {
                $seriBulanan = 1;
                $seriTahunan = 1;
            } else {
                $seriBulanan = ($lastSeri->bulan != $currentMonth) ? 1 : $lastSeri->seri_bulanan + 1;
                $seriTahunan = $lastSeri->seri_tahunan + 1;
            }
        }

        if (!$save) {
            return [
                'seri_bulanan' => $seriBulanan,
                'seri_tahunan' => $seriTahunan
            ];
        }

        $newSeri = self::create([
            'kode' => $kode,
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
