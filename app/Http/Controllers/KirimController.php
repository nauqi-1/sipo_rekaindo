<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arsip;
use App\Models\Memo;
use App\Models\Divisi;
use App\Models\Seri;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Models\Undangan;
use App\Models\Kirim_Document;
use App\Models\Risalah;
use App\Models\RisalahDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use App\Http\Controllers\MemoController;

class KirimController extends Controller
{
    public function index($id)
    {
        $memo = Memo::find($id);
        $undangan = Undangan::find($id);
        $risalah = Risalah::find($id);

        if (!$memo && !$undangan && !$risalah) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $divisi = Divisi::all();
        $position = Position::all();
        $user = User::whereIn('role_id_role', ['2', '3'])->get();
        $userId = Auth::id();

        if ($memo) {
            if ($memo->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $memo->final_status = $memo->status;
            } else {
                $statusKirim = Kirim_Document::where('id_document', $memo->id_memo)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $userId)
                    ->first();
                $memo->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return view('admin.memo.kirim-memoAdmin', compact('user', 'divisi', 'memo', 'position'));
        } elseif ($undangan) {
            if ($undangan->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $undangan->final_status = $undangan->status;
            } else {
                $statusKirim = Kirim_Document::where('id_document', $undangan->id_undangan)
                    ->where('jenis_document', 'undangan')
                    ->where('id_penerima', $userId)
                    ->first();
                $undangan->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return view('admin.undangan.kirim-undanganAdmin', compact('user', 'divisi', 'undangan', 'position'));
        } elseif ($risalah) {
            if ($risalah->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $risalah->final_status = $risalah->status;
            } else {
                $statusKirim = Kirim_Document::where('id_document', $risalah->id_risalah)
                    ->where('jenis_document', 'risalah')
                    ->where(function ($query) use ($userId) {
                        $query->where('id_penerima', $userId)
                            ->orWhere('id_pengirim', $userId);
                    })
                    ->first();

                $risalah->final_status = $statusKirim ? $statusKirim->status : '-';
            }
            return view('admin.risalah.kirim-risalahAdmin', compact('user', 'divisi', 'risalah', 'position'));
        }

        // Bisa tambahkan elseif risalah di sini jika ada
    }

    public function viewManager($id)
    {
        // Cek apakah ID ini milik Memo, Undangan, atau Risalah
        $undangan = Undangan::find($id);

        // Pastikan minimal satu dokumen ditemukan
        if (!$undangan) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Ambil data divisi dan user
        $divisi = Divisi::all();
        $position = Position::all();
        $user = User::whereIn('role_id_role', ['2', '3'])->get();

        return view('manager.undangan.persetujuan-undangan', compact('user', 'divisi', 'undangan', 'position'));
    }


    public function sendDocument(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'id_document' => 'required',
            'posisi_penerima' => 'required|exists:position,id_position', // Validasi posisi
            'divisi_penerima' => 'required|exists:divisi,id_divisi', // Pastikan divisi ada
        ]);


        $documentid = $request->id_document;
        $posisiPenerima = $request->posisi_penerima;
        $divisiPenerima = $request->divisi_penerima;

        // Cari semua user dengan posisi dan divisi yang dipilih
        $penerimaUsers = User::where('position_id_position', $posisiPenerima)
            ->where('divisi_id_divisi', $divisiPenerima)
            ->get();

        if ($penerimaUsers->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada user yang sesuai dengan kriteria penerima.');
        }

