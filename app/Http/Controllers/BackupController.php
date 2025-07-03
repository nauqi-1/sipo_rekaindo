<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Undangan;
use App\Models\Risalah;
use App\Models\Divisi;
use App\Models\Backup_Document;
use Auth;


class BackupController extends Controller
{
    public function memo(Request $request)
    {
        $userId = Auth::id();
        $divisi = Divisi::all();
    
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
        if ($request->filled('divisi_id_divisi')) {
            $query->where('divisi_id_divisi', $request->divisi_id_divisi);
        }
    
        // Ambil hasil paginate
        $perPage = $request->get('per_page', 10);
        $memos = $query->paginate($perPage);
    
        return view('superadmin.backup.memo', compact('memos', 'sortDirection', 'divisi'));
    }
    
    

    public function undangan(Request $request)
    {
        $divisi = Divisi::all();
        $undangan = Backup_Document::where('jenis_document', 'undangan');

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


    public function RestoreMmeo($id)
    {

    $memo = Backup_Document::where('id_document', $id)->first();
   
        Memo::create([
            'id_memo' => $memo->id_document,
            'tujuan'=> $memo->tujuan,
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
            'divisi_id_divisi' => $memo->divisi_id_divisi,
        ]);
        // Hapus dari backup
        $memo->delete();
        return redirect()->route('memo.backup')->with('success', 'Memo restored successfully.');
    
    }

     public function RestoreUndangan($id)
     {
        $undangan = Backup_Document::where('id_document', $id)->first();

        // Pindahkan data ke tabel backup
        undangan::create([
            'id_undangan' => $undangan->id_document,
            'tujuan'=> $undangan->tujuan,
            'judul' => $undangan->judul,
            'nomor_undangan' => $undangan->nomor_document,
            'tgl_dibuat' => $undangan->tgl_dibuat,
            'tgl_disahkan' => $undangan->tgl_disahkan,
            'status' => $undangan->status,
            'catatan' => $undangan->catatan,
            'isi_undangan' => $undangan->isi_document,
            'seri_surat' => $undangan->seri_document,
            'nama_bertandatangan'=> $undangan->nama_bertandatangan,
            'lampiran' => $undangan->lampiran,
            'pembuat' => $undangan->pembuat,
            'divisi_id_divisi' => $undangan->divisi_id_divisi,
            'created_at' => $undangan->created_at,
            'updated_at' => $undangan->updated_at,
            // tambahkan kolom lain jika ada
        ]);
    
        // Hapus file lampiran jika ada
        if ($undangan->lampiran && file_exists(public_path($undangan->lampiran))) {
            unlink(public_path($undangan->lampiran));
        }
    
        // Hapus dari tabel memo
        $undangan->delete();
 
         return redirect()->route('undangan.backup' )->with('success', 'Memo deleted successfully.');
     }
    }