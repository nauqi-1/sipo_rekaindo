<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kategori_barang extends Model
{
    use HasFactory;
    protected $table = 'kategori_barang';
    protected $primaryKey = 'id_kategori_barang';
    protected $fillable = [ 'nomor', 'barang', 'qty', 'satuan', 'document_id_document', 'document_divisi_id_divisi'];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id_document', 'id_document');
    }
}
