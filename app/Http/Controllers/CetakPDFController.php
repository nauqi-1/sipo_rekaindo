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
        $path = public_path('img/border-surat.png'); // Ambil path gambar langsung dari folder public
    
        // Konversi gambar ke base64
        $base64Image = null;
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    
        // Load view yang akan digunakan sebagai template PDF
        $pdf = PDF::loadView('format-surat.format-memo', compact('memo', 'base64Image'))
                  ->setPaper('A4', 'portrait')
                  ;
    
        // Return PDF untuk ditampilkan di browser
        return $pdf->stream('laporan-memo.pdf');
    }
    


    public function viewmemoPDF($id)
    {
        // Ambil data dari database
        $memo = Memo::findOrFail($id); // Sesuaikan dengan model yang benar

        // Tampilkan langsung dalam browser
        return view('format-surat.format-memo', compact('memo'));
    }

    public function cetakundanganPDF($id)
    {
        // Ambil data dari database
        $undangan = Undangan::findOrFail($id); // Sesuaikan dengan model yang benar
        $path = public_path('img/border-surat.png'); 

        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $base64Image = null; // Jika gambar tidak ditemukan
        }

        // Load view yang akan digunakan sebagai template PDF
        $pdf = PDF::loadView('format-surat.format-undangan', compact('undangan'));

        // Set ukuran kertas (opsional)
        $pdf->setPaper('A4', 'portrait');
        
       


        // Return PDF untuk didownload
        return $pdf->download('laporan-undangan.pdf');
    }

    public function viewundanganPDF($id)
    {
        // Ambil data dari database
        $undangan = Undangan::findOrFail($id); // Sesuaikan dengan model yang benar
        $tujuanList = explode(';', $undangan->tujuan);

        // Tampilkan langsung dalam browser
        return view('format-surat.format-undangan', compact('undangan','tujuanList'));
    }
}