        $filePath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $fileData = base64_encode(file_get_contents($file->getRealPath()));
            // Simpan file base64 ke tabel sesuai jenis dokumen
            if ($request->jenis_document == 'memo') {
                $memo = Memo::findOrFail($documentid);
                $memo->lampiran = $fileData;
                $memo->save();
            } elseif ($request->jenis_document == 'undangan') {
                $undangan = Undangan::findOrFail($documentid);
                $undangan->lampiran = $fileData;
                $undangan->save();
            } elseif ($request->jenis_document == 'risalah') {
                $risalah = Risalah::findOrFail($documentid);
                $risalah->lampiran = $fileData;
                $risalah->save();
            }
        }




        // Simpan pengiriman memo ke setiap penerima
        foreach ($penerimaUsers as $user) {
            Kirim_Document::create([
                'id_document' => $documentid,
                'jenis_document' => $request->jenis_document,
                'id_pengirim' => Auth::id(),
                'id_penerima' => $user->id,
                'status' => 'pending',
            ]);
        }
        $previousUrl = session('previous_url', route('memo.diterima'));
        session()->forget('previous_url');
        if (Auth::user()->role->nm_role == 'manager') {
            return redirect($previousUrl)->with('success', 'Dokumen berhasil dikirim.');
        } else {

            return redirect()->back()->with('success', 'Dokumen berhasil dikirim.');
        }
    }

    public function memoTerkirim(Request $request)
    {
        $userId = Auth::id();
        $memoController = new MemoController();
        $userKode = $memoController->getDivDeptKode(Auth::user());
        $sortBy = $request->get('sort_by', 'kirim_document.id_kirim_document');
        $sortDirection = $request->get('sort_direction', 'desc');



        $allowedSorts = [
            'kirim_document.id_kirim_document',
            'memo.tgl_dibuat',
            'memo.tgl_disahkan',
            'memo.judul',
            'memo.nomor_memo'
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'kirim_document.id_kirim_document';
        }

        // Get archived memo document IDs for this user
        $memoDiarsipkan = Arsip::where('user_id', $userId)
            ->where('jenis_document', 'App\\Models\\Memo')
            ->pluck('document_id')
            ->toArray();

        $memoTerkirim = Kirim_Document::query()
            ->where('jenis_document', 'memo')
            ->where('id_penerima', $userId)
            ->whereNotIn('id_document', $memoDiarsipkan) // exclude archived memos
            ->whereHas('memo', function ($query) use ($request, $userKode) {
                $query->where('memo.kode', $userKode);

                // Date filtering
                if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
                    $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
                } elseif ($request->filled('tgl_dibuat_awal')) {
                    $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
                } elseif ($request->filled('tgl_dibuat_akhir')) {
                    $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
                }

                // Search
                if ($request->filled('search')) {
                    $query->where(function ($q) use ($request) {
                        $q->where('judul', 'like', '%' . $request->search . '%')
                            ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
                    });
                }

                // Status filter
                if ($request->filled('status')) {
                    $query->where('memo.status', $request->status);
                }
            })
            ->whereIn('id_kirim_document', function ($subQuery) {
                // Subquery to get earliest send per document
                $subQuery->selectRaw('MIN(id_kirim_document)')
                    ->from('kirim_document')
                    ->groupBy('id_document');
            })
            ->with('memo');

        if (Str::startsWith($sortBy, 'memo.')) {
            $memoColumn = Str::after($sortBy, 'memo.');
            $memoTerkirim->join('memo', 'kirim_document.id_document', '=', 'memo.id_memo')
                ->orderBy("memo.$memoColumn", $sortDirection)
                ->select('kirim_document.*'); // agar tetap menghasilkan Kirim_Document model
        } else {
            $memoTerkirim->orderBy($sortBy, $sortDirection);
        }

        $perPage = $request->get('per_page', 10);
        $memoTerkirim = $memoTerkirim->paginate($perPage);

        return view('manager.memo.memo-terkirim', compact('memoTerkirim', 'sortBy', 'sortDirection'));
    }

    public function memoDiterima(Request $request)
    {
        $userId = auth()->id();
        $memoController = new MemoController();
        $userKode = $memoController->getDivDeptKode(Auth::user());
        session(['previous_url' => url()->previous()]);
        $sortBy = $request->get('sort_by', 'kirim_document.id_kirim_document');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSorts = [
            'kirim_document.id_kirim_document',
            'memo.tgl_dibuat',
            'memo.tgl_disahkan',
            'memo.judul',
            'memo.nomor_memo'
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'kirim_document.id_kirim_document';
        }
        // Get archived memo document IDs for this user
        $memoDiarsipkan = Arsip::where('user_id', $userId)
            ->where('jenis_document', 'App\\Models\\Memo')
            ->pluck('document_id')
            ->toArray();

        $memoDiterima = Kirim_Document::where('jenis_document', 'memo')
            ->where('id_penerima', $userId)
            ->whereNotIn('id_document', $memoDiarsipkan) // exclude archived
            ->whereIn('kirim_document.status', ['pending', 'approve'])
            ->whereHas('memo', function ($query) use ($request, $userKode) {
                $query->where('kode', '!=', $userKode);

                if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
                    $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
                } elseif ($request->filled('tgl_dibuat_awal')) {
                    $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
                } elseif ($request->filled('tgl_dibuat_akhir')) {
                    $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
                }

                if ($request->filled('search')) {
                    $query->where(function ($q2) use ($request) {
                        $q2->where('judul', 'like', '%' . $request->search . '%')
                            ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
                    });
                }
            })
            ->whereIn('id_kirim_document', function ($subQuery) use ($userId) {
                $subQuery->selectRaw('MIN(id_kirim_document)')
                    ->from('kirim_document')
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $userId)
                    ->groupBy('id_document');
            })
            ->with('memo');

        if (Str::startsWith($sortBy, 'memo.')) {
            $memoColumn = Str::after($sortBy, 'memo.');
            $memoDiterima->join('memo', 'kirim_document.id_document', '=', 'memo.id_memo')
                ->orderBy("memo.$memoColumn", $sortDirection)
                ->select('kirim_document.*');
        } else {
            $memoDiterima->orderBy($sortBy, $sortDirection);
        }

        $perPage = $request->get('per_page', 10);
        $memoDiterima = $memoDiterima->paginate($perPage);

        return view('manager.memo.memo-diterima', compact('memoDiterima', 'sortBy', 'sortDirection'));
    }

    public function undangan(Request $request)
    {
        $userId = auth()->id();
        $filterType = $request->get('userid_filter', 'both');
        $sortBy = $request->get('sort_by', 'tgl_rapat_diff');
        $sortDirection = $request->get('sort_direction', 'asc') === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'kirim_document.id_kirim_document',
            'undangan.tgl_dibuat',
            'undangan.tgl_disahkan',
            'undangan.judul',
            'undangan.nomor_undangan',
            'tgl_rapat_diff'
        ];

        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'tgl_rapat_diff';

        // Ambil id_kirim_document terkecil tiap id_document
        $subQuery = Kirim_Document::where('jenis_document', 'undangan')
            ->where(function ($q) use ($userId, $filterType) {
                if ($filterType === 'own') {
                    $q->where('id_pengirim', $userId);
                } elseif ($filterType === 'other') {
                    $q->where('id_penerima', $userId);
                } else {
                    $q->where(function ($q2) use ($userId) {
                        $q2->where('id_pengirim', $userId)->orWhere('id_penerima', $userId);
                    });
                }
            });

        if ($request->filled('status')) $subQuery->where('status', $request->status);

        $subQuery->whereHas('undangan', function ($q) use ($request) {
            if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
                $q->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
            } elseif ($request->filled('tgl_dibuat_awal')) {
                $q->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
            } elseif ($request->filled('tgl_dibuat_akhir')) {
                $q->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
            }

            if ($request->filled('search')) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('judul', 'like', '%' . $request->search . '%')
                        ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
                });
            }
        });

        $idKirimList = $subQuery->selectRaw('MIN(id_kirim_document) as id_kirim_document')
            ->groupBy('id_document')
            ->pluck('id_kirim_document');

        // 🔹 Ambil semua undangan yang sudah diarsipkan user ini
        $undanganDiarsipkan = Arsip::where('user_id', $userId)
            ->where('jenis_document', 'App\\Models\\Undangan')
            ->pluck('document_id')
            ->toArray();

        // Query utama kirim_document + undangan
        $undangans = Kirim_Document::whereIn('id_kirim_document', $idKirimList)
            ->whereNotIn('id_document', $undanganDiarsipkan) // ⬅ filter arsip disini
            ->with('undangan');

        // Sorting
        if ($sortBy == 'tgl_rapat_diff') {
            $undangans->join('undangan', 'kirim_document.id_document', '=', 'undangan.id_undangan')
                ->orderByRaw("
                CASE WHEN DATEDIFF(tgl_rapat, CURDATE()) < 0 THEN 1 ELSE 0 END ASC
            ")
                ->orderByRaw("
                ABS(DATEDIFF(tgl_rapat, CURDATE())) {$sortDirection}
            ")
                ->select('kirim_document.*');
        } elseif (Str::startsWith($sortBy, 'undangan.')) {
            $field = Str::after($sortBy, 'undangan.');
            $undangans->join('undangan', 'kirim_document.id_document', '=', 'undangan.id_undangan')
                ->orderBy("undangan.$field", $sortDirection)
                ->select('kirim_document.*');
        } else {
            $undangans->orderBy($sortBy, $sortDirection);
        }

        $perPage = $request->get('per_page', 10);
        $undangans = $undangans->paginate($perPage);

        return view('manager.undangan.undangan', compact('undangans', 'sortBy', 'sortDirection'));
    }



    public function risalah(Request $request)
    {
        $userId = auth()->id();
        $sortBy = $request->get('sort_by', 'kirim_document.id_kirim_document');
        $sortDirection = $request->get('sort_direction', 'desc');
        $perPage = $request->get('per_page', 10);

        // Validasi kolom sort yang diperbolehkan
        $allowedSorts = [
            'kirim_document.id_kirim_document',
            'risalah.tgl_dibuat',
            'risalah.tgl_disahkan',
            'risalah.judul',
            'risalah.nomor_risalah'
        ];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'kirim_document.id_kirim_document';
        }

        // Subquery untuk ambil kirim_document unik per id_document
        $subQuery = DB::table('kirim_document')
            ->selectRaw('MIN(id_kirim_document) as id_kirim_document')
            ->where('jenis_document', 'risalah')
            ->where(function ($query) use ($userId) {
                $query->where('id_penerima', $userId)
                    ->orWhere('id_pengirim', $userId);
            })
            ->groupBy('id_document');

        // 🔹 Ambil risalah yang sudah diarsipkan oleh user ini
        $risalahDiarsipkan = Arsip::where('user_id', $userId)
            ->where('jenis_document', 'App\\Models\\Risalah')
            ->pluck('document_id')
            ->toArray();

        // Ambil data kirim_document utama berdasarkan hasil subquery
        $risalahs = Kirim_Document::whereIn('id_kirim_document', $subQuery)
            ->whereNotIn('id_document', $risalahDiarsipkan) // ⬅ Filter arsip
            ->when(auth()->user()->position_id_position == 1, function ($query) {
                $query->where('status', 'approve');
            })
            ->with(['risalah' => function ($query) use ($request) {
                // Filter tanggal dibuat
                if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
                    $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
                } elseif ($request->filled('tgl_dibuat_awal')) {
                    $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
                } elseif ($request->filled('tgl_dibuat_akhir')) {
                    $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
                }

                // Filter pencarian
                if ($request->filled('search')) {
                    $query->where(function ($q) use ($request) {
                        $q->where('judul', 'like', '%' . $request->search . '%')
                            ->orWhere('nomor_risalah', 'like', '%' . $request->search . '%');
                    });
                }
            }]);

        // Sorting
        if (Str::startsWith($sortBy, 'risalah.')) {
            // Sorting berdasarkan relasi risalah
            $risalahs = $risalahs->join('risalah', 'kirim_document.id_document', '=', 'risalah.id_risalah')
                ->orderBy(Str::after($sortBy, 'risalah.'), $sortDirection)
                ->select('kirim_document.*'); // pastikan hanya ambil kolom utama
        } else {
            $risalahs = $risalahs->orderBy($sortBy, $sortDirection);
        }

        // Paginate
        $risalahs = $risalahs->paginate($perPage);

        return view('manager.risalah.risalah', compact('risalahs', 'sortBy', 'sortDirection'));
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

        $user = Auth::user();

        $unitId       = $user->unit_id_unit;
        $sectionId    = $user->section_id_section;
        $departmentId = $user->department_id_department;
        $divisiId     = $user->divisi_id_divisi;
        $directorId   = $user->id_director;

        $query = "
            SELECT *
            FROM undangan
            WHERE judul NOT IN (
                SELECT judul FROM risalah
            )
            AND EXISTS (
                SELECT 1
                FROM users
                WHERE users.id = undangan.pembuat
        ";

        $params = [];

        if ($unitId) {
            $query .= " AND users.unit_id_unit = ?";
            $params[] = $unitId;
        } elseif ($sectionId) {
            $query .= " AND users.section_id_section = ?";
            $params[] = $sectionId;
        } elseif ($departmentId) {
            $query .= " AND users.department_id_department = ?";
            $params[] = $departmentId;
        } elseif ($divisiId) {
            $query .= " AND users.divisi_id_divisi = ?";
            $params[] = $divisiId;
        } elseif ($directorId) {
            $query .= " AND users.id_director = ?";
            $params[] = $directorId;
        } else {
            // Jika semuanya kosong, tidak perlu query, langsung return null / []
            return [];
        }

        $query .= ")";
        $undangan = DB::select($query, $params);

        $risalah = new Risalah(); // atau ambil dari data risalah terakhir, terserah kebutuhanmu

        // Ambil nomor seri berikutnya
        $nextSeri = Seri::getNextSeri(false);
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

        $divisiId = auth()->user()->divisi_id_divisi;
        $seri = Seri::getNextSeri(true);
        $seri = Seri::where('tahun', now()->year)
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
            'status' => 'approve',
            'judul' => $request->judul,
            'pembuat' => $request->pembuat,
            'lampiran' => $filePath,
            'nama_bertandatangan' => $request->nama_bertandatangan,
            'risalah_id_risalah' => $request->id_risalah,
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
                        'id_pengirim' => Auth::user()->id,
                        'id_penerima' => $user->id,
                    ], [
                        'status' => 'approve'
                    ]);
                }
            }
        }

        $risalah->save();

        return redirect()->route('risalah.' . Auth::user()->role->nm_role)->with('success', 'Risalah berhasil ditambahkan');
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

    // Daftar dokumen yang dikirim
    public function sentDocuments()
    {
        $documents = Kirim_Document::where('id_pengirim', Auth::id())->get();
        return view('manager.memo.memo-terkirim', compact('documents'));
    }

    // Daftar dokumen yang diterima


    public function viewRisalah($id)
    {
        // Cek apakah ID ini milik Risalah
        $risalah = Risalah::find($id);

        if (!$risalah) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan.');
        }

        // Ambil data referensi
        $divisi = Divisi::all();
        $position = Position::all();
        $user = User::whereIn('role_id_role', ['2', '3'])->get();

        // Ambil undangan berdasarkan judul risalah
        $undangan = Undangan::where('judul', $risalah->judul)->first();

        // Cek apakah undangan dan tujuannya ada
        if ($undangan && $undangan->tujuan) {
            $userIds = explode(';', $undangan->tujuan);
            $pdfController = new \App\Http\Controllers\CetakPDFController();
            $listNama = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
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

        return view('manager.risalah.persetujuan-risalah', compact('user', 'divisi', 'risalah', 'position', 'undangan', 'tujuanUsernames'));
    }
}
