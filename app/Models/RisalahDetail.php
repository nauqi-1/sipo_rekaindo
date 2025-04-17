<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RisalahDetail extends Model
{
    use HasFactory;

    protected $table = 'risalah_details'; // Nama tabel di database

    protected $primaryKey = 'id_risalah_detail'; // Primary Key

    public $timestamps = true; // Mengaktifkan timestamps (created_at & updated_at)

    protected $fillable = [
        'risalah_id_risalah',
        'nomor',
        'topik',
        'pembahasan',
        'tindak_lanjut',
        'target',
        'pic'
    ];

    /**
     * Relasi ke tabel `risalah`
     */
    public function risalah()
    {
        return $this->belongsTo(Risalah::class, 'risalah_id_risalah', 'id_risalah');
    }
}

