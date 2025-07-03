<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use App\Models\Risalah;
use App\Models\RisalahDetail;
use App\Models\Seri;
use App\Models\Arsip;
use App\Models\Notifikasi;
use App\Models\Kirim_Document;
use App\Models\BackupRisalah;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Undangan;

class RisalahController extends Controller
{
    public function index(Request $request)
    {
        $divisi = Divisi::all();
        $seri = Seri::all(); 
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id(); 

        // Ambil ID memo yang sudah diarsipkan oleh user saat ini
        $risalahDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();
        $sortBy = $request->get('sort_by', 'created_at'); // default ke created_at
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_risalah', 'judul'];
         if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // fallback default
        }

        // Ambil memo yang belum diarsipkan oleh user saat ini
        $query = Risalah::with('divisi')
        ->whereNotIn('id_risalah', $risalahDiarsipkan) // Filter memo yang belum diarsipkan
        ->where(function ($q) use ($userDivisiId, $userId) {
            // Memo yang dibuat oleh divisi user sendiri
            $q->where('divisi_id_divisi', $userDivisiId)
            
            // Memo yang dikirim ke user dari divisi lain melalui tabel kirim_document
            ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {
                $query->where('jenis_document', 'risalah')
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
                  ->orWhere('nomor_risalah', 'like', '%' . $request->search . '%');
            });
        }

        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Mengambil daftar memo dengan relasi divisi
        $risalahs = $query->with('divisi')->orderBy('tgl_dibuat', 'desc')->paginate(10);

        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $risalahs = $query->paginate($perPage);

        $risalahs->getCollection()->transform(function ($risalah) use ($userId) {
            if ($risalah->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $risalah->final_status = $risalah->status; // Memo dari divisi sendiri
            } else {
                $statusKirim= Kirim_Document::where('id_document', $risalah->id_risalah)
                    ->where('jenis_document', 'risalah')
                    ->where('id_penerima', $userId)
                    ->first();
                $risalah->final_status = $statusKirim ? $statusKirim->status : '-';
                // Cari status kiriman untuk user login
            }
            return $risalah;
        });
          $kirimDocuments = Kirim_Document::where('jenis_document', 'risalah')
        ->whereHas('risalah') // Memastikan dokumen adalah risalah
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

