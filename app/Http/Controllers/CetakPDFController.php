<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;
use Clegginabox\PDFMerger\PDFMerger;
use App\Models\Memo;
use App\Models\Undangan;
use App\Models\Risalah;
use App\Models\Divisi;
use App\Models\Department;
use App\Models\Director;
use App\Models\Section;
use App\Models\Unit;
use App\Models\User;
use FontLib\TrueType\Collection;
use Illuminate\Support\Str;

class CetakPDFController extends Controller
{
    public function cetakmemoPDF($id)
    {
        // Ambil data dari database
        $memo = Memo::findOrFail($id); // Sesuaikan dengan model yang benar

        $tujuanNames = explode(';', $memo->tujuan_string);

        $manager = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$memo->nama_bertandatangan])
            ->first();

        if ($manager) {
            $level = $this->detectLevel($manager);
            $manager->level_kerja = $level;
            $manager->bagian_text = $this->getBagianText($manager, $level);
        }

        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');
        $qrCode = $memo->qr_approved_by;

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        // Load view yang akan digunakan sebagai template PDF
        // $pdf = PDF::loadView('format-surat.format-undangan', compact('undangan'));

        // Set ukuran kertas (opsional)
        // $pdf->setPaper('A4', 'portrait');
        $formatMemoPdf = PDF::loadView('format-surat.format-memo', [
            'memo' => $memo,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'tujuanNames' => $tujuanNames,
            'manager' => $manager,
            'qrCode' => $qrCode,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Return PDF untuk didownload
        $formatMemoPath = storage_path('app/temp_format_memo_' . $memo->id . '.pdf');
        $formatMemoPdf->save($formatMemoPath);

        // Jika ada lampiran, gabungkan PDF-nya
        if (!empty($memo->lampiran)) {
            $lampiranTempPath = storage_path('app/temp_lampiran_' . $memo->id . '.pdf');
            file_put_contents($lampiranTempPath, base64_decode($memo->lampiran));

            $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
            $pdfMerger->addPDF($formatMemoPath, 'all');
            $pdfMerger->addPDF($lampiranTempPath, 'all');

            $outputPath = storage_path('app/view_memo_' . $memo->id . '.pdf');
            $pdfMerger->merge('file', $outputPath);

            // Download lalu hapus semua file sementara
            if (file_exists($formatMemoPath)) unlink($formatMemoPath);
            if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);
            return response()->download($outputPath)->deleteFileAfterSend(true);
        } else {
            $fileName = Str::slug($memo->judul) . '-' . $memo->id . '.pdf';
            return response()->streamDownload(function () use ($formatMemoPdf, $formatMemoPath) {
                echo $formatMemoPdf->output();
                if (file_exists($formatMemoPath)) unlink($formatMemoPath);
            }, $fileName);
        }
    }

    public function viewmemoPDF($id_memo)

    {
        // Ambil data memo berdasarkan ID
        $memo = Memo::findOrFail($id_memo);

        $tujuanNames = explode(';', $memo->tujuan_string);

        $manager = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$memo->nama_bertandatangan])
            ->first();

        if ($manager) {
            $level = $this->detectLevel($manager);
            $manager->level_kerja = $level;
            $manager->bagian_text = $this->getBagianText($manager, $level);
        }

        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');



        // Konversi gambar ke base64
        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        // Generate PDF halaman pertama (format memo)
        $formatMemoPdf = PDF::loadView('format-surat.format-memo', [
            'memo' => $memo,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'tujuanNames' => $tujuanNames,
            'manager' => $manager,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Simpan PDF memo sementara
        $formatMemoPath = storage_path('app/temp_format_memo_' . $memo->id . '.pdf');
        $formatMemoPdf->save($formatMemoPath);

        // Jika ada lampiran, gabungkan PDF-nya
        if (!empty($memo->lampiran)) {
            $lampiranTempPath = storage_path('app/temp_lampiran_' . $memo->id . '.pdf');
            file_put_contents($lampiranTempPath, base64_decode($memo->lampiran));

            $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
            $pdfMerger->addPDF($formatMemoPath, 'all');
            $pdfMerger->addPDF($lampiranTempPath, 'all');

            $outputPath = storage_path('app/view_memo_' . $memo->id . '.pdf');
            $pdfMerger->merge('file', $outputPath);

            // Hapus file sementara setelah digabung
            if (file_exists($formatMemoPath)) unlink($formatMemoPath);
            if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);

            // Tampilkan file hasil merge
            return response()->file($outputPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
        } else {
            // Kalau tidak ada lampiran, tampilkan risalah langsung
            return response()->file($formatMemoPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
        }
    }
    // Tambahkan method helper untuk sanitize filename
    private function sanitizeFileName($filename, $maxLength = 80)
    {
        // Remove HTML tags dan decode entities
        $filename = html_entity_decode(strip_tags($filename), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Replace karakter yang tidak diizinkan di nama file dengan underscore
        // Karakter yang tidak diizinkan: \ / : * ? " < > |
        $filename = preg_replace('/[\\\\\/:\*\?"<>\|]/', '_', $filename);

        // Replace multiple spaces dengan single space
        $filename = preg_replace('/\s+/', ' ', $filename);

        // Trim whitespace
        $filename = trim($filename);

        // Limit panjang filename
        if (strlen($filename) > $maxLength) {
            $filename = substr($filename, 0, $maxLength);
        }

        // Jika kosong, berikan nama default
        return $filename ?: 'undangan';
    }
    public function cetakundanganPDF($id)
    {
        // Ambil data dari database
        $undangan = Undangan::findOrFail($id); // Sesuaikan dengan model yang benar
        // $path = public_path('img/border-surat.png');
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');
        $qrCode = $undangan->qr_approved_by;

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        $tujuanIds = explode(';', $undangan->tujuan);  // [id_user1;id_user2;...]
        $tujuanUsers = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereIn('id', $tujuanIds)
            ->get()
            ->map(function ($user) {
                $level = $this->detectLevel($user);
                $user->level_kerja = $level;
                $user->bagian_text = $this->getBagianText($user, $level);
                return $user;
            })
            ->sortBy(function ($user) {
                return optional($user->position)->id_position; // urutkan by ID posisi
            })
            ->values(); // reset index array

        $manager = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$undangan->nama_bertandatangan])
            ->first();

        if ($manager) {
            $level = $this->detectLevel($manager);
            $manager->level_kerja = $level;
            $manager->bagian_text = $this->getBagianText($manager, $level);
        }

        $cleanTag = html_entity_decode(strip_tags($undangan->isi_undangan), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $formatUndanganPdf = PDF::loadView('format-surat.format-undangan', [
            'undangan' => $undangan,
            'tujuanUsers' => $tujuanUsers,
            'cleanTag' => $cleanTag,
            'manager' => $manager,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Return PDF untuk didownload
        $formatUndanganPath = storage_path('app/temp_format_undangan_' . $undangan->id . '.pdf');
        $formatUndanganPdf->save($formatUndanganPath);

        // Jika ada lampiran, gabungkan PDF-nya
        if (!empty($undangan->lampiran)) {
            $lampiranTempPath = storage_path('app/temp_lampiran_' . $undangan->id . '.pdf');
            file_put_contents($lampiranTempPath, base64_decode($undangan->lampiran));

            $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
            $pdfMerger->addPDF($formatUndanganPath, 'all');
            $pdfMerger->addPDF($lampiranTempPath, 'all');

            // BAGIAN YANG DIPERBAIKI UNTUK NAMA FILE
            $fileName = $this->sanitizeFileName($undangan->judul) . ' - ' . $undangan->id . '.pdf';
            $outputPath = storage_path('app/' . $fileName);
            $pdfMerger->merge('file', $outputPath);

            // Download lalu hapus semua file sementara
            if (file_exists($formatUndanganPath)) unlink($formatUndanganPath);
            if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);
            return response()->download($outputPath, $fileName)->deleteFileAfterSend(true);
        } else {
            // Jika tidak ada lampiran, langsung download PDF undangan saja
            // BAGIAN YANG DIPERBAIKI UNTUK NAMA FILE
            $fileName = $this->sanitizeFileName($undangan->judul) . ' - ' . $undangan->id . '.pdf';

            return response()->streamDownload(function () use ($formatUndanganPdf, $formatUndanganPath) {
                echo $formatUndanganPdf->output();

                if (file_exists($formatUndanganPath)) unlink($formatUndanganPath);
            }, $fileName);
        }
    }

    public function detectLevel($user)
    {
        if (!empty($user->unit_id_unit)) return 'unit';
        if (!empty($user->section_id_section)) return 'section';
        if (!empty($user->department_id_department)) return 'department';
        if (!empty($user->divisi_id_divisi)) return 'divisi';
        if (!empty($user->director_id_director)) return 'director';
        return null;
    }

    public function getBagianText($user, $level)
    {
        switch ($level) {
            case 'unit':
                return optional($user->unit)->name_unit;
            case 'section':
                return optional($user->section)->name_section;
            case 'department':
                return optional($user->department)->name_department;
            case 'divisi':
                return optional($user->divisi)->nm_divisi; // khusus nm_divisi
            case 'director':
                return optional($user->director)->name_director;
            default:
                return '-';
        }
    }
    public function viewundanganPDF($id_undangan)
    {
        $undangan = Undangan::findOrFail($id_undangan);
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        $tujuanIds = explode(';', $undangan->tujuan);  // [id_user1;id_user2;...]
        $tujuanUsers = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereIn('id', $tujuanIds)
            ->get()
            ->map(function ($user) {
                $level = $this->detectLevel($user);
                $user->level_kerja = $level;
                $user->bagian_text = $this->getBagianText($user, $level);

                // Format position name - remove parentheses and create abbreviations
                if (isset($user->position->nm_position)) {
                    $rawPosition = $user->position->nm_position;

                    // Remove parentheses and content inside them, then clean up extra spaces
                    $formattedPosition = preg_replace('/\s*\([^)]*\)\s*/', ' ', $rawPosition);
                    $formattedPosition = trim(preg_replace('/\s+/', ' ', $formattedPosition));

                    // Create abbreviations for common positions
                    if (!in_array($formattedPosition, ['Staff', 'Direktur'])) {
                        $abbreviations = [
                            'Penanggung Jawab Senior Manager' => 'PJ SM',
                            'Penanggung Jawab Manager' => 'PJ M',
                            'Penanggung Jawab Supervisor' => 'PJ SPV',
                            'Senior Manager' => 'SM',
                            'General Manager' => 'GM',
                            'Manager' => 'M',
                            'Supervisor' => 'SPV'
                        ];

                        foreach ($abbreviations as $full => $abbrev) {
                            if (strpos($formattedPosition, $full) !== false) {
                                $formattedPosition = str_replace($full, $abbrev, $formattedPosition);
                                break;
                            }
                        }
                    }

                    // Update the position name with formatted version
                    $user->position->nm_position = $formattedPosition;
                }

                return $user;
            })
            ->sortBy(function ($user) {
                return optional($user->position)->id_position; // urutkan by ID posisi
            })
            ->values(); // reset index array

        $manager = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$undangan->nama_bertandatangan])
            ->first();

        if ($manager) {
            $level = $this->detectLevel($manager);
            $manager->level_kerja = $level;
            $manager->bagian_text = $this->getBagianText($manager, $level);
        }

        $cleanTag = html_entity_decode(strip_tags($undangan->isi_undangan), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $formatUndanganPdf = PDF::loadView('format-surat.format-undangan', [
            'undangan' => $undangan,
            'tujuanUsers' => $tujuanUsers,
            'cleanTag' => $cleanTag,
            'manager' => $manager,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Simpan PDF memo sementara
        $formatUndanganPath = storage_path('app/temp_format_undangan_' . $undangan->id . '.pdf');
        $formatUndanganPdf->save($formatUndanganPath);

        // Jika ada lampiran, gabungkan PDF-nya
        if (!empty($undangan->lampiran)) {
            $lampiranTempPath = storage_path('app/temp_lampiran_' . $undangan->id . '.pdf');
            file_put_contents($lampiranTempPath, base64_decode($undangan->lampiran));

            $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
            $pdfMerger->addPDF($formatUndanganPath, 'all');
            $pdfMerger->addPDF($lampiranTempPath, 'all');

            $outputPath = storage_path('app/view_undangan_' . $undangan->id . '.pdf');
            $pdfMerger->merge('file', $outputPath);

            // Hapus file sementara setelah digabung
            if (file_exists($formatUndanganPath)) unlink($formatUndanganPath);
            if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);

            // Tampilkan file hasil merge
            return response()->file($outputPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
        } else {
            // Kalau tidak ada lampiran, tampilkan risalah langsung
            return response()->file($formatUndanganPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
        }
    }

    public function getGMFromKode($kode)
    {
        $divisi = Divisi::where('kode_divisi', $kode)->first();
        $users = collect();

        if ($divisi) {
            $users = User::where('divisi_id_divisi', $divisi->id_divisi)->get();
        } else {
            $department = Department::where('kode_department', $kode)->first();
            if ($department) {
                $users = User::where('department_id_department', $department->id_department)->get();
            } else {
                $director = Director::where('kode_director', $kode)->first();
                if ($director) {
                    $users = User::where('director_id_director', $director->id_director)->get();
                } else {
                    return response()->json(['error' => 'Kode tidak valid'], 404);
                }
            }
        }

        for ($i = 1; $i <= 9; $i++) {
            $user = $users->firstWhere('position_id_position', $i);
            if ($user) {
                return $user;
            }
        }

        return null;
    }
    public function laporanmemoPDF(Request $request)
    {
        $memos = Memo::query();
        $memoController = new MemoController();
        $kodeUser = null;
        // Filter berdasarkan pencarian judul jika ada
        if ($request->filled('search')) {
            $memos->where('judul', 'like', '%' . $request->search . '%');
        }

        $kodeUser = null;
        if (Auth::user()->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }

        if (!$kodeUser && $request->filled('kode') && $request->kode != 'pilih') {
            $kodeUser = $request->kode;
        }

        if ($kodeUser) {
            $memos->where(function ($query) use ($kodeUser) {
                $query->where('kode', $kodeUser);
            });
        }

        if ($kodeUser) {
            $manager = $this->getGMFromKode($kodeUser);
        } else {
            $manager = null;
        }

        $memos->whereDate('tgl_dibuat', '>=', $request->tgl_awal)
            ->whereDate('tgl_dibuat', '<=', $request->tgl_akhir);

        // Ambil semua data yang sudah difilter
        $memos = $memos->orderBy('tgl_dibuat', 'asc')->get();

        // Ambil path gambar header dan footer
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        // Generate PDF dari view
        $pdf = PDF::loadView('format-surat.format-cetakLaporan-memo', [
            'memos' => $memos,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'manager' => $manager,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Tampilkan PDF langsung di browser
        return $pdf->stream('laporan-undangan.pdf');
    }

    public function laporanundanganPDF(Request $request)
    {
        // Ambil data divisi
        $undangans = Undangan::query();
        $memoController = new MemoController();

        if ($request->filled('search')) {
            $undangans->where('judul', 'like', '%' . $request->search . '%');
        }

        $kodeUser = null;
        if (Auth::user()->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }

        if (!$kodeUser && $request->filled('kode') && $request->kode != 'pilih') {
            $kodeUser = $request->kode;
        }

        if ($kodeUser) {
            $undangans->where(function ($query) use ($kodeUser) {
                $query->where('kode', $kodeUser);
            });
        }

        if ($kodeUser) {
            $manager = $this->getGMFromKode($kodeUser);
        } else {
            $manager = null;
        }

        $undangans->whereDate('tgl_dibuat', '>=', $request->tgl_awal)
            ->whereDate('tgl_dibuat', '<=', $request->tgl_akhir);
        // Ambil semua data yang sudah difilter
        $undangans = $undangans->orderBy('tgl_dibuat', 'asc')->get();


        // Ambil path gambar header dan footer
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        // Generate PDF dari view
        $pdf = PDF::loadView('format-surat.format-cetakLaporan-undangan', [
            'undangans' => $undangans,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'manager' => $manager,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Tampilkan PDF langsung di browser
        return $pdf->stream('laporan-undangan.pdf');
    }


    public function cetakrisalahPDF($id)
    {
        $risalah = Risalah::findOrFail($id);

        // QRCode jika ada
        $qrCode = $risalah->qr_approved_by;

        // User bertandatangan
        $userBertandatangan = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$risalah->nama_bertandatangan])
            ->first();

        if ($userBertandatangan) {
            $level = $this->detectLevel($userBertandatangan);
            $userBertandatangan->level_kerja = $level;
            $userBertandatangan->bagian_text = $this->getBagianText($userBertandatangan, $level);
        }

        $cleanIsi = strip_tags($risalah->isi_risalah);

        // mPDF debug header
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_top' => 50,
            'margin_bottom' => 30,
        ]);

        // CSS
        $stylesheet = file_get_contents(public_path('css/format-surat/format-cetakLaporan.css'));
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        // Set header/footer dengan text HTML biasa
        $mpdf->SetHTMLHeader('<div style="width:100%;text-align:center;font-size:18px;padding:10px 0;border-bottom:2px solid #333;background:#ffe;">HEADER DEBUG PDF - CETAK RISALAH</div>');
        $mpdf->SetHTMLFooter('<div style="width:100%;text-align:center;font-size:14px;padding:8px 0;border-top:2px solid #333;background:#eef;">FOOTER DEBUG PDF - CETAK RISALAH</div>');

        // Render Blade (mode PDF)
        $html = view('format-surat.format-risalah', [
            'risalah' => $risalah,
            'cleanIsi' => $cleanIsi,
            'manager' => $userBertandatangan,
            'qrCode' => $qrCode,
            'isPdf' => true
        ])->render();

        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
    }

    public function viewrisalahPDF($id_risalah)
    {
        $risalah = Risalah::findOrFail($id_risalah);
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        // Ambil user yang bertandatangan
        $userBertandatangan = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$risalah->nama_bertandatangan])
            ->first();

        if ($userBertandatangan) {
            $level = $this->detectLevel($userBertandatangan);
            $userBertandatangan->level_kerja = $level;
            $userBertandatangan->bagian_text = $this->getBagianText($userBertandatangan, $level);
        }

        $cleanIsi = strip_tags($risalah->isi_risalah);

        $formatRisalahPdf = PDF::loadView('format-surat.format-risalah', [
            'risalah' => $risalah,
            'cleanIsi' => $cleanIsi,
            'manager' => $userBertandatangan,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Simpan PDF risalah sementara
        $formatRisalahPath = storage_path('app/temp_format_risalah_' . $risalah->id . '.pdf');
        $formatRisalahPdf->save($formatRisalahPath);

        // Jika ada lampiran, gabungkan PDF-nya
        if (!empty($risalah->lampiran)) {
            $lampiranTempPath = storage_path('app/temp_lampiran_' . $risalah->id . '.pdf');
            file_put_contents($lampiranTempPath, base64_decode($risalah->lampiran));

            $pdfMerger = new \Clegginabox\PDFMerger\PDFMerger;
            $pdfMerger->addPDF($formatRisalahPath, 'all');
            $pdfMerger->addPDF($lampiranTempPath, 'all');

            $outputPath = storage_path('app/view_risalah_' . $risalah->id . '.pdf');
            $pdfMerger->merge('file', $outputPath);

            // Hapus file sementara setelah digabung
            if (file_exists($formatRisalahPath)) unlink($formatRisalahPath);
            if (file_exists($lampiranTempPath)) unlink($lampiranTempPath);

            return response()->file($outputPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
        } else {
            return response()->file($formatRisalahPath, ['Content-Type' => 'application/pdf'])->deleteFileAfterSend(true);
        }
    }


    public function laporanrisalahPDF(Request $request)
    {
        // Ambil data divisi
        $risalahs = Risalah::query();
        $memoController = new MemoController();
        $kodeUser = null;

        // Filter berdasarkan pencarian judul jika ada
        if ($request->filled('search')) {
            $risalahs->where('judul', 'like', '%' . $request->search . '%');
        }

        if (Auth::user()->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }

        if (!$kodeUser && $request->filled('kode') && $request->kode != 'pilih') {
            $kodeUser = $request->kode;
        }

        if ($kodeUser) {
            $risalahs->where(function ($query) use ($kodeUser) {
                $query->where('kode', $kodeUser);
            });
        }

        if ($kodeUser) {
            $manager = $this->getGMFromKode($kodeUser);
        } else {
            $manager = null;
        }

        $risalahs->whereDate('tgl_dibuat', '>=', $request->tgl_awal)
            ->whereDate('tgl_dibuat', '<=', $request->tgl_akhir);
        // Ambil semua data yang sudah difilter
        $risalahs = $risalahs->orderBy('tgl_dibuat', 'desc')->get();

        // Ambil path gambar header dan footer
        $headerPath = public_path('img/bheader.png');
        $footerPath = public_path('img/bfooter.png');

        $headerBase64 = file_exists($headerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerPath)) : null;
        $footerBase64 = file_exists($footerPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerPath)) : null;

        // Generate PDF dari view
        $pdf = PDF::loadView('format-surat.format-cetakLaporan-risalah', [
            'risalahs' => $risalahs,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'headerImage' => $headerBase64,
            'footerImage' => $footerBase64,
            'manager' => $manager,
            'isPdf' => true
        ])->setPaper('A4', 'portrait');

        // Tampilkan PDF langsung di browser
        return $pdf->stream('laporan-risalah.pdf');
    }
}
