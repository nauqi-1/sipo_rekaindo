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
use App\Models\Kirim_Document;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


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
        $sortBy = $request->get('sort_by', 'created_at'); // default ke created_at
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_undangan', 'judul'];
         if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // fallback default
        }

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


        // Sorting default menggunakan tgl_dibuat
        $query->orderBy($sortBy, $sortDirection);

        

        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $undangans = $query->paginate($perPage);

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
                $kirimDocuments = Kirim_Document::where('jenis_document', 'undangan')
        ->whereHas('undangan') // Memastikan dokumen adalah memo
        ->orderBy('id_kirim_document', 'desc')
        ->get();

    // Ambil divisi penerima dan pengirim melalui relasi user
    $kirimDocuments->each(function ($kirim) {
        $pengirim = User::find($kirim->id_pengirim);
        $penerima = User::find($kirim->id_penerima);
        $user = Auth::user();

        $kirim->divisi_pengirim = $pengirim ? $pengirim->divisi->nm_divisi : 'Tidak Diketahui';
        $kirim->divisi_penerima = $penerima ? $penerima->divisi->nm_divisi : 'Tidak Diketahui';
        $kirim->divisi_user = $user->divisi->nm_divisi ?? 'Tidak Diketahui';
    });
    
        return view(Auth::user()->role->nm_role.'.undangan.undangan', compact('undangans','divisi','seri','sortDirection','kirimDocuments'));
    }
    public function superadmin(Request $request){
        $undangan = null;
        $divisi = Divisi::all();
        $seri = Seri::all(); 
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id(); 
        

        // Ambil ID undangan yang sudah diarsipkan oleh user saat ini
        $undanganDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();
        $sortBy = $request->get('sort_by', 'created_at'); // default ke created_at
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_undangan', 'judul'];
         if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // fallback default
        }

        // Sorting default menggunakan tgl_dibuat
        $query = Undangan::query()
            ->whereNotIn('id_undangan', $undanganDiarsipkan)
            ->orderBy($sortBy, $sortDirection);

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

       

        

        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $undangans = $query->paginate($perPage);

        return view(Auth::user()->role->nm_role.'.undangan.undangan', compact('undangans','divisi','seri','sortDirection'));

    }
    
   
    public function create()
    {

        $divisiId = auth()->user()->divisi_id_divisi;
        $divisiName = auth()->user()->divisi->nm_divisi;
        $divisi = Divisi::all();

    // Ambil nomor seri berikutnya
    $nextSeri = Seri::getNextSeri(false);

    // Konversi bulan ke angka Romawi
    $bulanRomawi = $this->convertToRoman(now()->month);

    // Format nomor dokumen
    $nomorDokumen = sprintf(
        "%d.%d/REKA/GEN/%s/%s/%d",
        $nextSeri['seri_tahunan'],
        $nextSeri['seri_bulanan'],
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
        'managers' => $managers,
        'divisi' => $divisi
    ]);  
    }
    public function store(Request $request)
{
        // dd($request->all());
    // Ubah validasi tujuan jadi array
    $validator = Validator::make($request->all(), [
        'judul' => 'required|string|max:70',
        'isi_undangan' => 'required|string',
        'tujuan' => 'required|array|min:1',
        'tujuan.*' => 'exists:divisi,id_divisi',
        'nomor_undangan' => 'required|string|max:255',
        'nama_bertandatangan' => 'required|exists:users,id', // harus user id
        'pembuat' => 'required|string|max:255',
        'catatan' => 'nullable|string|max:255',
        'tgl_dibuat' => 'required|date',
        'seri_surat' => 'required|numeric',
        'tgl_disahkan' => 'nullable|date',
        'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
        'tgl_rapat' => 'required|date',
        'tempat' => 'required|string',
        'waktu_mulai' => 'required|string',
        'waktu_selesai' => 'required|string',
        'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ], [
        'tgl_dibuat.required' => 'Tanggal dibuat wajib diisi.',
        'tujuan.required' => 'Minimal satu divisi tujuan harus dipilih.',
        'lampiran.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
        'lampiran.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Proses file lampiran (jika ada)
    $filePath = null;

    if ($request->hasFile('lampiran')) {
        $file = $request->file('lampiran');
        $fileData = base64_encode(file_get_contents($file->getRealPath()));
        $filePath = $fileData;
    }

        //PROSES AMBIL ID DAN NAMA DIVISI TUJUAN UNDANGAN (KEPADA)
        // Ambil array ID divisi tujuan dari form (checkbox tujuan[])

        $tujuanArray = $request->input('tujuan'); // contoh: [2,3]
        // Simpan sebagai string "2;3" jika ingin
        $tujuanString = implode(';', $tujuanArray);
        // Ambil nama divisi tujuan (IT, SDM, dst) dan simpan sebagai string
        $namaDivisiArray = \App\Models\Divisi::whereIn('id_divisi', $tujuanArray)->pluck('nm_divisi')->toArray();
        $namaDivisiString = implode('; ', $namaDivisiArray);
        
        // PROSES UNTUK Simpan undangan KE DATABASE
        // Ambil user manager yang dipilih
        $manager = User::findOrFail($request->input('nama_bertandatangan'));

        // IF ELSE
        if($manager->id == Auth::id()) {
            // Jika pengirim adalah manager yang sama, set status approve
            

        } else {
            $status = 'pending'; // Status pending jika bukan pengirim
        }
        $undangan = Undangan::create([
            'divisi_id_divisi' => Auth::user()->divisi_id_divisi,
            'judul' => $request->input('judul'),
            'tujuan' => $namaDivisiString,
            'isi_undangan' => $request->input('isi_undangan'),
            'nomor_undangan' => $request->input('nomor_undangan'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'pembuat' => $request->input('pembuat'),
            'catatan' => $request->input('catatan'),
            'seri_surat' => $request->input('seri_surat'),
            'status' => 'pending',
            'tgl_rapat' => $request->input('tgl_rapat'),
            'tempat' => $request->input('tempat'),
            'waktu_mulai' => $request->input('waktu_mulai'),
            'waktu_selesai' => $request->input('waktu_selesai'),
            // Simpan nama lengkap manager, bukan id
            'nama_bertandatangan' => $manager->firstname . ' ' . $manager->lastname,
            'lampiran' => $filePath,
        ]);

        // Kirim approval ke manager yang dipilih saja
        $sudahDikirim = \App\Models\Kirim_Document::where('id_document', $undangan->id_undangan)
            ->where('jenis_document', 'undangan')
            ->where('id_pengirim', Auth::id())
            ->where('id_penerima', $manager->id)
            ->exists();

        if (!$sudahDikirim) {
            //ID_PENGIRIM 
            if(@Auth::user()->id == $manager->id) {
                // Jika pengirim adalah manager yang sama, set status approve
                // Kirim dokumen ke semua user di divisi tujuan (kecuali pengirim sendiri)
                    $namaDivisiArray = array_map('trim', explode(';', $undangan->tujuan));
                    $divisiIds = \App\Models\Divisi::whereIn('nm_divisi', $namaDivisiArray)->pluck('id_divisi');
                    $userTujuan = \App\Models\User::whereIn('divisi_id_divisi', $divisiIds)
                        ->where('id', '!=', Auth::id())
                        ->get();
                    foreach ($userTujuan as $user) {
                        $sudahDikirim = Kirim_Document::where('id_document', $undangan->id_undangan)
                            ->where('jenis_document', 'undangan')
                            ->where('id_pengirim', Auth::id())
                            ->where('id_penerima', $user->id)
                            ->exists();
                        if (!$sudahDikirim) {
                            //ID_PENGIRIM
                            Kirim_Document::create([
                                'id_document' => $undangan->id_undangan,
                                'jenis_document' => 'undangan',
                                'id_pengirim' => Auth::id(),
                                'id_penerima' => $user->id,
                                'status' => 'approve',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
            } elseif ($manager->id != Auth::id()) {
                    \App\Models\Kirim_Document::create([
                    'id_document' => $undangan->id_undangan,
                    'jenis_document' => 'undangan',
                    'id_pengirim' => Auth::id(),
                    'id_penerima' => $manager->id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }else{
                //
            }
        }
            

    return redirect()->route('undangan.' . Auth::user()->role->nm_role)
        ->with('success', 'Dokumen berhasil dibuat.');
}

    private function convertToRoman($number) {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$number];
    }
public function updateDocumentStatus(Request $request, $id)
{
    $undangan = Undangan::findOrFail($id);
    $userDivisiId = Auth::user()->divisi_id_divisi;
    $userId = Auth::id();

    // Validasi input dinamis: catatan wajib jika reject/correction
    $rules = [
        'status' => 'required|in:approve,reject,correction',
        'catatan' => 'nullable|string',
    ];
    $messages = [];
    if (in_array($request->status, ['reject', 'correction'])) {
        $rules['catatan'] = 'required|string';
        $messages['catatan.required'] = 'Catatan wajib diisi jika undangan ditolak atau dikoreksi.';
    }
    $request->validate($rules, $messages);

    // Update status di tabel undangan
    $undangan->status = $request->status;
    if ($request->status == 'approve' || $request->status == 'reject') {
        $undangan->tgl_disahkan = now();
    } else {
        $undangan->tgl_disahkan = null;
    }
    $undangan->catatan = $request->catatan;
    $undangan->save();

    // Update status di kirim_document untuk user yang login
    $currentKirim = Kirim_Document::where('id_document', $id)
        ->where('jenis_document', 'undangan')
        ->where('id_penerima', $userId)
        ->first();
    if ($currentKirim) {
        $currentKirim->status = $request->status;
        $currentKirim->updated_at = now();
        $currentKirim->save();
    }

    if ($request->status == 'approve') {
        // QR dan notifikasi
        $qrText = "Disetujui oleh: " . Auth::user()->firstname . ' ' . Auth::user()->lastname . "\nTanggal: " . now()->translatedFormat('l, d F Y');
        $qrImage = QrCode::format('svg')->generate($qrText);
        $qrBase64 = base64_encode($qrImage);
        $undangan->qr_approved_by = $qrBase64;
        $undangan->save();

        Notifikasi::create([
            'judul' => "Undangan Disetujui",
            'judul_document' => $undangan->judul,
            'id_divisi' => $undangan->divisi_id_divisi,
            'updated_at' => now()
        ]);

        // Kirim dokumen ke semua user di divisi tujuan (kecuali pengirim sendiri)
        $namaDivisiArray = array_map('trim', explode(';', $undangan->tujuan));
        $divisiIds = \App\Models\Divisi::whereIn('nm_divisi', $namaDivisiArray)->pluck('id_divisi');
        $userTujuan = \App\Models\User::whereIn('divisi_id_divisi', $divisiIds)
            ->where('id', '!=', Auth::id())
            ->get();
        foreach ($userTujuan as $user) {
            $sudahDikirim = Kirim_Document::where('id_document', $undangan->id_undangan)
                ->where('jenis_document', 'undangan')
                ->where('id_pengirim', Auth::id())
                ->where('id_penerima', $user->id)
                ->exists();
            if (!$sudahDikirim) {
                //ID_PENGIRIM
                Kirim_Document::create([
                    'id_document' => $undangan->id_undangan,
                    'jenis_document' => 'undangan',
                    'id_pengirim' => Auth::id(),
                    'id_penerima' => $user->id,
                    'status' => 'approve',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    } elseif ($request->status == 'reject') {
        Notifikasi::create([
            'judul' => "Undangan Tidak Disetujui",
            'judul_document' => $undangan->judul,
            'id_divisi' => $undangan->divisi_id_divisi,
            'updated_at' => now()
        ]);
    } elseif ($request->status == 'correction') {
        Notifikasi::create([
            'judul' => "Undangan Perlu Dikoreksi",
            'judul_document' => $undangan->judul,
            'id_divisi' => $undangan->divisi_id_divisi,
            'updated_at' => now()
        ]);
    }
    return redirect()->route('undangan.' . Auth::user()->role->nm_role)
        ->with('success', 'Dokumen berhasil dibuat.');
    //return redirect()->route('undangan.manager')->with('success', 'Status undangan berhasil diperbarui.');
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

         // Pastikan field tujuan diubah ke array id divisi untuk form edit
         if (empty($undangan->tujuan)) {
             $undangan->tujuan = [];
         } elseif (!is_array($undangan->tujuan)) {
             $namaDivisiArray = array_map('trim', explode(';', $undangan->tujuan));
             $idDivisiArray = \App\Models\Divisi::whereIn('nm_divisi', $namaDivisiArray)->pluck('id_divisi')->toArray();
             $undangan->tujuan = $idDivisiArray;
         }

         return view(Auth::user()->role->nm_role.'.undangan.edit-undangan', compact('undangan', 'divisi', 'seri','managers'));

     }
     public function update(Request $request, $id)
     {
         
        $undangan = Undangan::findOrFail($id);
        //dd('Update function masuk');
        //dd($request->all()); 
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_undangan' => 'required|string',
            'tujuan' => 'required|array|min:1',
            //'tujuan.*' => 'exists:divisi,id_divisi',
            'nomor_undangan' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
        ]);
        
        
        
        if ($request->filled('judul')) {
            $undangan->judul = $request->judul;
        }
        if ($request->filled('isi_undangan')) {
            $undangan->isi_undangan = $request->isi_undangan;
        }
        if ($request->filled('tujuan')) {
            $tujuanIds = $request->tujuan; // array of id_divisi yang dikirim dari checkbox
            $namaDivisi = \App\Models\Divisi::whereIn('id_divisi', $tujuanIds)->pluck('nm_divisi')->toArray();
            $undangan->tujuan = implode('; ', $namaDivisi); // simpan nama divisinya
        }
        // Set status ke pending saat update (opsional, seperti memo)
        $undangan->status = 'pending';

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
        \Log::info('Update undangan berhasil', $undangan->toArray());

        
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
        $userId = Auth::id(); // Ambil ID user yang sedang login
        $undangan = Undangan::where('id_undangan', $id)->firstOrFail();

        // Pastikan tujuan selalu string nama divisi (untuk data lama)
        if (is_array($undangan->tujuan)) {
            $namaDivisiArray = \App\Models\Divisi::whereIn('id_divisi', $undangan->tujuan)->pluck('nm_divisi')->toArray();
            $undangan->tujuan = implode('; ', $namaDivisiArray);
        }

        $undanganCollection = collect([$undangan]); // Bungkus dalam collection

        $undanganCollection->transform(function ($undangan) use ($userId) {
            if ($undangan->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $undangan->final_status = $undangan->status; // Undangan dari divisi sendiri
            } else {
                $statusKirim = Kirim_Document::where('id_document', $undangan->id_undangan)
                    ->where('jenis_document', 'undangan')
                    ->where('id_penerima', $userId)
                    ->first();
                $undangan->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return $undangan;
        });
    
        // Karena hanya satu memo, kita bisa mengambil dari collection lagi
        $undangan = $undanganCollection->first();

        return view(Auth::user()->role->nm_role.'.undangan.view-undangan', compact('undangan'));
    }
    public function updateStatus(Request $request, $id)
    {
        $undangan = Undangan::findOrFail($id);

        // Validasi input
        $request->validate([
            'status' => 'required|in:approve,reject,pending,correction',
            'catatan' => 'nullable|string',
        ]);

        // Update status
        $undangan->status = $request->status;
        
        // Jika status 'approve', simpan tanggal pengesahan
        if ($request->status == 'approve') {
            $undangan->tgl_disahkan = now();
        } elseif ($request->status == 'reject') {
            $undangan->tgl_disahkan = now();
        }elseif ($request->status == 'correction') {
            $undangan->tgl_disahkan = now();
        }else{
            $undangan->tgl_disahkan = null; // Reset tanggal disahkan jika status bukan approve atau reject
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
