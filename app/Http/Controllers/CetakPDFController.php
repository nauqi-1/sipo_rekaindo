<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Document;


class CetakPDFController extends Controller
{
    public function cetakmemoPDF()
    {
        // Ambil data dari database
        $document = Document::all(); // Nama model diperbaiki

        // Load view yang akan digunakan sebagai template PDF
        $pdf = PDF::loadView('format-surat.format-memo', compact('document'));

        // Set ukuran kertas (opsional)
        $pdf->setPaper('A4', 'portrait');

        // Return PDF untuk didownload
        return $pdf->download('laporan-memo.pdf');
    }
}
