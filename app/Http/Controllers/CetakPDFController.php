<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Clegginabox\PDFMerger\PDFMerger;
use App\Models\Memo;
use App\Models\Undangan;
use App\Models\Risalah;
use App\Models\Divisi;





class CetakPDFController extends Controller
{
    public function cetakmemoPDF($id)
    {
        // Ambil data dari database
        $memo = Memo::findOrFail($id); // Sesuaikan dengan model yang benar
        // $path = public_path('img/border-surat.png'); 
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png'); 
        $qrCode = $memo->qr_approved_by;   

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;
    
        // Load view yang akan digunakan sebagai template PDF
        // $pdf = PDF::loadView('format-surat.format-undangan', compact('undangan'));

        // Set ukuran kertas (opsional)
        // $pdf->setPaper('A4', 'portrait');
        $formatMemoPdf = PDF::loadView('format-surat.format-memo', [
            'memo' => $memo,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'qrCode' => $qrCode,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');
            
        // Return PDF untuk didownload
        $formatMemoPath = storage_path('app/temp_format_memo_' . $memo->id . '.pdf');
        $formatMemoPdf->save($formatMemoPath);
    
        // Jika ada lampiran, gabungkan PDF-nya
        if (!empty($memo->lampiran)) {
            $lampiranTempPath = storage_path('app/temp_lampiran_' . $memo->id . '.pdf');
            file_put_contents($lampiranTempPath, base64_decode($memo->lampiran));

            $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
            $pdfMerger->addPDF($formatMemoPath, 'all');
            $pdfMerger->addPDF($lampiranTempPath, 'all');

            $outputPath = storage_path('app/view_memo_' . $memo->id . '.pdf');
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

    public function viewmemoPDF($id_memo)
{
    // Ambil data memo berdasarkan ID
    $memo = Memo::findOrFail($id_memo);

    $divisiIds = explode(';', $memo->tujuan);
    $divisiNames = Divisi::whereIn('id_divisi', $divisiIds)->pluck('nm_divisi')->toArray();
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
        'divisiNames' => $divisiNames,
        'isPdf' => true
    ])->setPaper('A4', 'portrait');

    // Simpan PDF memo sementara
    $formatMemoPath = storage_path('app/temp_format_memo_' . $memo->id . '.pdf');
    $formatMemoPdf->save($formatMemoPath);

    // Jika ada lampiran, gabungkan PDF-nya
    if (!empty($memo->lampiran)) {
        $lampiranTempPath = storage_path('app/temp_lampiran_' . $memo->id . '.pdf');
        file_put_contents($lampiranTempPath, base64_decode($memo->lampiran));

        $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
        $pdfMerger->addPDF($formatMemoPath, 'all');
        $pdfMerger->addPDF($lampiranTempPath, 'all');

        $outputPath = storage_path('app/view_memo_' . $memo->id . '.pdf');
        $pdfMerger->merge('file', $outputPath);

        // Hapus file sementara setelah digabung
        if (file_exists($formatMemoPath)) unlink($formatMemoPath);
        if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);

        // Tampilkan file hasil merge
        return response()->file($outputPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
    } else {
        // Kalau tidak ada lampiran, tampilkan risalah langsung
        return response()->file($formatMemoPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
    }
}

    public function cetakundanganPDF($id)
    {
        // Ambil data dari database
        $undangan = Undangan::findOrFail($id); // Sesuaikan dengan model yang benar
        // $path = public_path('img/border-surat.png'); 
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png'); 
        $qrCode = $undangan->qr_approved_by;   

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;
    
        // Load view yang akan digunakan sebagai template PDF
        // $pdf = PDF::loadView('format-surat.format-undangan', compact('undangan'));

        // Set ukuran kertas (opsional)
        // $pdf->setPaper('A4', 'portrait');
        $formatUndanganPdf = PDF::loadView('format-surat.format-undangan', [
            'undangan' => $undangan,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'qrCode' => $qrCode,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');
            
        // Return PDF untuk didownload
        $formatUndanganPath = storage_path('app/temp_format_undangan_' . $undangan->id . '.pdf');
        $formatUndanganPdf->save($formatUndanganPath);
    
        // Jika ada lampiran, gabungkan PDF-nya
        if (!empty($undangan->lampiran)) {
            $lampiranTempPath = storage_path('app/temp_lampiran_' . $undangan->id . '.pdf');
            file_put_contents($lampiranTempPath, base64_decode($undangan->lampiran));
    
            $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
            $pdfMerger->addPDF($formatUndanganPath, 'all');
            $pdfMerger->addPDF($lampiranTempPath, 'all');
    
            $outputPath = storage_path('app/view_undangan_' . $undangan->id . '.pdf');
            $pdfMerger->merge('file', $outputPath);
    
            // Download lalu hapus semua file sementara
        if (file_exists($formatUndanganPath)) unlink($formatUndanganPath);
        if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);
        return response()->download($outputPath)->deleteFileAfterSend(true);


    } else {
        // Jika tidak ada lampiran, langsung download PDF memo saja
        return response()->streamDownload(function () use ($formatUndanganPdf, $formatUndanganPath) {
            echo $formatUndanganPdf->output();
            if (file_exists($formatUndanganPath)) unlink($formatUndanganPath);
        }, 'cetak_memo_' . $undangan->id . '.pdf');
    }

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
        $formatUndanganPdf = PDF::loadView('format-surat.format-undangan', [
            'undangan' => $undangan,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Simpan PDF memo sementara
    $formatUndanganPath = storage_path('app/temp_format_undangan_' . $undangan->id . '.pdf');
    $formatUndanganPdf->save($formatUndanganPath);

    // Jika ada lampiran, gabungkan PDF-nya
    if (!empty($undangan->lampiran)) {
        $lampiranTempPath = storage_path('app/temp_lampiran_' . $undangan->id . '.pdf');
        file_put_contents($lampiranTempPath, base64_decode($undangan->lampiran));

        $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
        $pdfMerger->addPDF($formatUndanganPath, 'all');
        $pdfMerger->addPDF($lampiranTempPath, 'all');

        $outputPath = storage_path('app/view_undangan_' . $undangan->id . '.pdf');
        $pdfMerger->merge('file', $outputPath);

        // Hapus file sementara setelah digabung
        if (file_exists($formatUndanganPath)) unlink($formatUndanganPath);
        if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);

        // Tampilkan file hasil merge
        return response()->file($outputPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
    } else {
        // Kalau tidak ada lampiran, tampilkan risalah langsung
        return response()->file($formatUndanganPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
    }
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

    public function cetakrisalahPDF($id)
{
    $risalah = Risalah::findOrFail($id);
    $path = public_path('img/border-surat.png');
    $qrCode = $risalah->qr_approved_by;

    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $base64Image = null;
    }

    // Gunakan PDF::loadView() secara langsung
    $pdf = Pdf::loadView('format-surat.format-risalah', compact('risalah', 'base64Image', 'qrCode'))
              ->setPaper('A4', 'portrait');

    // Simpan PDF memo sementara
    $formatRisalahPath = storage_path('app/temp_format_risalah_' . $risalah->id . '.pdf');
    $pdf->save($formatRisalahPath);

    // Cek apakah lampiran tidak kosong
    if (!empty($risalah->lampiran)) {
     // Decode base64 lampiran dan simpan sementara
     $lampiranTempPath = storage_path('app/temp_lampiran_' . $risalah->id . '.pdf');
     file_put_contents($lampiranTempPath, base64_decode($risalah->lampiran));

     // Gabungkan format memo + lampiran
     $pdfMerger = new PDFMerger;
     $pdfMerger->addPDF($formatRisalahPath, 'all');
     $pdfMerger->addPDF($lampiranTempPath, 'all');

     $outputPath = storage_path('app/cetak_risalah_' . $risalah->id . '.pdf');
     $pdfMerger->merge('file', $outputPath);

     // Download lalu hapus semua file sementara
     if (file_exists($formatRisalahPath)) unlink($formatRisalahPath);
     if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);
     return response()->download($outputPath)->deleteFileAfterSend(true);


 } else {
     // Jika tidak ada lampiran, langsung download PDF memo saja
     return response()->streamDownload(function () use ($pdf, $formatRisalahPath) {
         echo $pdf->output();
         if (file_exists($formatRisalahPath)) unlink($formatRisalahPath);
     }, 'cetak_risalah_' . $risalah->id . '.pdf');
 }
}
 
public function viewrisalahPDF($id_risalah)
{
    $risalah = Risalah::findOrFail($id_risalah);
    $path = public_path('img/border-surat.png');

    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $base64Image = null;
    }

    // Generate PDF risalah
    $pdf = Pdf::loadView('format-surat.format-risalah', compact('risalah', 'base64Image'))
              ->setPaper('A4', 'portrait');

    // Simpan PDF risalah sementara
    $formatRisalahPath = storage_path('app/temp_format_risalah_' . $risalah->id . '.pdf');
    $pdf->save($formatRisalahPath);

    // Jika ada lampiran, gabungkan PDF-nya
    if (!empty($risalah->lampiran)) {
        $lampiranTempPath = storage_path('app/temp_lampiran_' . $risalah->id . '.pdf');
        file_put_contents($lampiranTempPath, base64_decode($risalah->lampiran));

        $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
        $pdfMerger->addPDF($formatRisalahPath, 'all');
        $pdfMerger->addPDF($lampiranTempPath, 'all');

        $outputPath = storage_path('app/view_risalah_' . $risalah->id . '.pdf');
        $pdfMerger->merge('file', $outputPath);

        // Hapus file sementara setelah digabung
        if (file_exists($formatRisalahPath)) unlink($formatRisalahPath);
        if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);

        // Tampilkan file hasil merge
        return response()->file($outputPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
    } else {
        // Kalau tidak ada lampiran, tampilkan risalah langsung
        return response()->file($formatRisalahPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
    }
}

    public function laporanrisalahPDF(Request $request)
    {
        // Ambil data divisi
        $risalahs = Risalah::query();

        // Filter berdasarkan divisi jika ada
        if ($request->filled('divisi_id_divisi')) {
            $risalahs->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Filter berdasarkan pencarian judul jika ada
        if ($request->filled('search')) {
            $risalahs->where('judul', 'like', '%' . $request->search . '%');
        }

        $risalahs->where('status', 'approve');

        // Ambil semua data yang sudah difilter
        $risalahs = $risalahs->orderBy('tgl_dibuat', 'desc')->get();

        // Ambil path gambar header dan footer
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');    

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;
        
        // Generate PDF dari view
        $pdf = PDF::loadView('format-surat.format-cetakLaporan-risalah', [
            'risalahs' => $risalahs,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Tampilkan PDF langsung di browser
        return $pdf->stream('laporan-risalah.pdf');
    }

}
