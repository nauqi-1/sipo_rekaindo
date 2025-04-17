<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kirim_Document extends Model
{
    use HasFactory;
    protected $table = 'kirim_document';
    protected $primaryKey = 'id_kirim_document';

    protected $fillable = [
        'id_document',
        'jenis_document',
        'id_pengirim',
        'id_penerima',
        'status',
        'updated_at',
    ];
    public $timestamps = false;

    // Relasi ke User (Pengirim)
    public function pengirim()
    {
        return $this->belongsTo(User::class, 'id_pengirim');
    }

    public function memo()
    {
        return $this->belongsTo(Memo::class, 'id_document'); // Sesuaikan dengan foreign key yang benar
    }

    public function undangan()
    {
        return $this->belongsTo(Undangan::class, 'id_document'); // Sesuaikan dengan foreign key yang benar
    }

    public function risalah()
    {
        return $this->belongsTo(Risalah::class, 'id_document'); // Sesuaikan dengan foreign key yang benar
    }
    
    // Relasi ke User (Penerima)
    public function penerima()
    {
        return $this->belongsTo(User::class, 'id_penerima');
    }
}
