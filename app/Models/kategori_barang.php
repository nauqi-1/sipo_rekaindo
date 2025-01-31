<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kategori_barang extends Model
{
    protected $fillable = ['memo_id', 'nomor', 'barang', 'qty', 'satuan'];

    public function memo()
    {
        return $this->belongsTo(Memo::class);
    }
}
