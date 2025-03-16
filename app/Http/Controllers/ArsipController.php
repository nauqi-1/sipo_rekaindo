<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arsip;
use App\Models\Memo; 
use App\Models\Undangan; 
use App\Models\Risalah; 
use Illuminate\Support\Facades\Auth;

class ArsipController extends Controller
{
    // Arsipkan Dokumen (Memo, Undangan, atau Risalah)
    public function archiveDocument($document_id, $jenis_document)
    {
        $user_id = Auth::id();

        // Pastikan tipe dokumen valid
        $validTypes = ['Memo', 'Undangan', 'Risalah'];
        if (!in_array($jenis_document, $validTypes)) {
            return redirect()->back()->with('error', 'Tipe dokumen tidak valid.');
        }

        $modelClass = "App\\Models\\" . ucfirst($jenis_document); // Contoh: Memo -> App\Models\Memo

        if (!class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Model tidak ditemukan.');
        }

        // Cek apakah dokumen sudah diarsipkan oleh user ini
        $existing = Arsip::where('user_id', $user_id)
            ->where('document_id', $document_id)
            ->where('jenis_document', $modelClass)
            ->first();

        if ($existing) {
            return redirect()->back()->with('warning', ucfirst($jenis_document) . ' sudah diarsipkan sebelumnya.');
        }

        // Simpan arsip
        Arsip::create([
            'user_id' => $user_id,
            'document_id' => $document_id,
            'jenis_document' => $modelClass,
        ]);

        return redirect()->back()->with('success', ucfirst($jenis_document) . ' berhasil diarsipkan!');
    }

    // Tampilkan daftar arsip berdasarkan user yang login
    

    // Kembalikan Dokumen dari Arsip
    public function restoreDocument($document_id, $jenis_document)
    {
        $user_id = Auth::id();
        $modelClass = "App\\Models\\" . ucfirst($jenis_document);

        // Hapus arsip hanya untuk user yang login
        Arsip::where('user_id', $user_id)
            ->where('document_id', $document_id)
            ->where('jenis_document', $modelClass)
            ->delete();

        return redirect()->back()->with('success', ucfirst($jenis_document) . ' berhasil dikembalikan!');
    }
    public function indexMemo(Request $request)
{  
    $user_id = Auth::id();

    // Ambil daftar arsip memo yang terkait dengan user
    $arsipMemo = Arsip::where('user_id', $user_id)
        ->where('jenis_document', 'App\Models\Memo')
        ->get();

    // Ambil daftar document_id dari arsip untuk query memo
    $memoIds = $arsipMemo->pluck('document_id');

    // Query Memo berdasarkan ID dari arsip
    $query = Memo::whereIn('id_memo', $memoIds);

    // Tambahkan fitur pencarian berdasarkan judul dan nomor memo
    if ($request->filled('search')) {
        $searchTerm = '%' . str_replace(' ', '%', $request->search) . '%';
        $query->where(function ($q) use ($searchTerm) {
            $q->where('judul', 'like', $searchTerm)
              ->orWhere('nomor_memo', 'like', $searchTerm);
        });
    }

    
    $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
    $query->orderBy('tgl_dibuat', $sortDirection);

    // Ambil memo yang sudah difilter
    $memos = $query->get();

    $arsipMemo = $arsipMemo->filter(function ($arsip) use ($memos) {
        return $memos->contains('id_memo', $arsip->document_id);
    });


    // Tambahkan data memo ke dalam daftar arsip
    foreach ($arsipMemo as $arsip) {
        $arsip->document = $memos->where('id_memo', $arsip->document_id)->first();
    }

    return view('arsip.arsip-memo', compact('arsipMemo'));
}

        
        public function indexUndangan(Request $request)
    {
        $user_id = Auth::id();
        $arsipUndangan = Arsip::where('user_id', $user_id)
            ->where('jenis_document', 'App\Models\Undangan')
            ->get();
        
        $undanganIds = $arsipUndangan->pluck('document_id');

        // Query Memo berdasarkan ID dari arsip
        $query = Undangan::whereIn('id_undangan', $undanganIds);

         // Tambahkan fitur pencarian berdasarkan judul dan nomor memo
    if ($request->filled('search')) {
        $searchTerm = '%' . str_replace(' ', '%', $request->search) . '%';
        $query->where(function ($q) use ($searchTerm) {
            $q->where('judul', 'like', $searchTerm)
              ->orWhere('nomor_undangan', 'like', $searchTerm);
        });
    }
        
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('tgl_dibuat', $sortDirection);

        $undangans = $query->get();
        $arsipUndangan = $arsipUndangan->filter(function ($arsip) use ($undangans) {
            return $undangans->contains('id_undangan', $arsip->document_id);
        });

        // Ambil data undangan berdasarkan document_id dari Arsip
        foreach ($arsipUndangan as $arsip) {
            $arsip->document = Undangan::where('id_undangan', $arsip->document_id)->first();
        }

        return view('arsip.arsip-undangan', compact('arsipUndangan','sortDirection','undangans'));
    }

    public function indexRisalah()
    {
        $user_id = Auth::id();
        $arsipRisalah = Arsip::where('user_id', $user_id)
            ->where('jenis_document', 'App\Models\Risalah')
            ->get();

        // Ambil data risalah berdasarkan document_id dari Arsip
        foreach ($arsipRisalah as $arsip) {
            $arsip->document = Risalah::where('id_risalah', $arsip->document_id)->first();
        }

        return view('arsip.arsip-risalah', compact('arsipRisalah'));
    }

    public function view($id)
    {
        $memo = Memo::where('id_memo', $id)->firstOrFail();

        return view('arsip.view-arsipMemo', compact('memo'));
    }

    public function viewUndangan($id)
    {
        $undangan = Undangan::where('id_undangan', $id)->firstOrFail();

        return view('arsip.view-arsipUndangan', compact('undangan'));
    }
}
