<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Seri;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Arsip;
use App\Models\Notifikasi;
use App\Models\Undangan;
use App\Models\Backup_Document;
use App\Models\Kirim_document;

use Illuminate\Http\Request;

class UndanganController extends Controller
{
    public function index(Request $request)
    {
        
        $divisi = Divisi::all();
        $seri = Seri::all(); 
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id(); 

        // Ambil ID undangan yang sudah diarsipkan oleh user saat ini
        $undanganDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();

        // Ambil undangan yang belum diarsipkan oleh user saat ini
        $query = Undangan::with('divisi')
        ->whereNotIn('id_undangan', $undanganDiarsipkan) // Filter undangan yang belum diarsipkan
        ->where(function ($q) use ($userDivisiId, $userId) {
            // undangan yang dibuat oleh divisi user sendiri
            $q->where('divisi_id_divisi', $userDivisiId)
            
            // undangan yang dikirim ke user dari divisi lain melalui tabel kirim_document
            ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {
                $query->where('jenis_document', 'undangan')
                      ->where('id_penerima', $userId)
                      ->whereHas('penerima', function ($subQuery) use ($userDivisiId) {
                          $subQuery->where('divisi_id_divisi', $userDivisiId);
                      });
            });
        });
        
        
        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
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

        // Pencarian berdasarkan nama dokumen atau nomor undangans
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
            });
        }

        
    

        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        // Sorting default menggunakan tgl_dibuat
        $query->orderBy('created_at', $sortDirection);

        
        

        $undangans = $query->paginate(6);

        // **Tambahkan status penerima untuk setiap undangan**
        $undangans->getCollection()->transform(function ($undangan) use ($userId) {
            if ($undangan->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $undangan->final_status = $undangan->status; // Memo dari divisi sendiri
            } else {
                $statusKirim= Kirim_Document::where('id_document', $undangan->id_undangan)
                    ->where('jenis_document', 'undangan')
                    ->where('id_penerima', $userId)
                    ->first();
                $undangan->final_status = $statusKirim ? $statusKirim->status : '-';
                // Cari status kiriman untuk user login
            }
            return $undangan;
        });

    
        return view(Auth::user()->role->nm_role.'.undangan.undangan', compact('undangans','divisi','seri','sortDirection'));
    }

    public function superadmin(Request $request){
        $undangan = null;
        $divisi = Divisi::all();
        $seri = Seri::all(); 
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id(); 
        

        // Ambil ID undangan yang sudah diarsipkan oleh user saat ini
        $undanganDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        // Sorting default menggunakan tgl_dibuat
        
        // Filter berdasarkan status
        $query = Undangan::query()
        ->whereNotIn('id_undangan', $undanganDiarsipkan)
        ->orderBy('created_at', $sortDirection);

        if ($request->has('status') && $request->status != '') {
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

        if ($request->filled('divisi_id_divisi') && $request->divisi_id_divisi != 'pilih' ) {
            $query->where('divisi_id_divisi', $request->divisi_id_divisi);
        }


        // Pencarian berdasarkan nama dokumen atau nomor undangans
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
            });
        }

       

        

        $undangans = $query->paginate(6);

        return view(Auth::user()->role->nm_role.'.undangan.undangan', compact('undangans','divisi','seri','sortDirection'));

    }
    
   
    public function create()
    {
        $divisiId = auth()->user()->divisi_id_divisi;
        $divisiName = auth()->user()->divisi->nm_divisi;

    // Ambil nomor seri berikutnya
    $nextSeri = Seri::getNextSeri(false);

    // Konversi bulan ke angka Romawi
    $bulanRomawi = $this->convertToRoman(now()->month);

    // Format nomor dokumen
    $nomorDokumen = sprintf(
        "%d.%d/REKA/GEN/%s/%s/%d",
        $nextSeri['seri_bulanan'],
        $nextSeri['seri_tahunan'],
        strtoupper($divisiName),
        $bulanRomawi,
        now()->year
    );

    $managers = User::where('divisi_id_divisi', $divisiId)
        ->where('position_id_position', '2')
        ->get(['id', 'firstname', 'lastname']);

    return view(Auth::user()->role->nm_role.'.undangan.add-undangan', [
        'nomorSeriTahunan' => $nextSeri['seri_tahunan'], // Tambahkan nomor seri tahunan
        'nomorDokumen' => $nomorDokumen,
        'managers' => $managers
    ]);  
    }
    public function store(Request $request)
    {
        // dd($request->all());


        $request->validate([
            'judul' => 'required|string|max:70',
            'isi_undangan' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_undangan' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'pembuat' => 'required|string|max:255',
            'catatan'=>'nullable|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max
            ],[
                'lampiran.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
                'lampiran.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
            ]);
        
            $filePath = null;
            if ($request->hasFile('lampiran')) {
                $file = $request->file('lampiran');
                $fileData = base64_encode(file_get_contents($file->getRealPath()));
                $filePath = $fileData;
            }

       

        $divisiId = auth()->user()->divisi_id_divisi;
        $seri = Seri::getNextSeri(true);
        $seri = Seri::where('divisi_id_divisi', $divisiId)
                ->where('tahun', now()->year)
                ->latest()
                ->first();

        if (!$seri) {
            return back()->with('error', 'Nomor seri tidak ditemukan.');
        }
        


        // Simpan dokumen
        $Undangan = Undangan::create([
            'divisi_id_divisi' => $request->input('divisi_id_divisi'),
            'judul' => $request->input('judul'),
            'tujuan' => $request->input('tujuan'),
            'isi_undangan' => $request->input('isi_undangan'),
            'nomor_undangan' => $request->input('nomor_undangan'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'pembuat' => $request->input('pembuat'),
            'catatan' => $request->input('catatan'),
            'seri_surat' => $request->input('seri_surat'),
            'status' => 'pending',
            'nama_bertandatangan' => $request->input('nama_bertandatangan'),
            'lampiran' => $filePath,
        ]);
    
        return redirect()->route('undangan.'. Auth::user()->role->nm_role)->with('success', 'Dokumen berhasil dibuat.');
    }
    private function convertToRoman($number) {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$number];
    }
    public function updateDocumentStatus(Request $request, $id) {
        $undangan = Undangan::findOrFail($id);
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id();

        // Validasi input
        $request->validate([
            'status' => 'required|in:approve,reject,pending',
            'catatan' => 'nullable|string',
        ]);
        
        if ($userDivisiId == $undangan->divisi_id_divisi) {
        // Update status
            $undangan->status = $request->status;
            
            // Jika status 'approve', simpan tanggal pengesahan
            if ($request->status == 'approve') {
                $undangan->tgl_disahkan = now();
            } elseif ($request->status == 'reject') {
                $undangan->tgl_disahkan = now();
            }else{
                $undangan->tgl_disahkan = null;
            }
            

            // Simpan catatan jika ada
            $undangan->catatan = $request->catatan;

            // Simpan perubahan
            $undangan->save();
        } else {
                // Jika user dari divisi lain, update status di tabel kirim_document
                $currentKirim = Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'undangan')
                ->where('id_penerima', $userId)
                ->first();

            if ($currentKirim) {
                $currentKirim->status = $request->status;
                $currentKirim->updated_at = now();
                $currentKirim->save();

                // Update juga status record kiriman sebelumnya (pengirim sebelumnya)
                Kirim_document::where('id_document', $id)
                    ->where('jenis_document', 'undangan')
                    ->where('id_penerima', $currentKirim->id_pengirim)
                    ->where('status', 'pending')
                    ->update([
                        'status' => $request->status,
                        'updated_at' => now()
                    ]);
            }
        }

        Notifikasi::create([
            'judul' => "Memo {$request->status}",
            'judul_document' => $undangan->judul,
            'id_divisi' => $undangan->divisi_id_divisi,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Status undangan berhasil diperbarui.');
    
    }
    
    public function updateDocumentApprovalDate(Undangan $undangan) {
        if ($undangan->status !== 'pending') {
            $undangan->update(['tanggal_disahkan' => now()]);
        }
    }
    public function approve(Undangan $undangan) {
        $undangan->update([
            'status' => 'approve',
            'tanggal_disahkan' => now() // Set tanggal disahkan
        ]);
    
        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }
    public function reject(Undangan $undangan) {
        $undangan->update([
            'status' => 'reject',
            'tanggal_disahkan' => now() // Set tanggal disahkan
        ]);
    
        return redirect()->back()->with('error', 'Dokumen ditolak.');
    }
    public function edit($id)
     {
         $undangan = Undangan::findOrFail($id);
         $divisi = Divisi::all();
         $seri = Seri::all();  
         $divisiId = auth()->user()->divisi_id_divisi;
         $managers = User::where('divisi_id_divisi', $divisiId)
        ->where('position_id_position', '2')
        ->get(['id', 'firstname', 'lastname']);

         

         return view(Auth::user()->role->nm_role.'.undangan.edit-undangan', compact('undangan', 'divisi', 'seri','managers'));

     }
     public function update(Request $request, $id)
     {
        
        $undangan = Undangan::findOrFail($id);
        //  dd($request->all());    

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_undangan' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_undangan' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
            
        ]);
        // dd($request->all()); 

        if ($request->filled('judul')) {
            $undangan->judul = $request->judul;
        }
        if ($request->filled('isi_undangan')) {
            $undangan->isi_undangan = $request->isi_undangan;
        }
        if ($request->filled('tujuan')) {
            $undangan->tujuan = $request->tujuan;
        }
        if ($request->filled('nomor_undangan')) {
            $undangan->nomor_undangan = $request->nomor_undangan;
        }
        if ($request->filled('nama_bertandatangan')) {
            $undangan->nama_bertandatangan = $request->nama_bertandatangan;
        }
        if ($request->filled('tgl_dibuat')) {
            $undangan->tgl_dibuat = $request->tgl_dibuat;
        }
        if ($request->filled('seri_surat')) {
            $undangan->seri_surat = $request->seri_surat;
        }
        if ($request->filled('tgl_disahkan')) {
            $undangan->tgl_disahkan = $request->tgl_disahkan;
        }
        if ($request->filled('divisi_id_divisi')) {
            $undangan->divisi_id_divisi = $request->divisi_id_divisi;
        }
        

        $undangan->save();
 
         return redirect()->route('undangan.'. Auth::user()->role->nm_role)->with('success', 'Undangan updated successfully');
     }
     public function destroy($id)
     {
         $undangan = Undangan::findOrFail($id);

         // Pindahkan data ke tabel backup
        Backup_Document::create([
            'id_document' => $undangan->id_undangan,
            'jenis_document' => 'undangan',
            'tujuan'=> $undangan->tujuan,
            'judul' => $undangan->judul,
            'nomor_document' => $undangan->nomor_undangan,
            'tgl_dibuat' => $undangan->tgl_dibuat,
            'tgl_disahkan' => $undangan->tgl_disahkan,
            'status' => $undangan->status,
            'catatan' => $undangan->catatan,
            'isi_document' => $undangan->isi_undangan,
            'nama_bertandatangan'=> $undangan->nama_bertandatangan,
            'lampiran' => $undangan->lampiran,
            'pembuat' => $undangan->pembuat,
            'seri_document' => $undangan->seri_surat,
            'divisi_id_divisi' => $undangan->divisi_id_divisi,
            'created_at' => $undangan->created_at,
            'updated_at' => $undangan->updated_at,
            // tambahkan kolom lain jika ada
        ]);

         $undangan->delete();
 
         return redirect()->route('undangan.'. Auth::user()->role->nm_role)->with('success', 'Undangan deleted successfully.');
     }
     public function view($id)
    {
        $undangan = Undangan::where('id_undangan', $id)->firstOrFail();

        return view(Auth::user()->role->nm_role.'.undangan.view-undangan', compact('undangan'));
    }
    public function updateStatus(Request $request, $id)
    {
        $undangan = Undangan::findOrFail($id);

        // Validasi input
        $request->validate([
            'status' => 'required|in:approve,reject,pending',
            'catatan' => 'nullable|string',
        ]);

        // Update status
        $undangan->status = $request->status;
        
        // Jika status 'approve', simpan tanggal pengesahan
        if ($request->status == 'approve') {
            $undangan->tgl_disahkan = now();
        } elseif ($request->status == 'reject') {
            $undangan->tgl_disahkan = now();
        }else{
            $undangan->tgl_disahkan = null;
        }

        // Simpan catatan jika ada
        $undangan->catatan = $request->catatan;

        // Simpan perubahan
        $undangan->save();

        return redirect()->back()->with('success', 'Status undangan berhasil diperbarui.');
    }

    public function updateStatusNotif(Request $request, $id)
    {
        $undangan = Undangan::findOrFail($id);
        $undangan->status = $request->status;
        $undangan->save();
    
        // Simpan notifikasi
        Notifikasi::create([
            'judul' => "Undangan {$request->status}",
            'judul_document' => $undangan->judul,
            'id_divisi' => $undangan->divisi_id,
            'updated_at' => now()
        ]);
    
        return redirect()->back()->with('success', 'Status undangan berhasil diperbarui.');
    }
}
