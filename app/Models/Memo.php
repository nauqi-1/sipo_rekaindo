<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'memo';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_memo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nm_memo',
        'tgl_buat',
        'tgl_disahkan',
        'lampiran',
        'id_divisi',
        'id_document',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the division associated with the memo.
     */
    public function division()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }

    /**
     * Get the document associated with the memo.
     */
    public function document()
    {
        return $this->belongsTo(Document::class, 'id_document');
    }
}
