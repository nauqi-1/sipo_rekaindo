<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Undangan;
use App\Models\Risalah;
use App\Models\Divisi;
use App\Models\Backup_Document;
use App\Models\kategori_barang;
use App\Models\Kirim_Document;
use Illuminate\Support\Facades\Auth;


class BackupController extends Controller
{
    public function memo(Request $request)
    {
        $userId = Auth::id();
        $kode = Memo::withTrashed()
            ->whereNotNull('kode')
            ->pluck('kode')
            ->filter()
            ->unique()
            ->values();


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

        $kode = Undangan::withTrashed()
            ->whereNotNull('kode')
            ->pluck('kode')
            ->filter()
            ->unique()
            ->values();

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
        $kirim_documents = Kirim_Document::withTrashed()->where('id_document', $id)->where('jenis_document', 'memo')->get();
        if ($memo) {
            $memo->restore();
            foreach ($kirim_documents as $kirim_memo) {
                $kirim_memo->restore();
            }
        } else {
            return redirect()->route('memo.backup')->with('failure', 'Memo tidak ditemukan.');
        }
        return redirect()->route('memo.backup')->with('success', 'Memo terpilih berhasil dipulihkan.');
    }
    public function bulkRestoreMemo(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Memo::onlyTrashed()->whereIn('id_memo', $ids)->restore();
        Kirim_Document::onlyTrashed()->whereIn('id_document', $ids)->where('jenis_document', 'memo')->restore();
        return redirect()->back()->with('success', 'Memo terpilih berhasil dipulihkan.');
    }
    public function forceDeleteMemo($id)
    {

        $memo = Memo::onlyTrashed()->findOrFail($id);
        $kirim_document = Kirim_Document::onlyTrashed()->where('id_document', $id)->where('jenis_document', 'memo')->get();
        $barang = kategori_barang::where('memo_id_memo', $id)->get();
        if ($memo) {
            $memo->forceDelete();
            foreach ($kirim_document as $kirim_memo) {
                $kirim_memo->forceDelete();
            }

            foreach ($barang as $b) {
                $b->delete();
            }
        } else {
            return redirect()->route('memo.backup')->with('failure', 'Memo tidak ditemukan.');
        }
        return redirect()->route('memo.backup')->with('success', 'Memo terpilih berhasil dihapus permanen.');
    }
    public function bulkForceDeleteMemo(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Memo::onlyTrashed()->whereIn('id_memo', $ids)->forceDelete();
        Kirim_Document::onlyTrashed()->whereIn('id_document', $ids)->where('jenis_document', 'memo')->forceDelete();
        kategori_barang::whereIn('memo_id_memo', $ids)->delete();
        return redirect()->back()->with('success', 'Memo terpilih berhasil dihapus permanen.');
    }


    public function RestoreUndangan($id)
    {   //dd($id);
        $undangan = Undangan::withTrashed()
            ->where('id_undangan', $id)
            ->first();
        $kirim_documents = Kirim_Document::withTrashed()
            ->where('id_document', $id)
            ->where('jenis_document', 'undangan')
            ->get();
        
        if ($undangan) {
            $undangan->restore();
            foreach ($kirim_documents as $kirim_undangan) {
                $kirim_undangan->restore();
            }
        } else {
            dd($undangan);
        }
        return redirect()->route('undangan.backup')->with('success', 'Pemulihan Undangan Berhasil.');
    }


    public function forceDelete($id)
    {
        $undangan = Undangan::withTrashed()->findOrFail($id);
        $kirim_documents = Kirim_Document::withTrashed()
            ->where('id_document', $id)
            ->where('jenis_document', 'undangan')
            ->get();
        if ($undangan) {
            $undangan->forceDelete();
            foreach ($kirim_documents as $kirim_undangan) {
                $kirim_undangan->forceDelete();
            }
        } else {
            return redirect()->route('undangan.backup')->with('failure', 'Undangan tidak ditemukan.');
        }
        return redirect()->route('undangan.backup')->with('success', 'Undangan berhasil dihapus permanen.');
    }
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Undangan::onlyTrashed()->whereIn('id_undangan', $ids)->restore();
        Kirim_Document::onlyTrashed()->whereIn('id_document', $ids)->where('jenis_document', 'undangan')->restore();
        return redirect()->back()->with('success', 'Pemulihan Undangan Berhasil.');
    }

    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Undangan::onlyTrashed()->whereIn('id_undangan', $ids)->forceDelete();
        Kirim_Document::onlyTrashed()->whereIn('id_document', $ids)->where('jenis_document', 'undangan')->forceDelete();
        return redirect()->back()->with('success', 'Undangan berhasil dihapus permanen.');
    }

    public function bulkRestoreRisalah(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Risalah::onlyTrashed()->whereIn('id_risalah', $ids)->restore();
        Kirim_Document::onlyTrashed()->whereIn('id_document', $ids)->where('jenis_document', 'risalah')->restore();

        return redirect()->back()->with('success_restore', 'Beberapa risalah berhasil dipulihkan.');
    }

    public function bulkForceDeleteRisalah(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        Risalah::onlyTrashed()->whereIn('id_risalah', $ids)->forceDelete();
        Kirim_Document::onlyTrashed()->whereIn('id_document', $ids)->where('jenis_document', 'risalah')->forceDelete();

        return redirect()->back()->with('success_delete', 'Risalah berhasil dihapus permanen.');
    }

    public function forceDeleteRisalah($id)
    {
        Risalah::onlyTrashed()->where('id_risalah', $id)->forceDelete();
        Kirim_Document::onlyTrashed()->where('id_document', $id)->where('jenis_document', 'risalah')->forceDelete();

        return redirect()->back()->with('success_delete', 'Risalah berhasil dihapus permanen.');
    }
}
