<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Undangan extends Model
{
    use HasFactory;

    protected $table = 'undangan';
    protected $primaryKey = 'id_undangan';
    public $timestamps = true;

    protected $fillable = [
        'judul', 'tujuan', 'isi_undangan', 'tgl_dibuat', 'tgl_disahkan', 'status','pembuat','catatan','lampiran',
        'nomor_undangan', 'nama_bertandatangan', 'divisi_id_divisi', 'seri_surat'
    ];

    protected $casts = [
        'tgl_dibuat' => 'datetime',
        'tgl_disahkan' => 'datetime',
    ];

    /**
     * Get the division associated with the document.
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id_divisi','id_divisi');
    }
    public function arsip()
    {
        return $this->morphMany(Arsip::class, 'document');
    }
    public function kirimDocument()
    {
        return $this->hasMany(Kirim_Document::class, 'id_document','id_undangan');

    }
}
