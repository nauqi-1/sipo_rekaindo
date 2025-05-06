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

    public function memoTerkirim()
    {
        $userId = auth()->id(); // Ambil ID user yang sedang login (Manager divisi)
        $divisiId = auth()->user()->divisi_id_divisi; // Ambil divisi manager

        $memoTerkirim = Kirim_Document::where('jenis_document', 'memo')
            ->where('id_penerima', $userId)
            ->where('status', '!=', 'pending')
            ->whereHas('penerima', function ($query) use ($divisiId) {
                $query->where('divisi_id_divisi', $divisiId); // Mencari memo yang ditujukan ke divisi ini
            })
            
            ->whereHas('memo', function ($query) {
                $query->where('status', '!=','pending'); // Cek status dari tabel memo
            })

            ->with('memo'); // Relasi ke tabel memo
            $perPage = request()->get('per_page', 10);
            $memoTerkirim = $memoTerkirim->paginate($perPage);


        return view('manager.memo.memo-terkirim', compact('memoTerkirim'));
    }
    public function memoDiterima()
    {
        $userId = auth()->id(); // Ambil ID user yang sedang login (Manager divisi)
        // Contoh di middleware atau controller sebelumnya
        session(['previous_url' => url()->previous()]);


        $memoDiterima = Kirim_Document::where('jenis_document', 'memo')
            ->where('id_penerima', $userId)
            
            ->Where('status', 'pending') // Status di tabel kirim_document
            ->whereHas('memo')
            ->with('memo'); // Pastikan ada relasi 'memo' di model Kirim_Document
            
        
        $perPage = request()->get('per_page', 10);
        $memoDiterima = $memoDiterima->paginate($perPage);

        return view('manager.memo.memo-diterima', compact('memoDiterima'));
    }



    public function undangan()
    {
        $userId = auth()->id(); // Ambil ID user yang sedang login (Manager divisi)
        $divisiId = auth()->user()->divisi_id_divisi; // Ambil divisi manager

        $undangans = Kirim_Document::where('jenis_document', 'undangan')
            ->where('id_penerima', $userId)
            ->Where('status', 'pending') // Status di tabel kirim_document
            ->whereHas('undangan')
            ->with('undangan'); // Pastikan ada relasi 'memo' di model Kirim_Document
            

            $perPage = request()->get('per_page', 10);
            $undangans = $undangans->paginate($perPage);

        return view('manager.undangan.undangan', compact('undangans'));
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

        return view('manager.risalah.persetujuan-risalah', compact('user', 'divisi', 'risalah', 'position'));
       
    }

    public function risalah()
    {
        $userId = auth()->id(); // Ambil ID user yang sedang login (Manager divisi)
        $divisiId = auth()->user()->divisi_id_divisi; // Ambil divisi manager

        $risalahs = Kirim_Document::where('jenis_document', 'risalah')
        ->where('id_penerima', $userId)
        ->whereHas('risalah', function ($query) {
            $query->where('status', 'pending'); // Cek status dari tabel memo
        })
        ->with('risalah'); // Pastikan ada relasi 'memo' di model Kirim_Document
        

        $perPage = request()->get('per_page', 10);
        $risalahs = $risalahs->paginate($perPage);

        return view('manager.risalah.risalah', compact('risalahs'));
    }

}


