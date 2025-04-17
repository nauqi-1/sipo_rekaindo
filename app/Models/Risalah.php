<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Risalah extends Model
{
    use HasFactory;

    protected $table = 'risalah';
    protected $primaryKey = 'id_risalah';
    public $timestamps = true;

    protected $fillable = [
        'tgl_dibuat', 'tgl_disahkan', 'seri_surat', 'divisi_id_divisi',
        'nomor_risalah', 'agenda', 'tempat', 'waktu_mulai', 'status',
        'waktu_selesai', 'tujuan', 'judul', 'pembuat', 'topik', 
        'pembahasan', 'tindak_lanjut', 'target', 'pic', 'nama_bertandatangan',
        'lampiran','catatan'
    ];    

    protected $casts = [
        'tgl_dibuat' => 'datetime',
        'tgl_disahkan' => 'datetime',
    ];

    // Relasi ke tabel Divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id_divisi', 'id_divisi');
    }

    // Relasi ke tabel RisalahDetail
    public function risalahDetails()
    {
        return $this->hasMany(RisalahDetail::class, 'risalah_id_risalah', 'id_risalah');
    }

    public function kirimDocument()
    {
        return $this->hasMany(Kirim_Document::class, 'id_document');
    }
    
    public function arsip()
    {
        return $this->morphMany(Arsip::class, 'document');
    }
}
