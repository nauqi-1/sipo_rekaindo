<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Undangan;
use App\Models\Risalah;
use App\Models\Divisi;
use App\Models\Backup_Document;
use Illuminate\Support\Facades\Auth;


class BackupController extends Controller
{
    public function memo(Request $request)
    {
        $userId = Auth::id();
        $kode = Backup_Document::where('jenis_document','memo')
        ->pluck('kode')
        ->unique();
    
        // Gunakan nama variabel $query agar lebih jelas bahwa ini query builder
        $query = Backup_Document::where('jenis_document', 'memo');
    
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        // Filter berdasarkan tanggal dibuat
        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
            $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        } elseif ($request->filled('tgl_dibuat_awal')) {
            $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
        } elseif ($request->filled('tgl_dibuat_akhir')) {
            $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
        }
    
        // Urutan data
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortDirection);
    
        // Pencarian berdasarkan judul atau nomor
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_document', 'like', '%' . $request->search . '%');
            });
        }
    
        // Filter berdasarkan divisi
        if ($request->filled('kode')) {
            $query->where('kode', $request->kode);
        }
    
        // Ambil hasil paginate
        $perPage = $request->get('per_page', 10);
        $memos = $query->paginate($perPage);
    
        return view('superadmin.backup.memo', compact('memos', 'sortDirection', 'kode'));
    }
    
    

    public function undangan(Request $request)
    {
        $divisi = Divisi::all();
        $undangan = Undangan::onlyTrashed();



        // Filter berdasarkan status
        if ($request->filled('status')) {
            $undangan->where('status', $request->status);
        }

        // Filter berdasarkan tanggal dibuat
        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
            $undangan->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        } elseif ($request->filled('tgl_dibuat_awal')) {
            $undangan->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
        } elseif ($request->filled('tgl_dibuat_akhir')) {
            $undangan->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
        }

        // Urutan sorting
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $undangan->orderBy('created_at', $sortDirection);

        // Pencarian berdasarkan judul atau nomor dokumen
        if ($request->filled('search')) {
            $undangan->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                ->orWhere('nomor_document', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan divisi
        if ($request->filled('divisi_id_divisi')) {
            $undangan->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Ambil hasil paginate
        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $undangans = $undangan->paginate($perPage);

        return view('superadmin.backup.undangan', compact('undangans', 'divisi', 'sortDirection'));
    }


    public function RestoreMemo($id)
    {

    $memo = Backup_Document::where('id_document', $id)->first();
   
        Memo::create([
            'id_memo' => $memo->id_document,
            'tujuan'=> $memo->tujuan,
            'tujuan_string'=>$memo->tujuan_string,
            'judul' => $memo->judul,
            'nomor_memo' => $memo->nomor_document,
            'tgl_dibuat' => $memo->tgl_dibuat,
            'tgl_disahkan' => $memo->tgl_disahkan,
            'status' => $memo->status,
            'catatan' => $memo->catatan,
            'isi_memo' => $memo->isi_document,
            'seri_surat' => $memo->seri_document,
            'nama_bertandatangan'=> $memo->nama_bertandatangan,
            'lampiran' => $memo->lampiran,
            'pembuat' => $memo->pembuat,
            'kode' => $memo->kode,
            'qr_approved_by' => $memo->qr_approved_by
        ]);
        // Hapus dari backup
        $memo->delete();
        return redirect()->route('memo.backup')->with('success', 'Pemulihan Memo Berhasil.');
    
    }

     public function RestoreUndangan($id)
     {
         $undangan = Undangan::withTrashed()->where('id_undangan', $id)->first();

    if (!$undangan) {
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }

    $undangan->restore();
 
         return redirect()->route('undangan.backup' )->with('success', 'Memo deleted successfully.');
     }
    }