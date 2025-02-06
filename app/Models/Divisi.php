<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class divisi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'divisi';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_divisi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nm_divisi',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the documents associated with the division.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'divisi_id_divisi');
    }
    public function undangan()
    {
        return $this->hasMany(Undangan::class, 'divisi_id_divisi');
    }
    public function memo()
    {
        return $this->hasMany(Memo::class, 'divisi_id_divisi');
    }
    public function seri()
    {
        return $this->hasMany(Seri::class, 'divisi_id_divisi');
    }
}
