<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Clegginabox\PDFMerger\PDFMerger;
use App\Models\Memo;
use App\Models\Undangan;


class CetakPDFController extends Controller
{
    public function cetakmemoPDF($id)
{
    // Ambil data memo berdasarkan ID
    $memo = Memo::findOrFail($id);
    $headerPath = public_path('img/bheader.png');
    $footerPath = public_path('img/bfooter.png');

    // Konversi gambar ke base64
    $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
    $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

    // Generate PDF halaman pertama (format memo)
    $formatMemoPdf = PDF::loadView('format-surat.format-memo', [
        'memo' => $memo,
        'headerImage' => $headerBase64,
        'footerImage' => $footerBase64,
        'isPdf' => true
    ])->setPaper('A4', 'portrait');

    // Simpan PDF memo sementara
    $formatMemoPath = storage_path('app/temp_format_memo_' . $memo->id . '.pdf');
    $formatMemoPdf->save($formatMemoPath);

    // Cek apakah lampiran tidak kosong
    if (!empty($memo->lampiran)) {
        // Decode base64 lampiran dan simpan sementara
        $lampiranTempPath = storage_path('app/temp_lampiran_' . $memo->id . '.pdf');
        file_put_contents($lampiranTempPath, base64_decode($memo->lampiran));

        // Gabungkan format memo + lampiran
        $pdfMerger = new PDFMerger;
        $pdfMerger->addPDF($formatMemoPath, 'all');
        $pdfMerger->addPDF($lampiranTempPath, 'all');

        $outputPath = storage_path('app/cetak_memo_' . $memo->id . '.pdf');
        $pdfMerger->merge('file', $outputPath);

        // Download lalu hapus semua file sementara
        if (file_exists($formatMemoPath)) unlink($formatMemoPath);
        if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);
        return response()->download($outputPath)->deleteFileAfterSend(true);


    } else {
        // Jika tidak ada lampiran, langsung download PDF memo saja
        return response()->streamDownload(function () use ($formatMemoPdf, $formatMemoPath) {
            echo $formatMemoPdf->output();
            if (file_exists($formatMemoPath)) unlink($formatMemoPath);
        }, 'cetak_memo_' . $memo->id . '.pdf');
    }
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

    public function laporanmemoPDF(Request $request)
    {
        $memos = Memo::query();

        // Filter berdasarkan divisi jika ada
        if ($request->filled('divisi_id_divisi')) {
            $memos->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Filter berdasarkan pencarian judul jika ada
        if ($request->filled('search')) {
            $memos->where('judul', 'like', '%' . $request->search . '%');
        }

        $memos->where('status', 'approve');

        // Ambil semua data yang sudah difilter
        $memos = $memos->orderBy('tgl_dibuat', 'desc')->get();

        // Ambil path gambar header dan footer
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');    

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;
        
        // Generate PDF dari view
        $pdf = PDF::loadView('format-surat.format-cetakLaporan-memo', [
            'memos' => $memos,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Tampilkan PDF langsung di browser
        return $pdf->stream('laporan-undangan.pdf');
    }


    public function laporanundanganPDF(Request $request)
    {
        // Ambil data divisi
        $undangans = Undangan::query();

        // Filter berdasarkan divisi jika ada
        if ($request->filled('divisi_id_divisi')) {
            $undangans->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Filter berdasarkan pencarian judul jika ada
        if ($request->filled('search')) {
            $undangans->where('judul', 'like', '%' . $request->search . '%');
        }

        $undangans->where('status', 'approve');

        // Ambil semua data yang sudah difilter
        $undangans = $undangans->orderBy('tgl_dibuat', 'desc')->get();

        // Ambil path gambar header dan footer
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');    

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;
        
        // Generate PDF dari view
        $pdf = PDF::loadView('format-surat.format-cetakLaporan-undangan', [
            'undangans' => $undangans,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Tampilkan PDF langsung di browser
        return $pdf->stream('laporan-undangan.pdf');
    }

}
