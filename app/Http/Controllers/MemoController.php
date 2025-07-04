<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\kategori_barang;
use App\Models\Memo;
use App\Models\Seri;
use App\Models\Arsip;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Notifikasi;
use App\Models\Kirim_Document;
use App\Models\Backup_Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
class MemoController extends Controller
{
    public function index(Request $request)
    {
        
        $divisi = Divisi::all();
        $seri = Seri::all();
        $user = User::all();
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id();

        // Ambil ID memo yang sudah diarsipkan oleh user saat ini
        $memoDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();
        $sortBy = $request->get('sort_by', 'created_at'); // default ke created_at
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_memo', 'judul'];
         if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // fallback default
        }

        // Query memo dengan filter
        $query = Memo::with('divisi')
        ->whereNotIn('id_memo', $memoDiarsipkan) // Filter memo yang belum diarsipkan
        ->where(function ($q) use ($userDivisiId, $userId) {
            // Memo yang dibuat oleh divisi user sendiri
            $q->where('divisi_id_divisi', $userDivisiId)
            
            // Memo yang dikirim ke user dari divisi lain melalui tabel kirim_document
            ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {
                $query->where('jenis_document', 'memo')
                      ->where('id_penerima', $userId)
                      ->whereHas('penerima', function ($subQuery) use ($userDivisiId) {
                          $subQuery->where('divisi_id_divisi', $userDivisiId);
                      });
            });
        });
       
    

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

         // Ambil semua arsip memo berdasarkan user login
        $arsipMemoQuery = Arsip::where('user_id', $userId)
        ->where('jenis_document', 'memo')
        ->with('document');

        
        $query->orderBy($sortBy, $sortDirection);

        // Pencarian berdasarkan nama dokumen atau nomor memo
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
            });
        }
         

        // Pagination
        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $memos = $query->paginate($perPage);
        // **Tambahkan status penerima untuk setiap memo**
        $memos->getCollection()->transform(function ($memo) use ($userId) {
            if ($memo->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $memo->final_status = $memo->status; // Memo dari divisi sendiri
            } else {
                $statusKirim= Kirim_Document::where('id_document', $memo->id_memo)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $userId)
                    ->first();
                $memo->final_status = $statusKirim ? $statusKirim->status : '-';
                // Cari status kiriman untuk user login
            }
            return $memo;
        });
            $kirimDocuments = Kirim_Document::where('jenis_document', 'memo')
        ->whereHas('memo') // Memastikan dokumen adalah memo
        ->orderBy('id_kirim_document', 'desc') // Ambil data terbaru berdasarkan ID terbesar
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
        return view(Auth::user()->role->nm_role . '.memo.memo-' . Auth::user()->role->nm_role, compact('memos', 'divisi', 'seri','sortDirection', 'kirimDocuments'));
    }

    public function superadmin(Request $request){
        $divisi = Divisi::all();
        $seri = Seri::all();
        $userId = Auth::id();
        

        $memoDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();
        $sortBy = $request->get('sort_by', 'created_at'); // default ke created_at
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_memo', 'judul'];
         if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // fallback default
        }

        $query = Memo::query()
        ->whereNotIn('id_memo', $memoDiarsipkan)
        ->orderBy($sortBy, $sortDirection);

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

         // Ambil semua arsip memo berdasarkan user login
        $arsipMemoQuery = Arsip::where('user_id', $userId)
        ->where('jenis_document', 'memo')
        ->with('document');

        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortDirection);

        if ($request->filled('divisi_id_divisi') && $request->divisi_id_divisi != 'pilih') {
    $query->where('divisi_id_divisi', $request->divisi_id_divisi);
}

        // Pencarian berdasarkan nama dokumen atau nomor memo
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
            });
        }
        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $memos = $query->paginate($perPage);


        return view( 'superadmin.memo.memo-superadmin', compact('memos', 'divisi', 'seri','sortDirection'));
    }

    public function show($id)
    {
        $userId = Auth::id();
        $memo = Memo::with('divisi')->findOrFail($id);

        $memo->getCollection()->transform(function ($memo) use ($userId) {
            if ($memo->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $memo->final_status = $memo->status; // Memo dari divisi sendiri
            } else {
                $statusKirim= Kirim_Document::where('id_document', $memo->id_memo)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $userId)
                    ->first();
                $memo->final_status = $statusKirim ? $statusKirim->status : '-';
                // Cari status kiriman untuk user login
            }
            return $memo;
        });

        return view('admin.view-memo', compact('memo'));
    }



    public function create()
    {
        $divisiId = auth()->user()->divisi_id_divisi;
    $divisiName = auth()->user()->divisi->nm_divisi;
    $divisiList = Divisi::all(); 
    
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

       

    return view(Auth::user()->role->nm_role.'.memo.add-memo', [
        'nomorSeriTahunan' => $nextSeri['seri_tahunan'], // Tambahkan nomor seri tahunan
        'nomorDokumen' => $nomorDokumen,
        'managers' => $managers,
        'divisiList' => $divisiList
    ]);  
    }
    public function store(Request $request)
    {
        

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'isi_memo' => 'required|string',
            'tujuan' => 'required|array|min:1',
            'nomor_memo' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'manager_user_id' => 'required|exists:users,id',
            'pembuat'=>'required|string|max:255',
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

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        
        

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
        $memo = Memo::create([
            'divisi_id_divisi' => $request->input('divisi_id_divisi'),
            'judul' => $request->input('judul'),
            'tujuan' => implode('; ', $request->tujuan),
            'isi_memo' => $request->input('isi_memo'),
            'nomor_memo' => $request->input('nomor_memo'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'pembuat' => $request->input('pembuat'),
            'catatan' => $request->input('catatan'),
            'seri_surat' => $request->input('seri_surat'),
            'status' => 'pending',
            'nama_bertandatangan' => $request->input('nama_bertandatangan'),
            'lampiran' => $filePath,

        ]);
        if ($request->has('jumlah_kolom')&& !empty($request->nomor)) {
            foreach ($request->nomor as $key => $nomor) {
                kategori_barang::create([
                    'memo_id_memo' => $memo->id_memo,
                    'memo_divisi_id_divisi' => $memo->divisi_id_divisi, // Gunakan dari memo
                    'nomor' => $nomor, // Ambil dari array
                    'barang' => $request->barang[$key] ?? null,
                    'qty' => $request->qty[$key] ?? null,
                    'satuan' => $request->satuan[$key] ?? null,
                ]);
            }
        }
    
        $creator = Auth::user();
            

        $managers = User::where('id', $request->manager_user_id)
                        ->get();

        $sentCount = 0;
        
        foreach ($managers as $manager) {
            
            $kirim = Kirim_document::create([
                'id_document' => $memo->id_memo,
                'jenis_document' => 'memo',
                'id_pengirim' => $creator->id,
                'id_penerima' => $manager->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        
            if ($kirim) {
                $sentCount++;
            }
        }
        

        return redirect()->route('memo.'. Auth::user()->role->nm_role)->with('success', 'Dokumen berhasil dibuat.');

    }

    private function convertToRoman($number) {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$number];
    }
    public function updateDocumentStatus(Memo $memo) {
        $recipients = $memo->recipients;
    
        if ($recipients->every(fn($recipient) => $recipient->status === 'approve')) {
            $memo->update(['status' => 'approve']);
        } elseif ($recipients->contains(fn($recipient) => $recipient->status === 'reject')) {
            $memo->update(['status' => 'reject']);
        } elseif ($recipients->contains(fn($recipient) => $recipient->status === 'correction')) {
            $memo->update(['status' => 'correction']);
        } else {
            $memo->update(['status' => 'pending']);
        }
    }
    
    public function updateDocumentApprovalDate(Memo $memo) {
        if ($memo->status !== 'pending') {
            $memo->update(['tanggal_disahkan' => now()]);
        }
    }
    public function updateStatus(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id();

        // Validasi input
        if ($request->status == 'approve') {
            $request->validate([
                'status' => 'required|in:approve,reject,pending,correction',
                'catatan' => 'nullable|string',
            ]);
        } else {
            $request->validate([
                'status' => 'required|in:approve,reject,pending,correction',
                'catatan' => 'required|string',
            ]);
        }
        
        
        if ($userDivisiId == $memo->divisi_id_divisi) {
        // Update status
            $memo->status = $request->status;
            $currentKirim = Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'memo')
                ->where('id_penerima', $userId)
                ->first();
                
            if ($currentKirim) {
                $currentKirim->status = $request->status;
                $currentKirim->updated_at = now();
                $currentKirim->save();
            
            // Jika status 'approve', simpan tanggal pengesahan
            if ($request->status == 'approve') {
                $memo->tgl_disahkan = now();
                $qrText = "Disetujui oleh: " . Auth::user()->firstname . ' ' . Auth::user()->lastname . "\nTanggal: " . now()->translatedFormat('l, d F Y');
                $qrImage = QrCode::format('svg')->generate($qrText);
                $qrBase64 = base64_encode($qrImage);
                $memo->qr_approved_by = $qrBase64;  

                Notifikasi::create([
                    'judul' => "Memo Disetujui",
                    'judul_document' => $memo->judul,
                    'id_divisi' => $memo->divisi_id_divisi,
                    'updated_at' => now()
                ]);
            } elseif ($request->status == 'reject') {
                $memo->tgl_disahkan = now();
                Notifikasi::create([
                    'judul' => "Memo Ditolak",
                    'judul_document' => $memo->judul,
                    'id_divisi' => $memo->divisi_id_divisi,
                    'updated_at' => now()
                ]);
            } elseif ($request->status == 'correction') {
                Notifikasi::create([
                    'judul' => "Memo Perlu Revisi",
                    'judul_document' => $memo->judul,
                    'id_divisi' => $memo->divisi_id_divisi,
                    'updated_at' => now()
                ]);
            }else{
                $memo->tgl_disahkan = null;
            }
            

            // Simpan catatan jika ada
            $memo->catatan = $request->catatan;

            // Simpan perubahan
            $memo->save();
        }
            
        } else {
                // Jika user dari divisi lain, update status di tabel kirim_document
                $currentKirim = Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'memo')
                ->where('id_penerima', $userId)
                ->first();

            if ($currentKirim) {
                $currentKirim->status = $request->status;
                $currentKirim->updated_at = now();
                $currentKirim->save();
                

                // Update juga status record kiriman sebelumnya (pengirim sebelumnya)
                Kirim_document::where('id_document', $id)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $currentKirim->id_pengirim)
                    ->where('status', 'pending')
                    ->update([
                        'status' => $request->status,
                        'updated_at' => now()
                    ]);
                    if(($request->status == 'approve')){
                        Notifikasi::create([
                            'judul' => "Memo Ditindak Lanjuti",
                            'judul_document' => $memo->judul,
                            'id_divisi' => $memo->divisi_id_divisi,
                            'updated_at' => now()
                        ]);
                        
                    }
                
                    if ($request->status == 'reject') {
                        $memo->status = 'reject';
                        $memo->tgl_disahkan = now();
                        $memo->catatan = $request->catatan ?? $memo->catatan;
                        $memo->save();
                        
                        Notifikasi::create([
                            'judul' => "Memo Tidak Ditindak Lanjuti",
                            'judul_document' => $memo->judul,
                            'id_divisi' => $memo->divisi_id_divisi,
                            'updated_at' => now()
                        ]);
                    }
                    
            }
            
            
        }

        

        return redirect()->route('memo.diterima')->with('success', 'Status memo berhasil diperbarui.');
    }

    public function edit($id)
     {
         $memo = Memo::findOrFail($id);
         $divisi = Divisi::all();
         $divisiId = auth()->user()->divisi_id_divisi;
         $seri = Seri::all();  
         $managers = User::where('divisi_id_divisi', $divisiId)
        ->where('position_id_position', '2')
        ->get(['id', 'firstname', 'lastname']);
         
         return view(Auth::user()->role->nm_role. '.memo.edit-memo', compact('memo', 'divisi', 'seri', 'managers'));
     }
     public function update(Request $request, $id)
     {
        $memo = Memo::findOrFail($id);
        // dd($request->all());    


        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_memo' => 'required|string',
            'tujuan' => 'required|array|min:1',
            'nomor_memo' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
        ]);

        if ($request->filled('judul')) {
            $memo->judul = $request->judul;
        }
        if ($request->filled('isi_memo')) {
            $memo->isi_memo = $request->isi_memo;
        }
        if ($request->filled('tujuan')) {
            $memo->tujuan = implode(';', $request->tujuan);
        }
        if ($request->filled('nomor_memo')) {
            $memo->nomor_memo = $request->nomor_memo    ;
        }
        if ($request->filled('nama_bertandatangan')) {
            $memo->nama_bertandatangan = $request->nama_bertandatangan;
        }
        if ($request->filled('tgl_dibuat')) {
            $memo->tgl_dibuat = $request->tgl_dibuat;
        }
        if ($request->filled('seri_surat')) {
            $memo->seri_surat = $request->seri_surat;
        }
        if ($request->filled('tgl_disahkan')) {
            $memo->tgl_disahkan = $request->tgl_disahkan;
        }
        if ($request->filled('divisi_id_divisi')) {
            $memo->divisi_id_divisi = $request->divisi_id_divisi;
        }
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $memo->lampiran = file_get_contents($file->getRealPath());
        }
        $memo->status = 'pending'; // Set status ke pending saat update
        $memo->save();

        if ($request->has('kategori_barang')) {
            foreach ($request->kategori_barang as $dataBarang) {
                if (isset($dataBarang['id_kategori_barang']) && $dataBarang['id_kategori_barang'] != null) {
                    // Cek apakah barang sudah ada di database
                    $barang = $memo->kategoriBarang()->find($dataBarang['id_kategori_barang']);
                    if ($barang) {
                        $barang->update([
                            'memo_id_memo' => $memo->id_memo,
                            'memo_divisi_id_divisi' => $memo->divisi_id_divisi,
                            'nomor' => $dataBarang['nomor'],
                            'barang' => $dataBarang['barang'],
                            'qty' => $dataBarang['qty'],
                            'satuan' => $dataBarang['satuan'],
                        ]);
                    }
                } 
            }
        }

        
         return redirect()->route('memo.'.Auth::user()->role->nm_role)->with('success', 'User updated successfully');
     }
     public function destroy($id)
     {
        $memo = Memo::findOrFail($id);

        // Pindahkan data ke tabel backup
        Backup_Document::create([
            'id_document' => $memo->id_memo,
            'jenis_document' => 'memo',
            'tujuan'=> $memo->tujuan,
            'judul' => $memo->judul,
            'nomor_document' => $memo->nomor_memo,
            'tgl_dibuat' => $memo->tgl_dibuat,
            'tgl_disahkan' => $memo->tgl_disahkan,
            'status' => $memo->status,
            'catatan' => $memo->catatan,
            'isi_document' => $memo->isi_memo,
            'seri_document' => $memo->seri_surat,
            'nama_bertandatangan'=> $memo->nama_bertandatangan,
            'lampiran' => $memo->lampiran,
            'pembuat' => $memo->pembuat,
            'divisi_id_divisi' => $memo->divisi_id_divisi,
            'created_at' => $memo->created_at,
            'updated_at' => $memo->updated_at,
            
            // tambahkan kolom lain jika ada
        ]);
    
        // Hapus file lampiran jika ada
        if ($memo->lampiran && file_exists(public_path($memo->lampiran))) {
            unlink(public_path($memo->lampiran));
        }
    
        // Hapus dari tabel memo
        $memo->delete();
 
         return redirect()->route('memo.' .Auth::user()->role->nm_role)->with('success', 'Memo deleted successfully.');
     }

    //  menampilkan file yang disimpan dalam database
    public function showFile($id)
    {
        $memo = Memo::findOrFail($id);

        if (!$memo->lampiran) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        $fileContent = base64_decode($memo->lampiran);
        if (!$fileContent) {
            return response()->json(['error' => 'File corrupt atau tidak bisa di-decode.'], 500);
        }

        // Pastikan MIME type valid
        $finfo = finfo_open();
        $mimeType = finfo_buffer($finfo, $fileContent, FILEINFO_MIME_TYPE);
        finfo_close($finfo);

        // Validasi MIME type
        $validMimeTypes = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png'
        ];
        
        if (!isset($validMimeTypes[$mimeType])) {
            return response()->json(['error' => 'Format file tidak didukung.'], 400);
        }

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="dokumen.' . $validMimeTypes[$mimeType] . '"');
    }

    private function validateMimeType($mimeType)
    {
        // Valid MIME types for PDF, JPG, PNG, JPEG
        $validMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        
        if (in_array($mimeType, $validMimeTypes)) {
            return $mimeType;
        }

        return 'application/octet-stream'; // Default fallback MIME type if not valid
    }

    // Fungsi tambahan untuk mendapatkan ekstensi dari MIME type
    private function getExtension($mimeType)
    {
        $map = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];
        return $map[$mimeType] ?? 'bin';
    }

    // Fungsi download file
    public function downloadFile($id)
    {
        $memo = Memo::findOrFail($id);

        if (!$memo->lampiran) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fileData = base64_decode($memo->lampiran);
        $mimeType = finfo_buffer(finfo_open(), $fileData, FILEINFO_MIME_TYPE);
        $extension = $this->getExtension($mimeType);

        return response()->streamDownload(function () use ($fileData) {
            echo $fileData;
        }, "memo_{$id}.$extension", ['Content-Type' => $mimeType]);
    }
    
     public function showTerkirim($id)
    {
        $memo = Kirim_Document::where('jenis_document', 'memo')
            ->where('id_document', $id)
            ->with(['memo', 'penerima', 'pengirim'])
            ->firstOrFail();

        return view('manager.memo.view-memoTerkirim', compact('memo'));
    }

    public function showDiterima($id)
    {
        $userId = auth()->id(); // Ambil ID user yang sedang login (Manager divisi)

        $memo = Kirim_Document::where('jenis_document', 'memo')
            ->where('id_penerima', $userId)
             ->where('id_document', $id)
            ->Where('status', 'pending') // Status di tabel kirim_document
            ->whereHas('memo')
            ->with('memo') // Pastikan ada relasi 'memo' di model Kirim_Document
            ->firstOrFail();

        return view('manager.memo.view-memoDiterima', compact('memo'));
    }
    public function view($id)
    {
        $userId = auth()->id(); // Ambil ID user yang sedang login
        $memo = Memo::where('id_memo', $id)->firstOrFail();

        // Ubah menjadi Collection manual
        $memoCollection = collect([$memo]); // Bungkus dalam collection

        $memoCollection->transform(function ($memo) use ($userId) {
            if ($memo->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $memo->final_status = $memo->status; // Memo dari divisi sendiri
            } else {
                $statusKirim = Kirim_Document::where('id_document', $memo->id_memo)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $userId)
                    ->first();
                $memo->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return $memo;
        });

        // Karena hanya satu memo, kita bisa mengambil dari collection lagi
        $memo = $memoCollection->first();

        return view(Auth::user()->role->nm_role . '.memo.view-memo', compact('memo'));
    }



    public function updateStatusNotif(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);
        $memo->status = $request->status;
        $memo->save();
    
        // Simpan notifikasi
        Notifikasi::create([
            'judul' => "Memo {$request->status}",
            'jenis_document' => 'memo',
            'id_divisi' => $memo->divisi_id,
            'dibaca'         => false,
            'updated_at' => now()
        ]);
    
        return redirect()->back()->with('success', 'Status memo berhasil diperbarui.');
    }
    
}
