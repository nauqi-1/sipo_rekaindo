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
        $risalahs = BackupRisalah::where('jenis_document', 'risalah');
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

        if ($request->filled('divisi_id_divisi') ) {
            $risalahs->where('divisi_id_divisi', $request->divisi_id_divisi);
        }
    
        // Ambil hasil paginate
        $risalahs = $risalahs->paginate(6);

        return view('superadmin.backup.risalah', compact('risalahs','divisi', 'sortDirection'));
    }

    public function RestoreRisalah($id)
    {
       $risalah = BackupRisalah::findOrFail($id);

       // Pindahkan data ke tabel backup
       risalah::create([
           'id_risalah' => $risalah->id_document,
           'tgl_dibuat' => $risalah->tgl_dibuat,
           'tgl_disahkan' => $risalah->tgl_disahkan,
           'seri_surat' => $risalah->seri_document,
           'nomor_risalah' => $risalah->nomor_document,
           'tujuan'=> $risalah->tujuan,
           'waktu_mulai' => $risalah->waktu_mulai,
           'waktu_selesai' => $risalah->waktu_selesai,
           'agenda' => $risalah->agenda,
           'tempat' => $risalah->tempat,
           'nama_bertandatangan'=> $risalah->nama_bertandatangan,
           'lampiran' => $risalah->lampiran,
           'judul' => $risalah->judul,
           'pembuat' => $risalah->pembuat,
           'catatan' => $risalah->catatan,
           'divisi_id_divisi' => $risalah->divisi_id_divisi,
           'status' => $risalah->status,           
           'created_at' => $risalah->created_at,
           'updated_at' => $risalah->updated_at,
           
           // tambahkan kolom lain jika ada
       ]);
   
       // Hapus file lampiran jika ada
       if ($risalah->lampiran && file_exists(public_path($risalah->lampiran))) {
           unlink(public_path($risalah->lampiran));
       }
   
       // Hapus dari tabel memo
       $risalah->delete();

        return redirect()->route('risalah.backup')->with('success', 'Risalah deleted successfully.');
    }
}
