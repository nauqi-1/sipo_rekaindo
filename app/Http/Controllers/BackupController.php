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
        $kode = Memo::whereNotNull('kode')
            ->pluck('kode')
            ->unique();


        $query = Memo::onlyTrashed();
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
        if ($request->filled('kode') && $request->kode != 'pilih') {
            $query->where('kode', $request->kode);
        }

        // Ambil hasil paginate
        $perPage = $request->get('per_page', 10);
        $memos = $query->paginate($perPage);
        return view('superadmin.backup.memo', compact('memos', 'sortDirection', 'kode'));
    }



    public function undangan(Request $request)
    {
        $userId = Auth::id();
        $kode = Undangan::whereNotNull('kode')
            ->pluck('kode')
            ->unique();


        $query = Undangan::onlyTrashed();
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
        if ($request->filled('kode') && $request->kode != 'pilih') {
            $query->where('kode', $request->kode);
        }
        // Ambil hasil paginate
        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $undangans = $query->paginate($perPage);

        return view('superadmin.backup.undangan', compact('undangans', 'sortDirection', 'kode'));
    }


    public function RestoreMemo($id)
    {
        $memo = Memo::withTrashed()
            ->where('id_memo', $id)
            ->first();
        if ($memo) {
            $memo->restore();
        } else {
            dd($memo);
        }
        return redirect()->route('memo.backup')->with('success', 'Memo terpilih berhasil dipulihkan.');
    }

    public function RestoreUndangan($id)
    {   //dd($id);
        $undangan = Undangan::withTrashed()
            ->where('id_undangan', $id)
            ->first();
        if ($undangan) {
            $undangan->restore();
        } else {
            dd($undangan);
        }
        return redirect()->route('undangan.backup')->with('success', 'Pemulihan Undangan Berhasil.');
    }
    public function bulkRestoreMemo(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Memo::onlyTrashed()->whereIn('id_memo', $ids)->restore();

        return redirect()->back()->with('success', 'Memo terpilih berhasil dipulihkan.');
    }

    public function bulkForceDeleteMemo(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Memo::onlyTrashed()->whereIn('id_memo', $ids)->forceDelete();

        return redirect()->back()->with('success', 'Memo terpilih berhasil dihapus permanen.');
    }
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Undangan::onlyTrashed()->whereIn('id_undangan', $ids)->restore();

        return redirect()->back()->with('success', 'Beberapa undangan berhasil dipulihkan.');
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Undangan::onlyTrashed()->whereIn('id_undangan', $ids)->forceDelete();

        return redirect()->back()->with('success', 'Beberapa undangan berhasil dihapus permanen.');
    }

    public function bulkRestoreRisalah(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Risalah::onlyTrashed()->whereIn('id_risalah', $ids)->restore();

        return redirect()->back()->with('success', 'Beberapa risalah berhasil dipulihkan.');
    }

    public function bulkForceDeleteRisalah(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Risalah::onlyTrashed()->whereIn('id_risalah', $ids)->forceDelete();

        return redirect()->back()->with('success', 'Beberapa risalah berhasil dihapus permanen.');
    }

    public function forceDeleteRisalah($id)
    {
        Risalah::onlyTrashed()->where('id_risalah', $id)->forceDelete();

        return redirect()->back()->with('success_delete', 'Risalah berhasil dihapus permanen.');
    }
  
    public function forceDelete($id)
    {
        $undangan = Undangan::withTrashed()->findOrFail($id);
        $undangan->forceDelete();

        return redirect()->route('undangan.backup')->with('success', 'Undangan berhasil dihapus permanen.');
    }
  
    //PENGHAPUSAN PERMANEN
     public function forceDeleteMemo($id)
     {
    
        $memo = Memo::onlyTrashed()->findOrFail($id);
        $memo->forceDelete();

         return redirect()->route('memo.backup')->with('success', 'Memo terpilih berhasil dihapus permanen.');
     }

}
