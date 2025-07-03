<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Arsip;
use App\Models\Memo;
use App\Models\Kirim_document;
use Illuminate\Http\Request;
use App\Models\Seri;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Notifikasi;
use App\Models\Backup_Document;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Validator;
use Clegginabox\PDFMerger\PDFMerger;
use Barryvdh\DomPDF\Facade\PDF;

class MemoApiController extends Controller
{
    // Untuk user biasa berdasarkan divisi
    public function index(Request $request) 
    {
        $user = Auth::user();   //ambil data user saat sedang login
        $userDivisiId = $user->divisi_id_divisi;    //ambil id divisi
        $userId = $user->id;    //ambil id user

        $memoDiarsipkan = Arsip::where('user_id', $userId)->pluck('document_id')->toArray();    //ambil id memo yang sudah diarsipkan

        $query = Memo::with('divisi')   //ambil data memo beserta divisi
            ->whereNotIn('id_memo', $memoDiarsipkan)    //ambil data memo yang belum diarsipkan
            ->where(function ($q) use ($userDivisiId, $userId) {
                $q->where('divisi_id_divisi', $userDivisiId)    //ambil data memo yang sesuai dengan id divisi
                  ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {    //ambil data memo yang dikirim ke user
                      $query->where('jenis_document', 'memo')   //ambil data memo yang dikirim
                            ->where('id_penerima', $userId) //ambil id penerima
                            ->whereHas('penerima', function ($subQuery) use ($userDivisiId) {   //ambil data memo yang sesuai dengan id divisi
                                $subQuery->where('divisi_id_divisi', $userDivisiId);    //ambil id divisi
                            });
                  });
            });

