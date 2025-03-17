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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class MemoController extends Controller
{
    public function index(Request $request)
    {
        $memo = null;
        $divisi = Divisi::all();
        $seri = Seri::all();
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id();

        // Ambil ID memo yang sudah diarsipkan oleh user saat ini
        $memoDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();

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

        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortDirection);

        // Pencarian berdasarkan nama dokumen atau nomor memo
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
            });
        }

        // Pagination
        $memos = $query->paginate(6);
        // **Tambahkan status penerima untuk setiap memo**
        foreach ($memos as $memo) {
            $memo->status_penerima = Kirim_document::where('id_document', $memo->id_memo)
                ->where('jenis_document', 'memo')
                ->where('id_penerima', $userDivisiId)
                ->value('status') ?? $memo->status;
            }
            $status = $memo ? $memo->status_penerima : 'pending';

        return view(Auth::user()->role->nm_role . '.memo.memo-' . Auth::user()->role->nm_role, compact('memos', 'divisi', 'seri','status','sortDirection'));
    }

    public function show($id)
    {
        $memo = Memo::with('divisi')->findOrFail($id);
        return view('admin.view-memo', compact('memo'));
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

       

    return view(Auth::user()->role->nm_role.'.memo.add-memo', [
        'nomorSeriTahunan' => $nextSeri['seri_tahunan'], // Tambahkan nomor seri tahunan
        'nomorDokumen' => $nomorDokumen,
        'managers' => $managers
    ]);  
    }
    public function store(Request $request)
    {
        //  dd($request->all());

        $request->validate([
            'judul' => 'required|string|max:70',
            'isi_memo' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_memo' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'pembuat'=>'required|string|max:255',
            'catatan'=>'nullable|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
            'tanda_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max
        ],[
            'tanda_identitas.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
            'tanda_identitas.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
        ]);
        
        

        $filePath = null;
        if ($request->hasFile('tanda_identitas')) {
            $file = $request->file('tanda_identitas');
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
            'tujuan' => $request->input('tujuan'),
            'isi_memo' => $request->input('isi_memo'),
            'nomor_memo' => $request->input('nomor_memo'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'pembuat' => $request->input('pembuat'),
            'catatan' => $request->input('catatan'),
            'seri_surat' => $request->input('seri_surat'),
            'status' => 'pending',
            'nama_bertandatangan' => $request->input('nama_bertandatangan'),
            'tanda_identitas' => $filePath,

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
        $request->validate([
            'status' => 'required|in:approve,reject,pending',
            'catatan' => 'nullable|string',
        ]);
        
        if ($userDivisiId == $memo->divisi_id_divisi) {
        // Update status
            $memo->status = $request->status;
            
            // Jika status 'approve', simpan tanggal pengesahan
            if ($request->status == 'approve') {
                $memo->tgl_disahkan = now();
            } elseif ($request->status == 'reject') {
                $memo->tgl_disahkan = now();
            }else{
                $memo->tgl_disahkan = null;
            }
            

            // Simpan catatan jika ada
            $memo->catatan = $request->catatan;

            // Simpan perubahan
            $memo->save();
        } else {
            // Jika user dari divisi lain, update status di tabel kirim_document
            Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'memo')
                ->where('id_penerima', $userId)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);
        }

        Notifikasi::create([
            'judul' => "Memo {$request->status}",
            'judul_document' => $memo->judul,
            'id_divisi' => $memo->divisi_id_divisi,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Status memo berhasil diperbarui.');
    }

    public function edit($id)
     {
         $memo = Memo::findOrFail($id);
         $divisi = Divisi::all();
         $seri = Seri::all();  
         
         return view(Auth::user()->role->nm_role. '.memo.edit-memo', compact('memo', 'divisi', 'seri'));
     }
     public function update(Request $request, $id)
     {
        $memo = Memo::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:70',
            'isi_memo' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_memo' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
            'tanda_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ],[
            'tanda_identitas.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
            'tanda_identitas.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
        ]);

        if ($request->filled('judul')) {
            $memo->judul = $request->judul;
        }
        if ($request->filled('isi_memo')) {
            $memo->isi_memo = $request->isi_memo;
        }
        if ($request->filled('tujuan')) {
            $memo->tujuan = $request->tujuan;
        }
        if ($request->filled('nomor_memo')) {
            $memo->nomor_memo = $request->nomor_memo;
        }
        if ($request->filled('nama_bertandatangan')) {
            $memo->nama_bertandatangan = $request->nama_bertandatangan;
        }
        if ($request->filled('tgl_surat')) {
            $memo->tgl_dibuat = bcrypt($request->tgl_dibuat);
        }
        if ($request->filled('seri_surat')) {
            $memo->seri_memo = $request->seri_memo;
        }
        if ($request->filled('tgl_disahkan')) {
            $memo->tgl_disahkan = $request->tgl_disahkan;
        }
        if ($request->filled('divisi_id_divisi')) {
            $memo->divisi_id_divisi = $request->divisi_id_divisi;
        }
        if ($request->hasFile('tanda_identitas')) {
            $file = $request->file('tanda_identitas');
            $memo->tanda_identitas = file_get_contents($file->getRealPath());
        }
        
        $memo->save();
 
         return redirect()->route('memo.'.Auth::user()->role->nm_role)->with('success', 'User updated successfully');
     }
     public function destroy($id)
     {
         $memo = Memo::findOrFail($id);

        if ($memo->tanda_identitas && file_exists(public_path($memo->tanda_identitas))) {
            unlink(public_path($memo->tanda_identitas));
        }

         $memo->delete();
 
         return redirect()->route('memo.' .Auth::user()->role->nm_role)->with('success', 'Memo deleted successfully.');
     }

    //  menampilkan file yang disimpan dalam database
    public function showFile($id)
    {
        $memo = Memo::findOrFail($id);

        if (!$memo->tanda_identitas) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        $fileContent = base64_decode($memo->tanda_identitas);
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

        if (!$memo->tanda_identitas) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fileData = base64_decode($memo->tanda_identitas);
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
        $memo = Kirim_Document::where('jenis_document', 'memo')
            ->where('id_document', $id)
            ->with(['memo', 'pengirim', 'penerima'])
            ->firstOrFail();

        return view('manager.memo.view-memoDiterima', compact('memo'));
    }
    public function view($id)
    {
        $memo = Memo::where('id_memo', $id)->firstOrFail();

        return view(Auth::user()->role->nm_role.'.memo.view-memo', compact('memo'));
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
