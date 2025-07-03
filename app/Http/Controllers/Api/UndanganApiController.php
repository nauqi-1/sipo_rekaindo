<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Undangan;
use App\Models\Seri;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Arsip;
use App\Models\Notifikasi;
use App\Models\Kirim_Document;
use App\Models\Backup_Document;
use Illuminate\Support\Facades\Log;
use Clegginabox\PDFMerger\PDFMerger;
use Barryvdh\DomPDF\Facade\PDF;

class UndanganApiController extends Controller
{
    // Untuk user biasa berdasarkan divisi
    public function index(Request $request)
    {
        $user = Auth::user();
        $userDivisiId = $user->divisi_id_divisi;
        $userId = $user->id;

        Log::debug("User ID: {$userId}, Divisi ID: {$userDivisiId}");

        $undanganDiarsipkan = Arsip::where('user_id', $userId)->pluck('document_id')->toArray();

        $query = Undangan::with('divisi')
            ->whereNotIn('id_undangan', $undanganDiarsipkan)
            ->where(function ($q) use ($userDivisiId, $userId) {
                $q->where('divisi_id_divisi', $userDivisiId)
                  ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {
                      $query->where('jenis_document', 'undangan')
                            ->where('id_penerima', $userId)
                            ->whereHas('penerima', function ($subQuery) use ($userDivisiId) {
                                $subQuery->where('divisi_id_divisi', $userDivisiId);
                            });
                  });
            });

        // Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
            $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
            });
        }

        $query->orderBy('created_at', $request->get('sort_direction', 'desc'));

        $undangans = $query->get();

        // Transform final_status
        $undangans->transform(function ($undangan) use ($userId, $userDivisiId) {
            if ($undangan->divisi_id_divisi === $userDivisiId) {
                $undangan->final_status = $undangan->status;
            } else {
                $statusKirim = Kirim_Document::where('id_document', $undangan->id_undangan)
                    ->where('jenis_document', 'undangan')
                    ->where('id_penerima', $userId)
                    ->first();
                $undangan->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return $undangan;
        });

        Log::debug('Undangan setelah transform final_status: ' . $undangans->pluck('final_status'));

        $kirimDocuments = Kirim_Document::where('jenis_document', 'undangan')
        ->whereHas('undangan')
        ->orderBy('id_kirim_document', 'desc')
        ->get();

        Log::debug('Jumlah kirimDocuments: ' . $kirimDocuments->count());

        $kirimDocuments->each(function ($kirim) {
            $pengirim = User::find($kirim->id_pengirim);
            $penerima = User::find($kirim->id_penerima);
            $user = Auth::user();

            $kirim->divisi_pengirim = $pengirim ? $pengirim->divisi->nm_divisi : 'Tidak Diketahui';
            $kirim->divisi_penerima = $penerima ? $penerima->divisi->nm_divisi : 'Tidak Diketahui';
            $kirim->divisi_user = $user->divisi->nm_divisi ?? 'Tidak Diketahui';
        });

        return response()->json([
            'undangans' => $undangans,
            'kirimDocuments' => $kirimDocuments,
        ]);
    }

    // Untuk superadmin
    public function superadmin(Request $request)
    {
        $user = Auth::user();
        if ($user->role->nm_role !== 'superadmin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $undanganDiarsipkan = Arsip::where('user_id', $user->id)->pluck('document_id')->toArray();

        $query = Undangan::with('divisi')
            ->whereNotIn('id_undangan', $undanganDiarsipkan);

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
                  ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
            });
        }

        $query->orderBy('created_at', $request->get('sort_direction', 'desc'));

        $undangans = $query->get();

        return response()->json($undangans);
    }

    // SEKKK IKI
    public function updateDocumentStatus(Request $request, $id)
    {
         
        try {
            $undangan = Undangan::findOrFail($id);

            // Validasi input
            $request->validate([
                'status' => 'required|in:approve,reject,pending',
                'catatan' => 'nullable|string',
            ]);

            // Update status
            $undangan->status = $request->status;

            // Jika status 'approve' atau 'reject', simpan tanggal pengesahan
            if ($request->status == 'approve' || $request->status == 'reject') {
                $undangan->tgl_disahkan = now();
            } else {
                $undangan->tgl_disahkan = null;
            }

            // Simpan catatan jika ada
            $undangan->catatan = $request->catatan;

            // Simpan perubahan
            $undangan->save();

            //tambahan
            Kirim_Document::where('id_document', $undangan->id_undangan)
                ->where('jenis_document', 'undangan')
                // ->where('id_penerima', Auth::id())
                ->update([
                    'status' => $request->status,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Undangan berhasil dikirim.',
                'data' => $undangan
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirimkan undangan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
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
            ]);

            // Hapus data asli
            $undangan->delete();

            return response()->json([
                'message' => 'Undangan deleted successfully.',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete undangan: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function jumlahUndangan(Request $request)
    {
        $user = Auth::user();

        $undanganDiarsipkan = Arsip::where('user_id', $user->id)->pluck('document_id')->toArray();

        $query = Undangan::with('divisi')
            ->whereNotIn('id_undangan', $undanganDiarsipkan);

        // Cek apakah superadmin atau bukan
        if ($user->role->nm_role !== 'superadmin') {
            // Kalau bukan superadmin, batasi query seperti di index
            $userDivisiId = $user->divisi_id_divisi;
            $userId = $user->id;

            $query->where(function ($q) use ($userDivisiId, $userId) {
                $q->where('divisi_id_divisi', $userDivisiId)
                ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {
                    $query->where('jenis_document', 'undangan')
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
                ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('divisi_id_divisi') && $user->role->nm_role === 'superadmin') {
            $query->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Hitung jumlah
        $count = $query->count();

        return response()->json([
            'jumlah_undangan' => $count
        ]);
    }

    public function ViewUndanganPDF($id_undangan)
    {
        try {
            // Ambil data undangan
            $undangan = Undangan::findOrFail($id_undangan);

            // Siapkan gambar header dan footer dalam base64
            $headerPath = public_path('img/bheader.png');
            $footerPath = public_path('img/bfooter.png');

            $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
            $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

            // Generate PDF utama dari Blade
            $formatUndanganPdf = PDF::loadView('format-surat.format-undangan', [
                'undangan' => $undangan,
                'headerImage' => $headerBase64,
                'footerImage' => $footerBase64,
                'isPdf' => true
            ])->setPaper('A4', 'portrait');

            $undanganPdfContent = $formatUndanganPdf->output();

            // Jika ada lampiran di database (dalam base64), gabungkan
            if (!empty($undangan->lampiran)) {
                $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;

                // Simpan file PDF utama dan lampiran ke file sementara
                $mainPath = storage_path("app/temp_main_{$undangan->id}.pdf");
                $lampiranPath = storage_path("app/temp_lampiran_{$undangan->id}.pdf");

                file_put_contents($mainPath, $undanganPdfContent);
                file_put_contents($lampiranPath, base64_decode($undangan->lampiran));

                // Merge kedua file
                $outputPath = storage_path("app/temp_merged_{$undangan->id}.pdf");

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
                $base64Pdf = base64_encode($undanganPdfContent);
            }

            return response()->json([
                'success' => true,
                'message' => 'PDF undangan berhasil diambil',
                'undangan_id' => $undangan->id,
                'pdf_base64' => $base64Pdf
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil undangan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
