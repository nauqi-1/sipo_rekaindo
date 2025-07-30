<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Divisi;
use App\Models\Risalah;
use App\Models\BackupRisalah;

class BackupRisalahController extends Controller
{
    public function risalah(Request $request)
    {
        $divisi = Divisi::all();
        $risalahs = Risalah::onlyTrashed();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $risalahs->where('status', $request->status);
        }
    
        // Filter berdasarkan tanggal dibuat
        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
            $risalahs->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        } elseif ($request->filled('tgl_dibuat_awal')) {
            $risalahs->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
        } elseif ($request->filled('tgl_dibuat_akhir')) {
            $risalahs->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
        }
    
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $risalahs->orderBy('created_at', $sortDirection);
    
        // Pencarian berdasarkan nama dokumen atau nomor memo
        if ($request->filled('search')) {
            $risalahs->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_document', 'like', '%' . $request->search . '%');
            });
        }
    
        // Ambil hasil paginate
        $risalahs = $risalahs->paginate(6);

        return view('superadmin.backup.risalah', compact('risalahs','divisi', 'sortDirection'));
    }

    public function RestoreRisalah($id)
    {
       Risalah::withTrashed()->find($id)->restore();

        return redirect()->route('risalah.backup')->with('success_restore', 'Risalah deleted successfully.');
    }
}
