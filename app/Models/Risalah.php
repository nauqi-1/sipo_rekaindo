<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Risalah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'risalah';
    protected $primaryKey = 'id_risalah';
    public $timestamps = true;

    protected $fillable = [
        'tgl_dibuat', 'tgl_disahkan', 'qr_approved_by','seri_surat', 'kode',
        'nomor_risalah', 'agenda', 'tempat', 'waktu_mulai', 'status',
        'waktu_selesai', 'tujuan', 'judul', 'pembuat', 'topik', 
        'pembahasan', 'tindak_lanjut', 'target', 'pic', 'nama_bertandatangan',
        'lampiran','catatan'
    ];    

    protected $casts = [
        'tgl_dibuat' => 'datetime',
        'tgl_disahkan' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

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

    public function user(){
        return $this->belongsTo(User::class, 'pembuat');
    }

    public function up()
    {
        Schema::table('risalah', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('risalah', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

}
