<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Memo;
use App\Models\Undangan;


class CetakPDFController extends Controller
{
    public function cetakmemoPDF($id)
    {
        // Ambil data memo berdasarkan ID
        $memo = Memo::findOrFail($id);
        // $path = storage_path('app/public/img/border-surat.png');
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');
    
        // $path = public_path('img/border-surat.png'); // Ambil path gambar langsung dari folder public
    
        // Konversi gambar ke base64
        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;
    
        // Load view yang akan digunakan sebagai template PDF
        $pdf = PDF::loadView('format-surat.format-memo', [
            'memo' => $memo,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');    

        // Return PDF untuk ditampilkan di browser
        return $pdf->stream('laporan-memo.pdf');
    }
    


    // public function viewmemoPDF($id)
    // {
    //     // Ambil data dari database
    //     $memo = Memo::findOrFail($id); // Sesuaikan dengan model yang benar

    //     // Tampilkan langsung dalam browser
    //     return view('format-surat.format-memo', compact('memo'));
    // }

    public function viewmemoPDF($id)
    {
        // $memo = Memo::findOrFail($id);
        
        // // Ambil path gambar background dari storage atau public
        // $path = public_path('img/border-surat.png'); 


        
        $memo = Memo::findOrFail($id);
    
        // Path gambar header & footer
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');
    
        // Konversi gambar ke base64
        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;
    
        return view('format-surat.format-memo', [
            'memo' => $memo,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64
        ]);
    
    }

    public function cetakundanganPDF($id)
    {
        // Ambil data dari database
        $undangan = Undangan::findOrFail($id); // Sesuaikan dengan model yang benar
        // $path = public_path('img/border-surat.png'); 
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');    

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;
    
        // Load view yang akan digunakan sebagai template PDF
        // $pdf = PDF::loadView('format-surat.format-undangan', compact('undangan'));

        // Set ukuran kertas (opsional)
        // $pdf->setPaper('A4', 'portrait');
        $pdf = PDF::loadView('format-surat.format-undangan', [
            'undangan' => $undangan,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');
            
        // Return PDF untuk didownload
        // return $pdf->download('laporan-undangan.pdf');
        return $pdf->stream('laporan-undangan.pdf');

    }

    public function viewundanganPDF($id_undangan)
    {
        // Ambil data dari database
        $undangan = Undangan::findOrFail($id_undangan); // Sesuaikan dengan model yang benar
        // $tujuanList = explode(';', $undangan->tujuan);

        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        // Tampilkan langsung dalam browser
        // return view('format-surat.format-undangan', compact('undangan','tujuanList'));
        return view('format-surat.format-undangan', [
            'undangan' => $undangan,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64
        ]);
    }
}
