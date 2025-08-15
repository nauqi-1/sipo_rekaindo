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
use App\Models\SeriRisalah;
use App\Models\Arsip;
use App\Models\Notifikasi;
use App\Models\Kirim_Document;
use App\Models\BackupRisalah;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Undangan;
use App\Models\Department;
use App\Models\Director;
use App\Http\Controllers\MemoController;

class RisalahController extends Controller
{
    public function index(Request $request)
    {
        // $divisi = Divisi::all();
        $seri = SeriRisalah::all();
        $userId = Auth::id();

        // Ambil ID memo yang sudah diarsipkan oleh user ini
        $risalahDiarsipkan = Arsip::where('user_id', $userId)->pluck('document_id')->toArray();

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_risalah', 'judul'];
        $sortBy = in_array($request->get('sort_by'), $allowedSortColumns) ? $request->get('sort_by') : 'created_at';
        $sortDirection = $request->get('sort_direction', 'desc') === 'desc' ? 'desc' : 'asc';

        // Query awal: risalah belum diarsipkan
        $query = Risalah::query()
            ->whereNotIn('id_risalah', $risalahDiarsipkan)
            ->where(function ($q) use ($userId) {
                // Jika user terlibat dalam kirimDocument jenis risalah
                $q->orWhereHas('kirimDocument', function ($query) use ($userId) {
                    $query->where('jenis_document', 'risalah')
                        ->where(function ($query) use ($userId) {
                            $query->where('id_pengirim', $userId)
                                ->orWhere('id_penerima', $userId);
                        });
                });
            });

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal dibuat
        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
            $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        } elseif ($request->filled('tgl_dibuat_awal')) {
            $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
        } elseif ($request->filled('tgl_dibuat_akhir')) {
            $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
        }

