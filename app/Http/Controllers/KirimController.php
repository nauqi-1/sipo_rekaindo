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
    // Cek apakah ID ini milik Memo, Undangan, atau Risalah
    $memo = Memo::find($id);
    $undangan = Undangan::find($id);
    $risalah = Risalah::find($id);

    // Pastikan minimal satu dokumen ditemukan
    if (!$memo && !$undangan && !$risalah) {
        return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
    }

    // Ambil data divisi dan user
    $divisi = Divisi::all();
    $position = Position::all();
    $user = User::whereIn('role_id_role', ['2', '3'])->get();  


    return view('admin.memo.kirim-memoAdmin', compact('user', 'divisi', 'memo', 'undangan', 'risalah','position'));
}

    public function sendDocument(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id_document' => 'required|exists:memo,id_memo',
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
    
        // Simpan pengiriman memo ke setiap penerima
        foreach ($penerimaUsers as $user) {
            Kirim_Document::create([
                'id_document' => $documentid,
                'jenis_document' => $request->jenis_document,
                'id_pengirim' => Auth::id(),
                'id_penerima' => $user->id,
                'status' => 'sent'
            ]);
        }

        

        return back()->with('success', 'Dokumen berhasil dikirim.');
    }

    public function memoTerkirim()
    {
        $userId = auth()->id(); // Ambil ID user yang sedang login (Manager divisi)
        $divisiId = auth()->user()->divisi_id_divisi; // Ambil divisi manager

        $memoTerkirim = Kirim_Document::where('jenis_document', 'memo')
            ->where('id_penerima', $userId)
            ->whereHas('penerima', function ($query) use ($divisiId) {
                $query->where('divisi_id_divisi', $divisiId); // Mencari memo yang ditujukan ke divisi ini
            })
            ->with('memo') // Relasi ke tabel memo
            ->get();

        return view('manager.memo.memo-terkirim', compact('memoTerkirim'));
    }
    public function memoDiterima()
    {
       $userId = auth()->id(); // Ambil ID user yang sedang login (Manager divisi)
        $divisiId = auth()->user()->divisi_id_divisi; // Ambil divisi manager

        $memoDiterima = Kirim_Document::where('jenis_document', 'memo')
            ->where('id_penerima', $userId)
            ->whereHas('penerima', function ($query) use ($divisiId) {
                $query->where('divisi_id_divisi', $divisiId); // Mencari memo yang ditujukan ke divisi ini
            })
            ->with('memo') // Relasi ke tabel memo
            ->get();

        return view('manager.memo.memo-diterima', compact('memoDiterima'));
    }


    // Daftar dokumen yang dikirim
    public function sentDocuments()
    {
        $documents = Kirim_Document::where('id_pengirim', Auth::id())->get();
        return view('manager.memo.memo-terkirim', compact('documents'));
    }

    // Daftar dokumen yang diterima
    public function receivedDocuments()
    {
        $documents = Kirim_Document::where('id_penerima', Auth::id())->get();
        return view('manager.memo.memo-diterima', compact('documents'));
    }

    // Penerima mengubah status menjadi "dibaca"
    public function markAsRead($id)
    {
        $documentTransfer = Kirim_Document::findOrFail($id);
        
        if ($documentTransfer->receiver_id != Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki izin.');
        }

        $documentTransfer->update(['status' => 'read']);

        return back()->with('success', 'Dokumen ditandai sebagai telah dibaca.');
    }
}
