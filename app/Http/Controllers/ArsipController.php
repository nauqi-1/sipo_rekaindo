<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arsip;
use App\Models\Memo; 
use App\Models\Undangan; 
use App\Models\Risalah; 
use App\Models\Kirim_Document;
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

    // Ambil daftar arsip memo dari user
    $arsipQuery = Arsip::where('user_id', $user_id)
        ->where('jenis_document', 'App\Models\Memo');

    // Ambil semua document_id dari arsip terlebih dahulu
    $arsipAll = $arsipQuery->get();
    $memoIds = $arsipAll->pluck('document_id');

    // Siapkan query memo berdasarkan ID dari arsip
    $memoQuery = Memo::whereIn('id_memo', $memoIds);

    // Pencarian berdasarkan judul atau nomor memo
    if ($request->filled('search')) {
        $searchTerm = '%' . str_replace(' ', '%', $request->search) . '%';
        $memoQuery->where(function ($q) use ($searchTerm) {
            $q->where('judul', 'like', $searchTerm)
              ->orWhere('nomor_memo', 'like', $searchTerm);
        });
    }

    // Filter tanggal dibuat (dari - sampai)
    if ($request->filled('start_date')) {
        $memoQuery->whereDate('tgl_dibuat', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $memoQuery->whereDate('tgl_dibuat', '<=', $request->end_date);
    }

    // Filter status jika disediakan
    if ($request->filled('status')) {
        $memoQuery->where('status', $request->status);
    }

    // Sorting
    $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
    $memoQuery->orderBy('tgl_dibuat', $sortDirection);

    // Ambil hasil memo yang sudah difilter
    $filteredMemos = $memoQuery->get();

    // Ambil kembali ID memo yang tersaring
    $filteredMemoIds = $filteredMemos->pluck('id_memo');

    // Filter kembali arsip hanya untuk memo yang lolos filter
    $filteredArsipQuery = Arsip::where('user_id', $user_id)
        ->where('jenis_document', 'App\Models\Memo')
        ->whereIn('document_id', $filteredMemoIds);

    // Pagination arsip
    $perPage = $request->get('per_page', 10);
    $arsipMemo = $filteredArsipQuery->paginate($perPage);

    // Sisipkan data memo ke dalam arsip
    $memosMap = $filteredMemos->keyBy('id_memo');
    foreach ($arsipMemo as $arsip) {
        $arsip->document = $memosMap->get($arsip->document_id);
    }

        $arsipMemo->getCollection()->transform(function ($arsip) use ($user_id) {
        $memo = $arsip->document;

        if ($memo) {
            if ($memo->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $memo->final_status = $memo->status; // Memo dari divisi sendiri
            } else {
                $statusKirim = Kirim_Document::where('id_document', $memo->id_memo)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $user_id)
                    ->first();

                $memo->final_status = $statusKirim ? $statusKirim->status : '-';
            }
        }

        return $arsip;
    });


    return view('arsip.arsip-memo', compact('arsipMemo', 'sortDirection'));
}


        
    public function indexUndangan(Request $request)
    {
        $user_id = Auth::id();

        // Ambil daftar arsip undangan dari user
        $arsipQuery = Arsip::where('user_id', $user_id)
            ->where('jenis_document', 'App\Models\Undangan');

        // Ambil semua document_id dari arsip terlebih dahulu
        $arsipAll = $arsipQuery->get();
        $undanganIds = $arsipAll->pluck('document_id');

        // Siapkan query undangan berdasarkan ID dari arsip
        $undanganQuery = Undangan::whereIn('id_undangan', $undanganIds);

        // Pencarian berdasarkan judul atau nomor undangan
        if ($request->filled('search')) {
            $searchTerm = '%' . str_replace(' ', '%', $request->search) . '%';
            $undanganQuery->where(function ($q) use ($searchTerm) {
                $q->where('judul', 'like', $searchTerm)
                ->orWhere('nomor_undangan', 'like', $searchTerm);
            });
        }

        // Filter tanggal dibuat (dari - sampai)
        if ($request->filled('start_date')) {
            $undanganQuery->whereDate('tgl_dibuat', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $undanganQuery->whereDate('tgl_dibuat', '<=', $request->end_date);
        }

        // Filter status jika disediakan
        if ($request->filled('status')) {
            $undanganQuery->where('status', $request->status);
        }

        // Sorting
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $undanganQuery->orderBy('tgl_dibuat', $sortDirection);

        // Ambil hasil undangan yang sudah difilter
        $filteredUndangan = $undanganQuery->get();

        // Ambil kembali ID undangan yang tersaring
        $filteredUndanganIds = $filteredUndangan->pluck('id_undangan');

        // Filter kembali arsip hanya untuk undangan yang lolos filter
        $filteredArsipQuery = Arsip::where('user_id', $user_id)
            ->where('jenis_document', 'App\Models\Undangan')
            ->whereIn('document_id', $filteredUndanganIds);

        // Pagination arsip
        $perPage = $request->get('per_page', 10);
        $arsipUndangan = $filteredArsipQuery->paginate($perPage);

        // Sisipkan data undangan ke dalam arsip
        $undanganMap = $filteredUndangan->keyBy('id_undangan');
        foreach ($arsipUndangan as $arsip) {
            $arsip->document = $undanganMap->get($arsip->document_id);
        }

        $arsipUndangan->getCollection()->transform(function ($arsip) use ($user_id) {
        $undangan = $arsip->document;

        if ($undangan) {
            if ($undangan->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $undangan->final_status = $undangan->status; // Dari divisi sendiri
            } else {
                $statusKirim = Kirim_Document::where('id_document', $undangan->id_undangan)
                    ->where('jenis_document', 'undangan')
                    ->where('id_penerima', $user_id)
                    ->first();

                $undangan->final_status = $statusKirim ? $statusKirim->status : '-';
            }
        }

        return $arsip;
    });



        return view('arsip.arsip-undangan', compact('arsipUndangan', 'sortDirection'));
    }


    public function indexRisalah(Request $request)
    {
        $user_id = Auth::id();

        // Ambil daftar arsip risalah dari user
        $arsipQuery = Arsip::where('user_id', $user_id)
            ->where('jenis_document', 'App\Models\Risalah');

        // Ambil semua document_id dari arsip terlebih dahulu
        $arsipAll = $arsipQuery->get();
        $risalahIds = $arsipAll->pluck('document_id');

        // Siapkan query risalah berdasarkan ID dari arsip
        $risalahQuery = Risalah::whereIn('id_risalah', $risalahIds);

        // Pencarian berdasarkan judul atau nomor risalah
        if ($request->filled('search')) {
            $searchTerm = '%' . str_replace(' ', '%', $request->search) . '%';
            $risalahQuery->where(function ($q) use ($searchTerm) {
                $q->where('judul', 'like', $searchTerm)
                ->orWhere('nomor_risalah', 'like', $searchTerm);
            });
        }

        // Filter tanggal dibuat (dari - sampai)
        if ($request->filled('start_date')) {
            $risalahQuery->whereDate('tgl_dibuat', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $risalahQuery->whereDate('tgl_dibuat', '<=', $request->end_date);
        }

        // Filter status jika disediakan
        if ($request->filled('status')) {
            $risalahQuery->where('status', $request->status);
        }

        // Sorting
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $risalahQuery->orderBy('tgl_dibuat', $sortDirection);

        // Ambil hasil risalah yang sudah difilter
        $filteredRisalah = $risalahQuery->get();

        // Ambil kembali ID risalah yang tersaring
        $filteredRisalahIds = $filteredRisalah->pluck('id_risalah');

        // Filter kembali arsip hanya untuk risalah yang lolos filter
        $filteredArsipQuery = Arsip::where('user_id', $user_id)
            ->where('jenis_document', 'App\Models\Risalah')
            ->whereIn('document_id', $filteredRisalahIds);

        // Pagination arsip
        $perPage = $request->get('per_page', 10);
        $arsipRisalah = $filteredArsipQuery->paginate($perPage);

        // Sisipkan data risalah ke dalam arsip
        $risalahMap = $filteredRisalah->keyBy('id_risalah');
        foreach ($arsipRisalah as $arsip) {
            $arsip->document = $risalahMap->get($arsip->document_id);
        }

        $arsipRisalah->getCollection()->transform(function ($arsip) use ($user_id) {
        $risalah = $arsip->document;

        if ($risalah) {
            if ($risalah->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $risalah->final_status = $risalah->status; // Dari divisi sendiri
            }else {
                $statusKirim = Kirim_Document::where('id_document', $risalah->id_risalah)
                    ->where('jenis_document', 'risalah')
                    ->where('id_penerima', $user_id)
                    ->first();

                $risalah->final_status = $statusKirim ? $statusKirim->status : '-';
            }
        }

        return $arsip;
    });


        return view('arsip.arsip-risalah', compact('arsipRisalah', 'sortDirection'));
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

    
    public function viewRisalah($id)
    {
        $risalah = Risalah::where('id_risalah', $id)->firstOrFail();

        return view('arsip.view-arsipRisalah', compact('risalah'));
    }
}
