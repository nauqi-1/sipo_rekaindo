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
    protected $table = 'documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'seri_bulanan',
        'seri_tahunan',
        'kd_instansi',
        'kd_internal',
        'kd_bulan',
        'tahun',
        'id_divisi',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tahun' => 'date',
    ];

    /**
     * Get the division associated with the document.
     */
    public function division()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }
}
