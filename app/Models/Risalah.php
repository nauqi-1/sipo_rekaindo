<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Risalah extends Model
{
    use HasFactory;

    protected $table = 'risalah';
    protected $primaryKey = 'id_risalah';
    public $timestamps = true;

    protected $fillable = [
        'judul', 'tujuan', 'isi_risalah', 'tgl_dibuat', 'tgl_disahkan', 'status',
        'nomor_risalah', 'nama_bertandatangan', 'tanda_identitas', 'divisi_id_divisi', 'seri_surat'
    ];

    protected $casts = [
        'tgl_dibuat' => 'datetime',
        'tgl_disahkan' => 'datetime',
    ];

    /**
     * Get the division associated with the document.
     */
    public function division()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }
    public function arsip()
    {
        return $this->morphMany(Arsip::class, 'document');
    }
}
