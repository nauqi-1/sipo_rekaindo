<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Divisi;
use App\Models\Position;
use App\Models\User;
use App\Models\Undangan;
use App\Models\Kirim_Document;
use App\Models\Risalah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KirimController extends Controller
{
    public function index($id)
    {
        $memo = Memo::find($id);
        $undangan = Undangan::find($id);
        $risalah = Risalah::find($id);

        if (!$memo && !$undangan && !$risalah) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $divisi = Divisi::all();
        $position = Position::all();
        $user = User::whereIn('role_id_role', ['2', '3'])->get();  
        $userId = Auth::id();

        if ($memo) {
            if ($memo->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $memo->final_status = $memo->status;
            } else {
                $statusKirim = Kirim_Document::where('id_document', $memo->id_memo)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $userId)
                    ->first();
                $memo->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return view('admin.memo.kirim-memoAdmin', compact('user', 'divisi', 'memo', 'position'));
        } elseif ($undangan) {
            if ($undangan->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $undangan->final_status = $undangan->status;
            } else {
                $statusKirim = Kirim_Document::where('id_document', $undangan->id_undangan)
                    ->where('jenis_document', 'undangan')
                    ->where('id_penerima', $userId)
                    ->first();
                $undangan->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return view('admin.undangan.kirim-undanganAdmin', compact('user', 'divisi', 'undangan', 'position'));
        } elseif ($risalah) {
            if ($risalah->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $risalah->final_status = $risalah->status;
            } else {
                $statusKirim = Kirim_Document::where('id_document', $risalah->id_risalah)
                    ->where('jenis_document', 'risalah')
                    ->where('id_penerima', $userId)
                    ->first();
                $risalah->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return view('admin.risalah.kirim-risalahAdmin', compact('user', 'divisi', 'risalah', 'position'));
        }

        // Bisa tambahkan elseif risalah di sini jika ada
    }

    public function viewManager($id)
    {
        // Cek apakah ID ini milik Memo, Undangan, atau Risalah
        $undangan = Undangan::find($id);

        // Pastikan minimal satu dokumen ditemukan
        if (!$undangan) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Ambil data divisi dan user
        $divisi = Divisi::all();
        $position = Position::all();
        $user = User::whereIn('role_id_role', ['2', '3'])->get();  

        return view('manager.undangan.persetujuan-undangan', compact('user', 'divisi', 'undangan', 'position'));
    }


    public function sendDocument(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'id_document' => 'required',
            'posisi_penerima' => 'required|exists:position,id_position', // Validasi posisi
            'divisi_penerima' => 'required|exists:divisi,id_divisi', // Pastikan divisi ada
        ]);
        

        $documentid = $request->id_document;
        $posisiPenerima = $request->posisi_penerima;
        $divisiPenerima = $request->divisi_penerima;
    
        // Cari semua user dengan posisi dan divisi yang dipilih
        $penerimaUsers = User::where('position_id_position', $posisiPenerima)
                              ->where('divisi_id_divisi', $divisiPenerima)
                              ->get();
    
        if ($penerimaUsers->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada user yang sesuai dengan kriteria penerima.');
        }

        $filePath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $fileData = base64_encode(file_get_contents($file->getRealPath()));
            // Simpan file base64 ke tabel sesuai jenis dokumen
            if ($request->jenis_document == 'memo') {
                $memo = Memo::findOrFail($documentid);
                $memo->lampiran = $fileData;
                $memo->save();
            } elseif ($request->jenis_document == 'undangan') {
                $undangan = Undangan::findOrFail($documentid);
                $undangan->lampiran = $fileData;
                $undangan->save();
            } elseif ($request->jenis_document == 'risalah') {
                $risalah = Risalah::findOrFail($documentid);
                $risalah->lampiran = $fileData;
                $risalah->save();
            }
        }
        

      
    
        // Simpan pengiriman memo ke setiap penerima
        foreach ($penerimaUsers as $user) {
            Kirim_Document::create([
                'id_document' => $documentid,
                'jenis_document' => $request->jenis_document,
                'id_pengirim' => Auth::id(),
                'id_penerima' => $user->id,
                'status' => 'pending',
            ]);
        }
        $previousUrl = session('previous_url', route('memo.diterima'));
        session()->forget('previous_url');
        if(Auth::user()->role->nm_role == 'manager'){
            return redirect($previousUrl)->with('success', 'Dokumen berhasil dikirim.');
        }else{
        
        return redirect()->back()->with('success', 'Dokumen berhasil dikirim.');
        }
    }

    public function memoTerkirim(Request $request)
    {
        $userId = auth()->id();
        $divisiId = auth()->user()->divisi_id_divisi;
        $sortBy = $request->get('sort_by', 'kirim_document.id_kirim_document');
        $sortDirection = $request->get('sort_direction', 'desc');



        $allowedSorts = [
            'kirim_document.id_kirim_document',
            'memo.tgl_dibuat',
            'memo.tgl_disahkan',
            'memo.judul',
            'memo.nomor_memo'
        ];

    if (!in_array($sortBy, $allowedSorts)) {
        $sortBy = 'kirim_document.id_kirim_document';
    }

        $memoTerkirim = Kirim_Document::where('jenis_document', 'memo')
            ->where(function ($query) use ($userId) {
                $query->where('id_pengirim', $userId)
                      ->orWhere('id_penerima', $userId);
            })
            ->where('kirim_document.status', '!=', 'pending')
            ->whereHas('penerima', function ($query) use ($divisiId) {
                $query->where('divisi_id_divisi', $divisiId);
                      
            })
            ->whereHas('memo', function ($query) use ($request, $divisiId) {
                $query->where('memo.status', '!=', 'pending')
                    ->where('memo.divisi_id_divisi', $divisiId);

                if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
                    $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
                } elseif ($request->filled('tgl_dibuat_awal')) {
                    $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
                } elseif ($request->filled('tgl_dibuat_akhir')) {
                    $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
                }

                if ($request->filled('search')) {
                    $query->where(function ($q) use ($request) {
                        $q->where('judul', 'like', '%' . $request->search . '%')
                            ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
                    });
                }
                if ($request->filled('status')) {
                    $query->where('memo.status', $request->status);
                }
            });
        

        if (Str::startsWith($sortBy, 'memo.')) {
            $memoColumn = Str::after($sortBy, 'memo.');
            $memoTerkirim->join('memo', 'kirim_document.id_document', '=', 'memo.id_memo')
                ->orderBy("memo.$memoColumn", $sortDirection)
                ->select('kirim_document.*'); // agar tetap menghasilkan Kirim_Document model
        } else {
            $memoTerkirim->orderBy($sortBy, $sortDirection);
        }

        $perPage = $request->get('per_page', 10);
        $memoTerkirim = $memoTerkirim->paginate($perPage);
        
        return view('manager.memo.memo-terkirim', compact('memoTerkirim', 'sortBy', 'sortDirection'));
    }

    public function memoDiterima(Request $request)
    {
        $userId = auth()->id();
        $divisiId = auth()->user()->divisi_id_divisi;
        session(['previous_url' => url()->previous()]);
        $sortBy = $request->get('sort_by', 'kirim_document.id_kirim_document');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSorts = [
            'kirim_document.id_kirim_document',
            'memo.tgl_dibuat',
            'memo.tgl_disahkan',
            'memo.judul',
            'memo.nomor_memo'
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'kirim_document.id_kirim_document';
        }

         $memoDiterima = Kirim_Document::where('jenis_document', 'memo')
        ->where('id_penerima', $userId)
        ->whereIn('kirim_document.status',  ['pending','approve'])
        ->whereHas('memo', function ($query) use ($request, $divisiId) {
            if ($request->filled('divisi_filter')) {
        if ($request->divisi_filter === 'own') {
            $query->where('divisi_id_divisi', $divisiId);
        } elseif ($request->divisi_filter === 'other') {
            $query->where('divisi_id_divisi', '!=', $divisiId)
                  ->where('status', 'approve'); // only approved from other divisions
        }
    } else {
        // Default: show same division OR approved from other division
        $query->where(function ($q) use ($divisiId) {
            $q->where('divisi_id_divisi', $divisiId)
              ->where('status', 'pending')
              ;
        })->orWhere(function ($q) use ($divisiId) {
            $q->where('divisi_id_divisi', '!=', $divisiId)
              ->where('status', 'approve');
        });
    }

            // Additional filters
            if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
                $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
            } elseif ($request->filled('tgl_dibuat_awal')) {
                $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
            } elseif ($request->filled('tgl_dibuat_akhir')) {
                $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
            }

            if ($request->filled('search')) {
                $query->where(function ($q2) use ($request) {
                    $q2->where('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
                });
            }

            
        });

        
            if (Str::startsWith($sortBy, 'memo.')) {
            $memoColumn = Str::after($sortBy, 'memo.');
            $memoDiterima->join('memo', 'kirim_document.id_document', '=', 'memo.id_memo')
                ->orderBy("memo.$memoColumn", $sortDirection)
                ->select('kirim_document.*'); // agar tetap menghasilkan Kirim_Document model
            } else {
                $memoDiterima->orderBy($sortBy, $sortDirection);
            }

        $perPage = $request->get('per_page', 10);
        $memoDiterima = $memoDiterima->paginate($perPage);
        return view('manager.memo.memo-diterima', compact('memoDiterima', 'sortBy', 'sortDirection'));
    }

    public function undangan(Request $request)
    {
        $userId = auth()->id();

        $sortBy = $request->get('sort_by', 'kirim_document.id_kirim_document');
        $sortDirection = $request->get('sort_direction', 'asc');


        $allowedSorts = [
            'kirim_document.id_kirim_document',
            'undangan.tgl_dibuat',
            'undangan.tgl_disahkan',
            'undangan.judul',
            'undangan.nomor_undangan'
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'kirim_document.id_kirim_document';
        }

        // Step 1: Ambil id_kirim_document terkecil per id_document (distinct id_document)
        $subQuery = Kirim_Document::where('jenis_document', 'undangan')
            ->where(function($q) use ($userId) {
                $q->where('id_penerima', $userId)
                  ->orWhere('id_pengirim', $userId);
            });

        if ($request->filled('status')) {
            $subQuery->where('kirim_document.status', $request->status);
        }

        $subQuery->whereHas('undangan', function ($query) use ($request, $sortBy, $sortDirection) {
            if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
                $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
            } elseif ($request->filled('tgl_dibuat_awal')) {
                $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
            } elseif ($request->filled('tgl_dibuat_akhir')) {
                $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
            }

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
                });
            }
        });

        $idKirimList = $subQuery->selectRaw('MIN(id_kirim_document) as id_kirim_document')->groupBy('id_document')->pluck('id_kirim_document');

        // Step 2: Ambil data lengkap Kirim_Document berdasarkan id_kirim_document hasil subquery
        $undangans = Kirim_Document::whereIn('id_kirim_document', $idKirimList)
            ->with('undangan');

        // Sorting
        if (Str::startsWith($sortBy, 'memo.')) {
            $memoColumn = Str::after($sortBy, 'memo.');
            $undangans->join('memo', 'kirim_document.id_document', '=', 'memo.id_memo')
                ->orderBy("memo.$memoColumn", $sortDirection)
                ->select('kirim_document.*');
        } else {
            $undangans->orderBy($sortBy, $sortDirection);
        }

        $perPage = $request->get('per_page', 10);
        $undangans = $undangans->paginate($perPage);

        return view('manager.undangan.undangan', compact('undangans', 'sortBy', 'sortDirection'));
    }

    public function risalah(Request $request)
    {
        $userId = auth()->id();
        $sortBy = $request->get('sort_by', 'kirim_document.id_kirim_document');
        $sortDirection = $request->get('sort_direction', 'asc');


        $allowedSorts = [
            'kirim_document.id_kirim_document',
            'risalah.tgl_dibuat',
            'risalah.tgl_disahkan',
            'risalah.judul',
            'risalah.nomor_risalah'
        ];

    if (!in_array($sortBy, $allowedSorts)) {
        $sortBy = 'kirim_document.id_kirim_document';
    }

        $risalahs = Kirim_Document::where('jenis_document', 'risalah')
            ->where('id_penerima', $userId)
            ->whereHas('risalah', function ($query) use ($request, $sortBy, $sortDirection) { 
                if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
                    $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
                } elseif ($request->filled('tgl_dibuat_awal')) {
                    $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
                } elseif ($request->filled('tgl_dibuat_akhir')) {
                    $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
                }

                if ($request->filled('search')) {
                    $query->where(function ($q) use ($request) {
                        $q->where('judul', 'like', '%' . $request->search . '%')
                            ->orWhere('nomor_risalah', 'like', '%' . $request->search . '%');
                    });
                }
            });

            if (Str::startsWith($sortBy, 'risalah.')) {
            $risalahColumn = Str::after($sortBy, 'risalah.');
            $risalahs->join('risalah', 'kirim_document.id_document', '=', 'risalah.id_risalah')
                ->orderBy("risalah.$risalahColumn", $sortDirection)
                ->select('kirim_document.*'); // agar tetap menghasilkan Kirim_Document model
            } else {
                $risalahs->orderBy($sortBy, $sortDirection);
            }

        $perPage = $request->get('per_page', 10);
        $risalahs = $risalahs->paginate($perPage);

        return view('manager.risalah.risalah', compact('risalahs', 'sortBy', 'sortDirection'));
    }





    // Daftar dokumen yang dikirim
    public function sentDocuments()
    {
        $documents = Kirim_Document::where('id_pengirim', Auth::id())->get();
        return view('manager.memo.memo-terkirim', compact('documents'));
    }

    // Daftar dokumen yang diterima


    public function viewRisalah($id)
    {
        // Cek apakah ID ini milik Memo, Undangan, atau Risalah
        $risalah = Risalah::find($id);

        // Pastikan minimal satu dokumen ditemukan
        if (!$risalah) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Ambil data divisi dan user
        $divisi = Divisi::all();
        $position = Position::all();
        $user = User::whereIn('role_id_role', ['2', '3'])->get();  

        $undangan = Undangan::where('judul', $risalah->judul)->first();

        return view('manager.risalah.persetujuan-risalah', compact('user', 'divisi', 'risalah', 'position', 'undangan'));
       
    }

    

}


