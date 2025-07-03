<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Clegginabox\PDFMerger\PDFMerger;
use Barryvdh\DomPDF\Facade\PDF;
use App\Models\Risalah;
use App\Models\RisalahDetail;
use App\Models\Seri;
use App\Models\Arsip;
use App\Models\Notifikasi;
use App\Models\Kirim_Document;
use App\Models\BackupRisalah;
use App\Models\User;
use App\Models\Divisi;

class RisalahApiController extends Controller
{
   public function index(Request $request)
{
    $user = Auth::user();
    $userDivisiId = $user->divisi_id_divisi;
    $userId = $user->id;

    $risalahDiarsipkan = Arsip::where('user_id', $userId)->pluck('document_id')->toArray();

    $query = Risalah::with('divisi', 'risalahDetails')
        ->whereNotIn('id_risalah', $risalahDiarsipkan)
        ->where(function ($q) use ($userDivisiId, $userId) {
            $q->where('divisi_id_divisi', $userDivisiId)
              ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {
                  $query->where('jenis_document', 'risalah')
                        ->where('id_penerima', $userId)
                        ->where('status', 'pending') // Tambahan penting
                        ->whereHas('penerima', function ($subQuery) use ($userDivisiId) {
                            $subQuery->where('divisi_id_divisi', $userDivisiId);
                        });
              });
        });

