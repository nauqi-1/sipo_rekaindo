<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'document';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'divisi_id_divisi',          // ID divisi pengirim
        'jenis_document',              // Jenis dokumen (memo, undangan, risalah)
        'judul',
        'tujuan',                            // Judul dokumen
        'isi_document',             // Isi dokumen
        'status',             // Status dokumen (pending, approve, reject)
        'tgl_dibuat',     // Tanggal dokumen dibuat
        'tgl_disahkan',   // Tanggal dokumen disahkan secara global
        'seri_bulanan', // Nomor seri bulanan
        'seri_tahunan', // Nomor seri tahunan
        'bulan',              // Bulan dokumen dibuat (angka)
        'tahun',              // Tahun dokumen dibuat
        'nama_pimpinan',
        'nomor_document',
        'tanda_identitas',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_dibuat' => 'datetime',
        'tanggal_disahkan' => 'datetime',
    ];

    /**
     * Get the division associated with the document.
     */
    public function division()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }
    public function updateStatus()
    {
        $recipients = $this->recipients;

        if ($recipients->every(fn($recipient) => $recipient->status === 'approve')) {
            $this->update(['status' => 'approve', 'tanggal_disahkan' => now()]);
        } elseif ($recipients->contains(fn($recipient) => $recipient->status === 'reject')) {
            $this->update(['status' => 'reject', 'tanggal_disahkan' => now()]);
        } else {
            $this->update(['status' => 'pending', 'tanggal_disahkan' => null]);
        }
    }
}