        return view(Auth::user()->role->nm_role.'.risalah.risalah-'.Auth::user()->role->nm_role, compact('risalahs', 'divisi', 'seri', 'sortDirection', 'kirimDocuments'));
    }

    public function superadmin(Request $request){
        $divisi = Divisi::all();
        $seri = Seri::all();
        $userId = Auth::id();
        

        $risalahDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();
        $sortBy = $request->get('sort_by', 'created_at'); // default ke created_at
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_risalah', 'judul'];
         if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // fallback default
        }

        $query = Risalah::query()
        ->whereNotIn('id_risalah', $risalahDiarsipkan)
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
        $arsipRisalahQuery = Arsip::where('user_id', $userId)
        ->where('jenis_document', 'risalah')
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
                ->orWhere('nomor_risalah', 'like', '%' . $request->search . '%');
            });
        }
        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $risalahs = $query->paginate($perPage);

        return view( 'superadmin.risalah.risalah-superadmin', compact('risalahs', 'divisi', 'seri','sortDirection'));
    }

    public function create()
    {
        $divisiId = auth()->user()->divisi_id_divisi;
        $divisiName = auth()->user()->divisi->nm_divisi;
        $undangan = Undangan::whereNotIn('judul', function($query) {
                        $query->select('judul')->from('risalah');
                    })
                    ->where('divisi_id_divisi', $divisiId)
                    ->get();

        $risalah = new Risalah(); // atau ambil dari data risalah terakhir, terserah kebutuhanmu
        
        // Ambil nomor seri berikutnya
        $nextSeri = Seri::getNextSeri(false);
        
        // Konversi bulan ke angka Romawi
        $bulanRomawi = $this->convertToRoman(now()->month);
    
        // Format nomor dokumen sesuai contoh pada gambar
        $nomorDokumen = sprintf(
            "RIS-%d.%d/REKA/%s/%s/%d",
            $nextSeri['seri_tahunan'],
            $nextSeri['seri_bulanan'],
            strtoupper($divisiName),
            $bulanRomawi,
            now()->year
        );
    
        $managers = User::where('divisi_id_divisi', $divisiId)
            ->where('position_id_position', '2')
            ->get(['id', 'firstname', 'lastname']);
    
        return view(Auth::user()->role->nm_role.'.risalah.add-risalah', [
            'risalah' => $risalah,
            'nomorSeriTahunan' => $nextSeri['seri_tahunan'], // Tambahkan nomor seri tahunan
            'nomorDokumen' => $nomorDokumen,
            'managers' => $managers,
            'undangan' => $undangan
        ]);  
    }
    
    public function store(Request $request)
{
    //dd($request->all());

    $request->validate([
        'tgl_dibuat' => 'required|date',
        'seri_surat' => 'required|string',
        'nomor_risalah' => 'required|string',
        'agenda' => 'required|string',
        'tempat' => 'required|string',
        'waktu_mulai' => 'required|string',
        'waktu_selesai' => 'required|string',
        'judul' => 'required|string',
        'divisi_id_divisi' => 'required|integer|exists:divisi,id_divisi', 
        'nama_bertandatangan' => 'required|string',
        'pembuat'=>'required|string',
        'nomor' => 'nullable|array',
        'topik' => 'nullable|array',
        'pembahasan' => 'nullable|array',
        'tindak_lanjut' => 'nullable|array',
        'target' => 'nullable|array',
        'pic' => 'nullable|array',
        'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ],[
        'lampiran.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
        'lampiran.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
    ]);

    $filePath = null;
        if ($request->hasFile('lampiran')) {
        $file = $request->file('lampiran');
        $filePath = base64_encode(file_get_contents($file->getRealPath()));
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

    // Simpan risalah utama
    $risalah = Risalah::create([
        'divisi_id_divisi' => auth()->user()->divisi_id_divisi,
        'tgl_dibuat' => $request->tgl_dibuat,
        'seri_surat' => $request->seri_surat,
        'nomor_risalah' => $request->nomor_risalah,
        'agenda' => $request->agenda,
        'tempat' => $request->tempat,
        'waktu_mulai' => $request->waktu_mulai,
        'waktu_selesai' => $request->waktu_selesai,
        'status' => 'pending',
        'judul' => $request->judul,
        'pembuat' => $request->pembuat,
        'lampiran' => $filePath,
        'nama_bertandatangan' => $request->nama_bertandatangan,
        'risalah_id_risalah' => $request->id_risalah
    ]);

    if ($request->has('nomor') && is_array($request->nomor)) {
    foreach ($request->nomor as $index => $no) {
        RisalahDetail::create([
            'risalah_id_risalah' => $risalah->id_risalah,
            'nomor' => $no,
            'topik' => $request->topik[$index] ?? '',
            'pembahasan' => $request->pembahasan[$index] ?? '',
            'tindak_lanjut' => $request->tindak_lanjut[$index] ?? '',
            'target' => $request->target[$index] ?? '',
            'pic' => $request->pic[$index] ?? '',
        ]);
    }
}


    return redirect()->route('risalah.'.Auth::user()->role->nm_role)->with('success', 'Risalah berhasil ditambahkan');
}

public function updateDocumentStatus(Risalah $risalah) {
    $recipients = $risalah->recipients;

    if ($recipients->every(fn($recipient) => $recipient->status === 'approve')) {
        $risalah->update(['status' => 'approve']);
    } elseif ($recipients->contains(fn($recipient) => $recipient->status === 'reject')) {
        $risalah->update(['status' => 'reject']);
    } else {
        $risalah->update(['status' => 'pending']);
    }
}

public function updateDocumentApprovalDate(Risalah $risalah) {
    if ($risalah->status !== 'pending') {
        $risalah->update(['tanggal_disahkan' => now()]);
    }
}

public function approve(risalah $risalah) {
    $risalah->update([
        'status' => 'approve',
        'tanggal_disahkan' => now() // Set tanggal disahkan
    ]);

    return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
}

public function reject(Risalah $risalah) {
    $risalah->update([
        'status' => 'reject',
        'tanggal_disahkan' => now() // Set tanggal disahkan
    ]);

    return redirect()->back()->with('error', 'Dokumen ditolak.');
}

public function edit($id)
{
    // Ambil data risalah beserta detailnya
    $divisi = Divisi::all();
    $seri = Seri::all(); 
    $risalah = Risalah::with('risalahDetails')->findOrFail($id);
    

    // Ambil daftar manajer berdasarkan divisi yang sama
    $managers = User::where('divisi_id_divisi', $risalah->divisi_id_divisi)
        ->where('position_id_position', '2')
        ->get(['id', 'firstname', 'lastname']);

    return view(Auth::user()->role->nm_role.'.risalah.edit-risalah', compact('risalah', 'divisi', 'seri', 'managers'));
}
    
public function update(Request $request, $id)
    {
        // Validasi data
        $request->validate([
            'judul' => 'required',
            'agenda' => 'required',
            'tempat' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'nama_bertandatangan' => 'required',
            'nomor.*' => 'required',
            'topik.*' => 'required',
            'pembahasan.*' => 'required',
            'tindak_lanjut.*' => 'required',
            'target.*' => 'required',
            'pic.*' => 'required',
        ]);

        // Update data risalah utama
        $risalah = Risalah::findOrFail($id);
        $risalah->update([
            'tgl_dibuat' => $request->tgl_dibuat,
            'judul' => $request->judul,
            'agenda' => $request->agenda,
            'tempat' => $request->tempat,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'nama_bertandatangan' => $request->nama_bertandatangan,
            // tambahan lainnya sesuai kebutuhan
        ]);

        // Hapus data risalahDetails lama jika ada
        if ($request->has('nomor')) {
            // Hapus data risalahDetails lama jika ada
            if ($risalah->risalahDetails()->exists()) {
        $risalah->risalahDetails()->delete();
        }

        // Simpan data risalahDetails yang baru
        foreach ($request->nomor as $index => $nomor) {
            $risalah->risalahDetails()->create([
                'nomor' => $nomor,
                'topik' => $request->topik[$index],
                'pembahasan' => $request->pembahasan[$index],
                'tindak_lanjut' => $request->tindak_lanjut[$index],
                'target' => $request->target[$index],
                'pic' => $request->pic[$index],
            ]);
        }
    }

        // Redirect ke halaman risalah dengan pesan sukses
        return redirect()->route('risalah.'.Auth::user()->role->nm_role)->with('success', 'Risalah berhasil diperbarui.');
    }

    public function destroy($id)
{
    $risalah = Risalah::findOrFail($id);

    DB::transaction(function () use ($risalah) {
        BackupRisalah::create([
            'id_document' => $risalah->id_risalah,
            'jenis_document' => 'risalah',
            'nomor_document' => $risalah->nomor_risalah,
            'seri_document' => $risalah->seri_surat,
            'tgl_dibuat' => $risalah->tgl_dibuat,
            'tgl_disahkan' => $risalah->tgl_disahkan,
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
        ]);

        // Hapus file lampiran jika ada
        $lampiranPath = public_path($risalah->lampiran);
        if ($risalah->lampiran && file_exists($lampiranPath)) {
            unlink($lampiranPath);
        }

        RisalahDetail::where('risalah_id_risalah', $risalah->id_risalah)->delete();
        $risalah->delete();
    });

    return redirect()->route('risalah.'.Auth::user()->role->nm_role)->with('success', 'Dokumen berhasil dihapus.');
}

    
    private function convertToRoman($number)
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
            6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
            11 => 'XI', 12 => 'XII'
        ];
        return $map[$number] ?? '';
    }

    public function view($id)
    {
        $userId = Auth::id();
        $risalah = risalah::where('id_risalah', $id)->firstOrFail();

        $risalahCollection = collect([$risalah]); // Bungkus dalam collection

        $risalahCollection->transform(function ($risalah) use ($userId) {
            if ($risalah->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $risalah->final_status = $risalah->status; // Risalah dari divisi sendiri
            } else {
                $statusKirim = Kirim_Document::where('id_document', $risalah->id_risalah)
                    ->where('jenis_document', 'risalah')
                    ->where('id_penerima', $userId)
                    ->first();
                $risalah->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return $risalah;
        });

        // Karena hanya satu memo, kita bisa mengambil dari collection lagi
        $risalah = $risalahCollection->first();

        return view(Auth::user()->role->nm_role.'.risalah.view-risalah', compact('risalah'));
    }

    public function updateStatus(Request $request, $id)
    {
        $risalah = Risalah::findOrFail($id);
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id();

        // Validasi input
        $request->validate([
            'status' => 'required|in:approve,reject,pending',
            'catatan' => 'nullable|string',
        ]);
        
        if ($userDivisiId == $risalah->divisi_id_divisi) {
        // Update status
            $risalah->status = $request->status;
            $currentKirim = Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'risalah')
                ->where('id_penerima', $userId)
                ->first();
                
            if ($currentKirim) {
                $currentKirim->status = $request->status;
                $currentKirim->updated_at = now();
                $currentKirim->save();
            }
            
            // Jika status 'approve', simpan tanggal pengesahan
            if ($request->status == 'approve') {
                $risalah->tgl_disahkan = now();

                $qrText = "Disetujui oleh: " . Auth::user()->firstname . ' ' . Auth::user()->lastname . "\nTanggal: " . now()->translatedFormat('l, d F Y');
                $qrImage = QrCode::format('svg')->generate($qrText);
                $qrBase64 = base64_encode($qrImage);
                $risalah->qr_approved_by = $qrBase64;

                Notifikasi::create([
                    'judul' => "Risalah Disetujui",
                    'judul_document' => $risalah->judul,
                    'id_divisi' => $risalah->divisi_id_divisi,
                    'updated_at' => now()
                ]);
            } elseif ($request->status == 'reject') {
                $risalah->tgl_disahkan = now();
                Notifikasi::create([
                    'judul' => "Risalah Ditolak",
                    'judul_document' => $risalah->judul,
                    'id_divisi' => $risalah->divisi_id_divisi,
                    'updated_at' => now()
                ]);
            }else{
                $risalah->tgl_disahkan = null;
            }
            

            // Simpan catatan jika ada
            $risalah->catatan = $request->catatan;

            // Simpan perubahan
            $risalah->save();
            
        } else {
                // Jika user dari divisi lain, update status di tabel kirim_document
                $currentKirim = Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'risalah')
                ->where('id_penerima', $userId)
                ->first();

            if ($currentKirim) {
                $currentKirim->status = $request->status;
                $currentKirim->updated_at = now();
                $currentKirim->save();

                // Update juga status record kiriman sebelumnya (pengirim sebelumnya)
                Kirim_document::where('id_document', $id)
                    ->where('jenis_document', 'risalah')
                    ->where('id_penerima', $currentKirim->id_pengirim)
                    ->where('status', 'pending')
                    ->update([
                        'status' => $request->status,
                        'updated_at' => now()
                    ]);
                    if(($request->status == 'approve')){
                        Notifikasi::create([
                            'judul' => "Risalah Ditindak Lanjuti",
                            'judul_document' => $risalah->judul,
                            'id_divisi' => $risalah->divisi_id_divisi,
                            'updated_at' => now()
                        ]);
                    }
                
                    if ($request->status == 'reject') {
                        $risalah->status = 'reject';
                        $risalah->tgl_disahkan = now();
                        $risalah->catatan = $request->catatan ?? $risalah->catatan;
                        $risalah->save();
                        
                        Notifikasi::create([
                            'judul' => "Risalah Tidak Ditindak Lanjuti",
                            'judul_document' => $risalah->judul,
                            'id_divisi' => $risalah->divisi_id_divisi,
                            'updated_at' => now()
                        ]);
                    }
            }
        }

        

        return redirect()->back()->with('success', 'Status risalah berhasil diperbarui.');
    }

    //  menampilkan file yang disimpan dalam database
    public function showFile($id)
    {
        $risalah = Risalah::findOrFail($id);

        if (!$risalah->lampiran) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        $fileContent = base64_decode($risalah->lampiran);
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
    // Fungsi download file
    public function downloadFile($id)
    {
        $risalah = Risalah::findOrFail($id);

        if (!$risalah->lampiran) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $fileData = base64_decode($risalah->lampiran);
        $mimeType = finfo_buffer(finfo_open(), $fileData, FILEINFO_MIME_TYPE);
        $extension = $this->getExtension($mimeType);

        return response()->streamDownload(function () use ($fileData) {
            echo $fileData;
        }, "risalah_{$id}.$extension", ['Content-Type' => $mimeType]);
    }

    public function updateStatusNotif(Request $request, $id)
    {
        $risalah = Risalah::findOrFail($id);
        $risalah->status = $request->status;
        $risalah->save();
    
        // Simpan notifikasi
        Notifikasi::create([
            'judul' => "Risalah {$request->status}",
            'jenis_document' => 'risalah',
            'id_divisi' => $risalah->divisi_id,
            'dibaca'         => false,
            'updated_at' => now()
        ]);
    
        return redirect()->back()->with('success', 'Status memo berhasil diperbarui.');
    }
    
}