    // Filtering tambahan
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
        $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
    }

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('judul', 'like', '%' . $request->search . '%')
              ->orWhere('nomor_risalah', 'like', '%' . $request->search . '%');
        });
    }

    $query->orderBy('created_at', $request->get('sort_direction', 'desc'));

    $count = $query->count();
    $risalahs = $query->get();

    // Tambahkan final_status
    $risalahs->transform(function ($risalah) use ($userId, $userDivisiId) {
        if ($risalah->divisi_id_divisi === $userDivisiId) {
            $risalah->final_status = $risalah->status;
        } else {
            $statusKirim = Kirim_Document::where('id_document', $risalah->id_risalah)
                ->where('jenis_document', 'risalah')
                ->where('id_penerima', $userId)
                ->first();
            $risalah->final_status = $statusKirim ? $statusKirim->status : '-';
        }
        return $risalah;
    });

    return response()->json($risalahs);
}

    public function superadmin(Request $request)
    { $user = Auth::user();
        if ($user->role->nm_role !== 'superadmin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $risalahDiarsipkan = Arsip::where('user_id', $user->id)->pluck('document_id')->toArray();

        $query = Risalah::with('divisi', 'risalahDetails')
            ->whereNotIn('id_risalah', $risalahDiarsipkan);

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
                  ->orWhere('nomor_risalah', 'like', '%' . $request->search . '%');
            });
        }

        $query->orderBy('created_at', $request->get('sort_direction', 'desc'));

        $count = $query->count();
        $risalahs = $query->get();

        return response()->json($risalahs);
    }
    public function updateStatus(Request $request, $id)
{
    try {
        $risalah = Risalah::findOrFail($id);
        $user = Auth::user(); // Pastikan menggunakan middleware auth
        $userId = $user->id;
        $userDivisiId = $user->divisi_id_divisi;

        $request->validate([
            'status' => 'required|in:approve,reject,pending',
            'catatan' => 'nullable|string',
        ]);

        if ($userDivisiId == $risalah->divisi_id_divisi) {
            // Update status langsung
            $risalah->status = $request->status;

            if ($request->status === 'approve') {
                $risalah->tgl_disahkan = now();

                $qrText = "Disetujui oleh: {$user->firstname} {$user->lastname}\nTanggal: " . now()->translatedFormat('l, d F Y');
                $qrImage = QrCode::format('svg')->generate($qrText);
                $risalah->qr_approved_by = base64_encode($qrImage);

                Notifikasi::create([
                    'judul' => "Risalah Disetujui",
                    'judul_document' => $risalah->judul,
                    'id_divisi' => $risalah->divisi_id_divisi,
                    'updated_at' => now()
                ]);
            } elseif ($request->status === 'reject') {
                $risalah->tgl_disahkan = now();

                Notifikasi::create([
                    'judul' => "Risalah Ditolak",
                    'judul_document' => $risalah->judul,
                    'id_divisi' => $risalah->divisi_id_divisi,
                    'updated_at' => now()
                ]);
            } else {
                $risalah->tgl_disahkan = null;
            }

            $risalah->catatan = $request->catatan;
            $risalah->save();

        } else {
            // User bukan dari divisi risalah, update di kirim_document
            $kirim = Kirim_document::where('id_document', $id)
                ->where('jenis_document', 'risalah')
                ->where('id_penerima', $userId)
                ->first();

            if ($kirim) {
                $kirim->status = $request->status;
                $kirim->updated_at = now();
                $kirim->save();

                Kirim_document::where('id_document', $id)
                    ->where('jenis_document', 'risalah')
                    ->where('id_penerima', $kirim->id_pengirim)
                    ->where('status', 'pending')
                    ->update([
                        'status' => $request->status,
                        'updated_at' => now()
                    ]);

                if ($request->status === 'approve') {
                    Notifikasi::create([
                        'judul' => "Risalah Ditindak Lanjuti",
                        'judul_document' => $risalah->judul,
                        'id_divisi' => $risalah->divisi_id_divisi,
                        'updated_at' => now()
                    ]);
                }

                if ($request->status === 'reject') {
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

        return response()->json([
            'success' => true,
            'message' => 'Status risalah berhasil diperbarui.',
            'data' => $risalah
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat memperbarui status.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // DELETE /risalah/{id}
    public function destroy($id)
    {
        $risalah = Risalah::findOrFail($id);

        $risalah->risalahDetails()->delete();
        $risalah->delete();

        return response()->json(['message' => 'Risalah berhasil dihapus']);
    }

    public function jumlahRisalah(Request $request)
    {
        $user = Auth::user();

        $risalahDiarsipkan = Arsip::where('user_id', $user->id)->pluck('document_id')->toArray();

        $query = Risalah::with('divisi')
            ->whereNotIn('id_risalah', $risalahDiarsipkan);

        // Cek apakah superadmin atau bukan
        if ($user->role->nm_role !== 'superadmin') {
            // Kalau bukan superadmin, batasi query seperti di index
            $userDivisiId = $user->divisi_id_divisi;
            $userId = $user->id;

            $query->where(function ($q) use ($userDivisiId, $userId) {
                $q->where('divisi_id_divisi', $userDivisiId)
                ->orWhereHas('kirimDocument', function ($query) use ($userId, $userDivisiId) {
                    $query->where('jenis_document', 'risalah')
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
                ->orWhere('nomor_risalah', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('divisi_id_divisi') && $user->role->nm_role === 'superadmin') {
            $query->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Hitung jumlah
        $count = $query->count();

        return response()->json([
            'jumlah_risalah' => $count
        ]);
    }

public function ViewRisalahPDF($id_risalah)
    {
        try {
            // Ambil data undangan
            $risalah = Risalah::findOrFail($id_risalah);

            // Siapkan gambar header dan footer dalam base64
            $headerPath = public_path('img/bheader.png');
            $footerPath = public_path('img/bfooter.png');

            $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
            $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

            // Generate PDF utama dari Blade
            $formatRisalahPdf = PDF::loadView('format-surat.format-risalah', [
                'risalah' => $risalah,
                'headerImage' => $headerBase64,
                'footerImage' => $footerBase64,
                'isPdf' => true
            ])->setPaper('A4', 'portrait');

            $risalahPdfContent = $formatRisalahPdf->output();

            // Jika ada lampiran di database (dalam base64), gabungkan
            if (!empty($risalah->lampiran)) {
                $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;

                // Simpan file PDF utama dan lampiran ke file sementara
                $mainPath = storage_path("app/temp_main_{$risalah->id}.pdf");
                $lampiranPath = storage_path("app/temp_lampiran_{$risalah->id}.pdf");

                file_put_contents($mainPath, $risalahPdfContent);
                file_put_contents($lampiranPath, base64_decode($risalah->lampiran));

                // Merge kedua file
                $outputPath = storage_path("app/temp_merged_{$risalah->id}.pdf");

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
                $base64Pdf = base64_encode($risalahPdfContent);
            }

            return response()->json([
                'success' => true,
                'message' => 'PDF risalah berhasil diambil',
                'risalah_id' => $risalah->id,
                'pdf_base64' => $base64Pdf
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil risalah: ' . $e->getMessage(),
            ], 500);
        }
    }
}