        // Filter search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_risalah', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting & pagination
        $perPage = $request->get('per_page', 10);
        $risalahs = $query->with('kirimDocument')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);

        // Tambah final_status
        $risalahs->getCollection()->transform(function ($risalah) use ($userId) {
            $statusKirim = Kirim_Document::where('id_document', $risalah->id_risalah)
                ->where('jenis_document', 'risalah')
                ->where('id_penerima', $userId)
                ->first();
            $risalah->final_status = $statusKirim ? $statusKirim->status : '-';
            return $risalah;
        });

        // (Opsional) Ambil semua kirimDocuments user ini
        $kirimDocuments = Kirim_Document::where('jenis_document', 'risalah')
            ->where(function ($query) use ($userId) {
                $query->where('id_pengirim', $userId)
                    ->orWhere('id_penerima', $userId);
            })
            ->get();

        return view(
            Auth::user()->role->nm_role . '.risalah.risalah-' . Auth::user()->role->nm_role,
            compact('risalahs', 'seri', 'sortDirection', 'kirimDocuments')
        );
    }

    public function superadmin(Request $request)
    {
        $divisi = Divisi::all();
        $seri = SeriRisalah::all();
        $userId = Auth::id();
        $kode = Risalah::withTrashed()
            ->whereNotNull('kode')
            ->pluck('kode')
            ->filter()
            ->unique()
            ->values();

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
        if ($request->filled('kode') && $request->kode != 'pilih') {
            $query->where('kode', $request->kode);
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

        return view('superadmin.risalah.risalah-superadmin', compact('risalahs', 'divisi', 'seri', 'sortDirection', 'kode'));
    }

    public function create()
    {
        $idUser = Auth::user();
        $user = User::where('id', $idUser->id)->first();

        if ($user->position_id_position == 1) {
            $idDirektur = Director::where('id_director', $user->director_id_director)->first();
            $kodeDirektur = $idDirektur->kode_director;
        } else {
            $kodeDirektur = '';
        }
        // dd($user);
        if ($user->department_id_department != NULL) {
            $divisiName = Department::where('id_department', $user->department_id_department)->first();
            if ($divisiName->kode_department != NULL) {
                $divisiName = $divisiName->kode_department;
            } else if ($divisiName->kode_department == NULL) {
                if ($user->divisi_id_divisi == NULL) {
                    $divisiName = $divisiName->name_department;
                } else {
                    $divisiName = Divisi::where('id_divisi', $user->divisi_id_divisi)->first();
                    if ($divisiName->kode_divisi != NULL) {
                        $divisiName = $divisiName->kode_divisi;
                    } else if ($divisiName->kode_divisi == NULL) {
                        $divisiName = $divisiName->nm_divisi;
                    }
                }
            }
        } else if ($user->divisi_id_divisi != NULL) {
            $divisiName = Divisi::where('id_divisi', $user->divisi_id_divisi)->first();
            if ($divisiName->kode_divisi != NULL) {
                $divisiName = $divisiName->kode_divisi;
            } else if ($divisiName->kode_divisi == NULL) {
                $divisiName = $divisiName->nm_divisi;
            }
        } else if ($user->director_id_director != NULL) {
            $divisiName = Director::where('id_director', $user->director_id_director)->first();
            $divisiName = $divisiName->kode_director;
        }

        $departmentId = $user->department_id_department;
        $divisiId = $user->divisi_id_divisi;

        $undangan = DB::select("
            SELECT *
            FROM undangan
            WHERE status = 'approve'
                AND judul NOT IN (
                    SELECT judul FROM risalah
                )
                AND EXISTS (
                    SELECT 1
                    FROM users
                    WHERE
                        users.id = undangan.pembuat
                        AND (
                            (users.department_id_department IS NOT NULL AND users.department_id_department = ?)
                            OR
                            (users.department_id_department IS NULL AND users.divisi_id_divisi = ?)
                        )
                )
        ", [$departmentId, $divisiId]);

        $risalah = new Risalah(); // atau ambil dari data risalah terakhir, terserah kebutuhanmu

        // Ambil nomor seri berikutnya
        $nextSeri = SeriRisalah::getNextSeri(false);
        // Konversi bulan ke angka Romawi
        $bulanRomawi = $this->convertToRoman(now()->month);

        // Format nomor dokumen sesuai contoh pada gambar
        $nomorDokumen = sprintf(
            "RIS-%d.%d/REKA%s/%s/%s/%d",
            $nextSeri['seri_tahunan'],
            $nextSeri['seri_bulanan'],
            strtoupper($kodeDirektur),
            strtoupper($divisiName),
            $bulanRomawi,
            now()->year
        );

        // $managers = User::all();

        return view(Auth::user()->role->nm_role . '.risalah.add-risalah', [
            'risalah' => $risalah,
            'nomorSeriTahunan' => $nextSeri['seri_tahunan'], // Tambahkan nomor seri tahunan
            'nomorDokumen' => $nomorDokumen,
            // 'managers' => $managers,
            'undangan' => $undangan
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $memoController = new MemoController();
        $request->validate([
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|string',
            'nomor_risalah' => 'required|string',
            'agenda' => 'required|string',
            'tempat' => 'required|string',
            'waktu_mulai' => 'required|string',
            'waktu_selesai' => 'required|string',
            'judul' => 'required|string',
            'nama_bertandatangan' => 'required|string',
            'pembuat' => 'required|string',
            'nomor' => 'nullable|array',
            'topik' => 'nullable|array',
            'pembahasan' => 'nullable|array',
            'tindak_lanjut' => 'nullable|array',
            'target' => 'nullable|array',
            'pic' => 'nullable|array',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tujuan.required' => 'Minimal satu divisi tujuan harus dipilih.',
            'lampiran.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
            'lampiran.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filePath = base64_encode(file_get_contents($file->getRealPath()));
        }
        $divDeptKode = $memoController->getDivDeptKode(Auth::user());
        $divisiId = auth()->user()->divisi_id_divisi;
        $seri = SeriRisalah::getNextSeri(true);
        $seri = SeriRisalah::where('tahun', now()->year)
            ->latest()
            ->first();

        if (!$seri) {
            return back()->with('error', 'Nomor seri tidak ditemukan.');
        }
        // Simpan risalah utama
        $risalah = Risalah::create([
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
            'kode' => $divDeptKode,
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

        // Kirim otomatis ke semua MANAGER dari divisi pembuat (bukan ke tujuan)
        $divisiPembuatId = Auth::user()->divisi_id_divisi;

        $penerima = \App\Models\User::whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$request->nama_bertandatangan])->first();

        if (!$penerima) {
            return back()->withErrors(['nama_bertandatangan' => 'Nama penerima tidak ditemukan.']);
        }

        $sudahDikirim = \App\Models\Kirim_Document::where('id_document', $risalah->id_risalah)
            ->where('jenis_document', 'risalah')
            ->where('id_pengirim', Auth::id())
            ->where('id_penerima', $penerima->id)
            ->exists();

        if (!$sudahDikirim) {
            \App\Models\Kirim_Document::firstOrCreate([
                'id_document' => $risalah->id_risalah,
                'jenis_document' => 'risalah',
                'id_pengirim' => Auth::id(),
                'id_penerima' => $penerima->id,
            ], [
                'status' => 'pending'
            ]);
        }


        return redirect()->route('risalah.' . Auth::user()->role->nm_role)->with('success', 'Risalah berhasil ditambahkan');
    }

    public function updateDocumentStatus(Risalah $risalah)
    {
        $recipients = $risalah->recipients;

        if ($recipients->every(fn($recipient) => $recipient->status === 'approve')) {
            $risalah->update(['status' => 'approve']);
        } elseif ($recipients->contains(fn($recipient) => $recipient->status === 'reject')) {
            $risalah->update(['status' => 'reject']);
        } else {
            $risalah->update(['status' => 'pending']);
        }
    }

    public function updateDocumentApprovalDate(Risalah $risalah)
    {
        if ($risalah->status !== 'pending') {
            $risalah->update(['tanggal_disahkan' => now()]);
        }
    }

    public function approve(risalah $risalah)
    {
        $risalah->update([
            'status' => 'approve',
            'tanggal_disahkan' => now() // Set tanggal disahkan
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }

    public function reject(Risalah $risalah)
    {
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
        $seri = SeriRisalah::all();
        $user = Auth::User();
        $risalah = Risalah::with('risalahDetails')->findOrFail($id);
        $departmentId = $user->department_id_department;
        $divisiId = $user->divisi_id_divisi;

        $undangan = DB::select("
        SELECT *
        FROM undangan
        WHERE judul NOT IN (
            SELECT judul FROM risalah
        )
        AND EXISTS (
            SELECT 1
            FROM users
            WHERE
                users.id   = undangan.pembuat
                AND (
                    (users.department_id_department IS NOT NULL AND users.department_id_department = ?)
                    OR
                    (users.department_id_department IS NULL AND users.divisi_id_divisi = ?)
                )
        )
    ", [$departmentId, $divisiId]);

        // Ambil daftar manajer berdasarkan divisi yang sama

        return view(Auth::user()->role->nm_role . '.risalah.edit-risalah', compact('risalah', 'divisi', 'seri', 'undangan'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        // Validasi data
        $request->validate([
            'judul' => 'required',
            'agenda' => 'required',
            'tempat' => 'required',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
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
            'status' => 'pending',
        ]);

        $statusKirimDokumen = Kirim_Document::where('id_document', $risalah->id_risalah)
            ->where('jenis_document', 'risalah')
            ->first();

        if ($statusKirimDokumen) {
            $statusKirimDokumen->status = 'pending';
            $statusKirimDokumen->save();
        }

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
        return redirect()->route('risalah.' . Auth::user()->role->nm_role)->with('success', 'Risalah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Risalah::find($id)->delete();
        Kirim_Document::where('id_document', $id)->delete();

        return redirect()->route('risalah.' . Auth::user()->role->nm_role)->with('success', 'Dokumen berhasil dihapus.');
    }


    private function convertToRoman($number)
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $map[$number] ?? '';
    }

    public function view($id)
    {
        $userId = Auth::id();
        $risalah = Risalah::where('id_risalah', $id)->firstOrFail();

        // Ambil data undangan yang judulnya sama
        $undangan = Undangan::where('judul', $risalah->judul)->first();

        // Bungkus risalah dalam collection agar bisa diproses transform
        $risalahCollection = collect([$risalah]);

        $risalahCollection->transform(function ($risalah) use ($userId) {
            if ($risalah->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
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

        $risalah = $risalahCollection->first();

        // Cek apakah undangan dan tujuannya tidak null
        if ($undangan && $undangan->tujuan) {
            $userIds = explode(';', $undangan->tujuan);
            $pdfController = new \App\Http\Controllers\CetakPDFController();
            $listNama = \App\Models\User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
                ->whereIn('id', $userIds)
                ->get()
                ->map(function ($user, $key) use ($pdfController) {
                    $level = $pdfController->detectLevel($user);
                    $user->level_kerja = $level;
                    $user->bagian_text = $pdfController->getBagianText($user, $level);
                    return $user;
                })
                ->sortBy(function ($user) {
                    return optional($user->position)->id_position;
                })
                ->values();

            $tujuanUsernames = $listNama->map(function ($user, $index) {
                return ($index + 1) . '. '
                    . $user->position->nm_position . ' '
                    . $user->bagian_text . ' '
                    . '(' . $user->firstname . ' ' . $user->lastname . ')';
            })->implode("\n");
        } else {
            $tujuanUsernames = '-';
        }

        return view(Auth::user()->role->nm_role . '.risalah.view-risalah', compact('risalah', 'undangan', 'tujuanUsernames'));
    }

    public function updateStatus(Request $request, $id)
    {
        $risalah = Risalah::findOrFail($id);
        $userId = Auth::id();

        $rules = [
            'status' => 'required|in:pending,approve,reject,correction',
        ];

        // Jika status reject atau correction, catatan wajib diisi
        if (in_array($request->status, ['reject', 'correction'])) {
            $rules['catatan'] = 'required|string';
        }

        $validated = $request->validate($rules);

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
            $undangan = Undangan::where('judul', $risalah->judul)->first();

            if ($undangan) {
                $currentUserDivisiId = Auth::user()->divisi_id_divisi;
                $currentDivisi = Divisi::where('id_divisi', $currentUserDivisiId)->first();
                $tujuanString = $undangan->tujuan; // misalnya: "General Affair; QMSHE;"
                $tujuanArray = explode(';', $tujuanString);

                foreach ($tujuanArray as $idTujuan) {
                    $idTujuan = trim($idTujuan); // hilangkan spasi di pinggir
                    if (!$idTujuan) continue; // skip kalau kosong

                    $users = \App\Models\User::where('id', $idTujuan)->get();

                    foreach ($users as $user) {
                        \App\Models\Kirim_Document::firstOrCreate([
                            'id_document' => $risalah->id_risalah,
                            'jenis_document' => 'risalah',
                            'id_pengirim' => $currentKirim->id_pengirim,
                            'id_penerima' => $user->id,
                        ], [
                            'status' => 'approve'
                        ]);
                    }
                }
            }

            Notifikasi::create([
                'judul' => "Risalah Disetujui",
                'judul_document' => $risalah->judul,
                'id_user' => $risalah->pembuat,
                'updated_at' => now()
            ]);
        } elseif ($request->status == 'reject') {
            $risalah->tgl_disahkan = now();
            Notifikasi::create([
                'judul' => "Risalah Ditolak",
                'judul_document' => $risalah->judul,
                'id_user' => $risalah->pembuat,
                'updated_at' => now()
            ]);
        } elseif ($request->status == 'correction') {
            $risalah->tgl_disahkan = now();
            Notifikasi::create([
                'judul' => "Risalah Dikoreksi",
                'judul_document' => $risalah->judul,
                'id_user' => $risalah->pembuat,
                'updated_at' => now()
            ]);
        } else {
            $risalah->tgl_disahkan = null;
        }


        // Simpan catatan jika ada
        $risalah->catatan = $request->catatan;

        // Simpan perubahan
        $risalah->save();

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
            'id_user' => $risalah->pembuat,
            'dibaca'         => false,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Status memo berhasil diperbarui.');
    }
}