        // Filtering
        if ($request->filled('status')) {   //ambil data memo berdasarkan status
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {  //
            $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
            });
        }

        $query->orderBy('created_at', $request->get('sort_direction', 'desc')); // Urutkan berdasarkan tanggal dibuat

        $memos = $query->get(); // Ambil data memo

        // final_status
        $memos->transform(function ($memo) use ($userId, $userDivisiId) {   //
            if ($memo->divisi_id_divisi === $userDivisiId) {
                $memo->final_status = $memo->status;
            } else {
                $statusKirim = Kirim_Document::where('id_document', $memo->id_memo)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $userId)
                    ->first();
                $memo->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return $memo;
            //memo dari divisi ambil dari memo.status
            //memo dikirim ke user ambil dari kirim_document.status
        });

        return response()->json($memos);
    }

    // Untuk superadmin
    public function superadmin(Request $request)    //ambil data memo untuk superadmin
    {
        $user = Auth::user();   //ambil data user saat sedang login
        if ($user->role->nm_role !== 'superadmin') {    //cek apakah user adalah superadmin
            return response()->json(['message' => 'Unauthorized'], 403);    // jika bukan superadmin, tampilkan pesan unauthorized
        }

        $memoDiarsipkan = Arsip::where('user_id', $user->id)->pluck('document_id')->toArray();  

        $query = Memo::with('divisi')
            ->whereNotIn('id_memo', $memoDiarsipkan);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
            $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        }

        if ($request->filled('divisi_id_divisi')) {
            $query->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
            });
        }

        $query->orderBy('created_at', $request->get('sort_direction', 'desc'));

        $memos = $query->get();

        return response()->json($memos);

        //untuk status langsung ambil dari memo.status
    }
    
    public function manager(Request $request)
    {
        $user = Auth::user();

        if ($user->role->nm_role !== 'manager') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $kirimDocuments = Kirim_Document::where('id_penerima', $user->id)
            ->where('jenis_document', 'memo')
            ->with('document.divisi') // include relasi ke memo dan divisi
            ->get();

        $memoDiterima = [];
        $memoTerkirim = [];

        foreach ($kirimDocuments as $kirim) {
            //Status selalu diambil dari kirim_documents.status karena manager adalah penerima dokumen.

            $memo = $kirim->document;

            if (!$memo) {
                continue;
            }

            // Buat status virtual: jika manager belum memutuskan, maka status ditampilkan sebagai "pending"
            $virtualStatus = $kirim->status; // ini status dari kirim_documents

            $memoData = [
                'id_memo' => $memo->id_memo,
                'judul' => $memo->judul,
                'tgl_dibuat' => $memo->tgl_dibuat,
                'tgl_disahkan' => $memo->tgl_disahkan,
                'status' => $memo->status,
                'final_status' => $kirim->status,
                'divisi' => $memo->divisi,
                'nomor_memo' => $memo->nomor_memo,
                'seri_surat' => $memo->seri_surat
            ];

            // Kalau status belum di-respond (null/pending), tampilkan sebagai pending
            if (in_array($kirim->status, ['pending', 'Diproses', null])) {
                $virtualStatus = 'pending';
            } else {
                $virtualStatus = $kirim->status; // jika sudah approve/reject dari manager
            }

            if ($virtualStatus === 'pending') {
                $memoDiterima[] = $memoData;
            } else {
                $memoTerkirim[] = $memoData;
            }
        }

        return response()->json([
            'memo_diterima' => $memoDiterima,
            'memo_terkirim' => $memoTerkirim
        ]);
    
        //vitual status: Menentukan memo yang belum ditanggapi oleh manager
        //memisahkan status untuk memo diterima dan terkirim
    }

    public function destroy($id)
    {
        try {
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
                'nama_bertandatangan'=> $memo->nama_bertandatangan,
                'lampiran' => $memo->lampiran,
                'pembuat' => $memo->pembuat,
                'seri_document' => $memo->seri_surat,
                'divisi_id_divisi' => $memo->divisi_id_divisi,
                'created_at' => $memo->created_at,
                'updated_at' => $memo->updated_at,
            ]);

            // Hapus data asli
            $memo->delete();

            return response()->json([
                'message' => 'Memo deleted successfully.',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete memo: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
{
    try {
        $memo = Memo::findOrFail($id);
        $userDivisiId = Auth::user()->divisi_id_divisi;
        $userId = Auth::id();

        // Validasi input
        $request->validate([
            'status' => 'required|in:approve,reject,pending',
            'catatan' => 'nullable|string',
        ]);

        if ($userDivisiId == $memo->divisi_id_divisi) {
            $memo->status = $request->status;

            $currentKirim = Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'memo')
                ->where('id_penerima', $userId)
                ->first();

            if ($currentKirim) {
                $currentKirim->status = $request->status;
                $currentKirim->updated_at = now();
                $currentKirim->save();

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
                } else {
                    $memo->tgl_disahkan = null;
                }

                $memo->catatan = $request->catatan;
                $memo->save();
            }
        } else {
            // Jika dari divisi lain
            $currentKirim = Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'memo')
                ->where('id_penerima', $userId)
                ->first();

            if ($currentKirim) {
                $currentKirim->status = $request->status;
                $currentKirim->updated_at = now();
                $currentKirim->save();

                Kirim_document::where('id_document', $id)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $currentKirim->id_pengirim)
                    ->where('status', 'pending')
                    ->update([
                        'status' => $request->status,
                        'updated_at' => now()
                    ]);

                if ($request->status == 'approve') {
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

        return response()->json([
            'status' => true,
            'message' => 'Status memo berhasil diperbarui.',
            'data' => $memo
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}


    public function getMemoByAdmin()
    {
        $memo = Memo::with(['divisi', 'kirimDocument' => function($q){
            $q->latest();
        }])->get();

        $data = $memo->map(function ($item) {
            return [
                'id_memo' => $item->id_memo,
                'judul' => $item->judul,
                'tgl_dibuat' => $item->tgl_dibuat,
                'tgl_disahkan' => $item->tgl_disahkan,
                'status' => $item->status,
                'final_status' => optional($item->kirimDocument->first())->status ?? null, 
                'divisi' => $item->divisi,
                'nomor_memo' => $item->nomor_memo,
                'seri_surat' => $item->seri_surat
            ];
        });

        return response()->json($data);
    }

    //tambahan
    public function show($id)
    {
        try {
            $user = Auth::user();
            $userId = $user->id;
            $userDivisiId = $user->divisi_id_divisi;

            $memo = Memo::with('divisi')->findOrFail($id);

            // Cek apakah user adalah penerima memo melalui tabel kirim_document
            $kirimDocument = Kirim_Document::where('id_document', $memo->id_memo)
                ->where('jenis_document', 'memo')
                ->where('id_penerima', $userId)
                ->first();

            // Tentukan status final yang ditampilkan untuk user saat ini
            $finalStatus = $memo->divisi_id_divisi === $userDivisiId
                ? $memo->status
                : ($kirimDocument ? $kirimDocument->status : '-');

            return response()->json([
                'success' => true,
                'message' => 'Detail memo ditemukan',
                'data' => [
                    'id_memo' => $memo->id_memo,
                    'judul' => $memo->judul,
                    'isi_memo' => $memo->isi_memo,
                    'tgl_dibuat' => $memo->tgl_dibuat,
                    'tgl_disahkan' => $memo->tgl_disahkan,
                    'catatan' => $memo->catatan,
                    'pembuat' => $memo->pembuat,
                    'status' => $memo->status, // status asli dari memo
                    'final_status' => $finalStatus, // status sesuai yang dilihat oleh user
                    'divisi' => $memo->divisi,
                    'nomor_memo' => $memo->nomor_memo,
                    'seri_surat' => $memo->seri_surat,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail memo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function jumlahMemo(Request $request)
    {
        $user = Auth::user();

        $memoDiarsipkan = Arsip::where('user_id', $user->id)->pluck('document_id')->toArray();

        $query = Memo::with('divisi')
            ->whereNotIn('id_memo', $memoDiarsipkan);

        // Cek apakah superadmin atau bukan
        if ($user->role->nm_role !== 'superadmin') {
            // Kalau bukan superadmin, batasi query seperti di index
            $userDivisiId = $user->divisi_id_divisi;
            $userId = $user->id;

            $query->where(function ($q) use ($userDivisiId, $userId) {
                $q->where('divisi_id_divisi', $userDivisiId)
                ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {
                    $query->where('jenis_document', 'memo')
                            ->where('id_penerima', $userId)
                            ->whereHas('penerima', function ($subQuery) use ($userDivisiId) {
                                $subQuery->where('divisi_id_divisi', $userDivisiId);
                            });
                });
            });
        }

        // Filter tambahan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
            $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('divisi_id_divisi') && $user->role->nm_role === 'superadmin') {
            $query->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Hitung jumlah
        $count = $query->count();

        return response()->json([
            'jumlah_memo' => $count
        ]);
    }

    public function ViewMemoPDF($id_memo)
    {
        try {
            // Ambil data undangan
            $memo = Memo::findOrFail($id_memo);

            // Siapkan gambar header dan footer dalam base64
            $headerPath = public_path('img/bheader.png');
            $footerPath = public_path('img/bfooter.png');

            $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
            $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

            // Generate PDF utama dari Blade
            $formatMemoPdf = PDF::loadView('format-surat.format-memo', [
                'memo' => $memo,
                'headerImage' => $headerBase64,
                'footerImage' => $footerBase64,
                'isPdf' => true
            ])->setPaper('A4', 'portrait');

            $memoPdfContent = $formatMemoPdf->output();

            // Jika ada lampiran di database (dalam base64), gabungkan
            if (!empty($memo->lampiran)) {
                $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;

                // Simpan file PDF utama dan lampiran ke file sementara
                $mainPath = storage_path("app/temp_main_{$memo->id}.pdf");
                $lampiranPath = storage_path("app/temp_lampiran_{$memo->id}.pdf");

                file_put_contents($mainPath, $memoPdfContent);
                file_put_contents($lampiranPath, base64_decode($memo->lampiran));

                // Merge kedua file
                $outputPath = storage_path("app/temp_merged_{$memo->id}.pdf");

                $pdfMerger->addPDF($mainPath, 'all');
                $pdfMerger->addPDF($lampiranPath, 'all');
                $pdfMerger->merge('file', $outputPath);

                // Ambil isi PDF hasil merge
                $finalPdf = file_get_contents($outputPath);
                $base64Pdf = base64_encode($finalPdf);

                // Hapus file sementara
                @unlink($mainPath);
                @unlink($lampiranPath);
                @unlink($outputPath);
            } else {
                // Kalau tidak ada lampiran, kirim PDF utama saja
                $base64Pdf = base64_encode($memoPdfContent);
            }

            return response()->json([
                'success' => true,
                'message' => 'PDF memo berhasil diambil',
                'memo_id' => $memo->id,
                'pdf_base64' => $base64Pdf
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil memo: ' . $e->getMessage(),
            ], 500);
        }
    }
}