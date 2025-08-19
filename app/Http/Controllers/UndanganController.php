<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Seri;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Arsip;
use App\Models\Notifikasi;
use App\Models\Undangan;
use App\Models\Department;
use App\Models\Director;
use App\Models\Backup_Document;
use App\Models\Kirim_Document;
use App\Models\Section;
use App\Models\Unit;
use App\Http\Controllers\CetakPDFController;
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
        $seri = Seri::all();
        $userId = Auth::id();

        // Ambil semua document_id undangan yang sudah diarsipkan user ini
        $undanganDiarsipkan = Arsip::where('user_id', $userId)
            ->where('jenis_document', 'App\\Models\\Undangan') // Filter hanya undangan
            ->pluck('document_id')
            ->toArray();

        $sortBy = $request->get('sort_by', 'tgl_rapat_diff');
        $sortDirection = $request->get('sort_direction', 'asc') === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_undangan', 'judul', 'tgl_rapat_diff'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'tgl_rapat_diff';
        }

        // Query undangan yang belum diarsipkan user ini
        $query = Undangan::whereNotIn('id_undangan', $undanganDiarsipkan)
            ->whereHas('kirimDocument', function ($q) use ($userId) {
                $q->where('jenis_document', 'undangan')
                    ->where(function ($sub) use ($userId) {
                        $sub->where('id_pengirim', $userId)
                            ->orWhere('id_penerima', $userId);
                    });
            });

        if ($sortBy === 'tgl_rapat_diff') {
            $query
                ->whereNotNull('tgl_rapat')
                ->orderByRaw("
                CASE
                    WHEN DATEDIFF(tgl_rapat, CURDATE()) < 0 THEN 1
                    ELSE 0
                END ASC
            ")
                ->orderByRaw("
                ABS(DATEDIFF(tgl_rapat, CURDATE())) $sortDirection
            ");
        } elseif (in_array($sortBy, ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_undangan', 'judul'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('tgl_dibuat', 'desc');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $filterType = $request->get('userid_filter', 'both');
        if ($filterType === 'own') {
            $query->whereHas('kirimDocument', function ($q) use ($userId) {
                $q->where('jenis_document', 'undangan')
                    ->where('id_pengirim', $userId);
            });
        } elseif ($filterType === 'other') {
            $query->whereHas('kirimDocument', function ($q) use ($userId) {
                $q->where('jenis_document', 'undangan')
                    ->where('id_penerima', $userId);
            });
        }

        if ($request->filled('tgl_dibuat_awal') && $request->filled('tgl_dibuat_akhir')) {
            $query->whereBetween('tgl_dibuat', [$request->tgl_dibuat_awal, $request->tgl_dibuat_akhir]);
        } elseif ($request->filled('tgl_dibuat_awal')) {
            $query->whereDate('tgl_dibuat', '>=', $request->tgl_dibuat_awal);
        } elseif ($request->filled('tgl_dibuat_akhir')) {
            $query->whereDate('tgl_dibuat', '<=', $request->tgl_dibuat_akhir);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->get('per_page', 10);
        $undangans = $query->paginate($perPage);

        $undangans->getCollection()->transform(function ($undangan) use ($userId) {
            $statusKirim = Kirim_Document::where('id_document', $undangan->id_undangan)
                ->where('jenis_document', 'undangan')
                ->where('id_penerima', $userId)
                ->first();

            $undangan->final_status = $statusKirim ? $statusKirim->status : '-';
            return $undangan;
        });

        $kirimDocuments = Kirim_Document::where('jenis_document', 'undangan')
            ->where(function ($query) use ($userId) {
                $query->where('id_pengirim', $userId)
                    ->orWhere('id_penerima', $userId);
            })->get();

        return view(Auth::user()->role->nm_role . '.undangan.undangan', compact('undangans', 'seri', 'sortDirection', 'kirimDocuments'));
    }

    public function superadmin(Request $request)
    {   //dd('Superadmin Undangan');
        $divisi = Divisi::all();
        $kode = Undangan::whereNotNull('kode')
            ->pluck('kode')
            ->unique();

        $seri = Seri::all();
        $userId = Auth::id();


        $undanganDiarsipkan = Arsip::where('user_id', Auth::id())->pluck('document_id')->toArray();
        $sortBy = $request->get('sort_by', 'created_at'); // default ke created_at
        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSortColumns = ['created_at', 'tgl_disahkan', 'tgl_dibuat', 'nomor_undangan', 'judul'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // fallback default
        }

        $query = Undangan::query()
            ->whereNotIn('id_undangan', $undanganDiarsipkan)
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

        // Ambil semua arsip undangan berdasarkan user login
        $arsipUndanganQuery = Arsip::where('user_id', $userId)
            ->where('jenis_document', 'undangan')
            ->with('document');

        $sortDirection = $request->get('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('created_at', $sortDirection);

        if ($request->filled('kode') && $request->kode != 'pilih') {
            $query->where('kode', $request->kode);
        }

        // Pencarian berdasarkan nama dokumen atau nomor memo
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_undangan', 'like', '%' . $request->search . '%');
            });
        }
        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $undangans = $query->paginate($perPage);


        return view('superadmin.undangan.undangan', compact('undangans', 'kode', 'seri', 'sortDirection'));
    }



    public function create()
    {

        $orgTree = $this->getOrgTreeWithUsers();
        $jsTreeData = $this->convertToJsTree($orgTree);


        $divisiList = Divisi::all();

        $idUser = Auth::user();
        $user = User::where('id', $idUser->id)->first();

        if ($user->position_id_position == 1) {
            $idDirektur = Director::where('id_director', $user->director_id_director)->first();
            $kodeDirektur = $idDirektur->kode_director;
        } else {
            $kodeDirektur = '';
        }
        // dd($user);

        $divDeptKode = $this->getDivDeptKode($user);

        // Ambil nomor seri berikutnya
        $nextSeri = Seri::getNextSeri(false);
        // Konversi bulan ke angka Romawi
        $bulanRomawi = $this->convertToRoman(now()->month);
        // Format nomor dokumen
        $nomorDokumen = sprintf(
            "%d.%d/REKA/GEN/%s/%s/%d",
            $nextSeri['seri_tahunan'],
            $nextSeri['seri_bulanan'],
            // strtoupper($kodeDirektur),
            strtoupper($divDeptKode),
            $bulanRomawi,
            now()->year
        );

        //Mengambil data manager yang bertanda tangan nanti
        $user = Auth::user();

        //ini di cek berdasarkan role manager kemudian dicari yang cocok dengan yang login dan ditampilkan di dropdown
        if ($user->position_id_position !== 1) {
            $managers = User::with('position:id_position,nm_position')
                ->where('role_id_role', 3)
                ->where(function ($q) use ($user) {
                    $q->where(function ($q2) use ($user) {
                        $q2->whereNotNull('divisi_id_divisi')
                            ->where('divisi_id_divisi', $user->divisi_id_divisi);
                    })->orWhere(function ($q2) use ($user) {
                        $q2->whereNotNull('department_id_department')
                            ->where('department_id_department', $user->department_id_department);
                    })->orWhere(function ($q2) use ($user) {
                        $q2->whereNotNull('section_id_section')
                            ->where('section_id_section', $user->section_id_section);
                    })->orWhere(function ($q2) use ($user) {
                        $q2->whereNotNull('unit_id_unit')
                            ->where('unit_id_unit', $user->unit_id_unit);
                    });
                })
                ->get(['id', 'firstname', 'lastname', 'position_id_position']);
        } else
            $managers = collect([
                User::with('position:id_position,nm_position')
                    ->find($user->id, ['id', 'firstname', 'lastname', 'position_id_position'])
            ]);




        // Ambil seluruh user dan struktur organisasi (untuk dropdown tree)
        $users = User::select('id', 'firstname', 'lastname', 'divisi_id_divisi', 'department_id_department', 'section_id_section', 'unit_id_unit')->get();
        // Struktur organisasi tree (harus dibuat di backend, contoh dummy di bawah)
        $orgTree = $this->getOrgTreeWithUsers();
        $mainDirector = $orgTree[0] ?? null; // assuming the first node is the main director
        //dd($jsTreeData);

        return view(Auth::user()->role->nm_role . '.undangan.add-undangan', [
            'nomorSeriTahunan' => $nextSeri['seri_tahunan'],
            'nomorDokumen' => $nomorDokumen,
            'kode' => $divDeptKode,
            'managers' => $managers,
            'divisiList' => $divisiList,
            'users' => $users,
            'orgTree' => $orgTree,
            'jsTreeData' => $jsTreeData,
            'mainDirector' => $mainDirector
        ]);
    }

    //PRIVATE FUNCTIONS UNTUK BUAT TREE TUJUAN[]
    private function getOrgTreeWithUsers()
    {
        $directors = Director::with([
            'users.position',
            'divisi.users.position',
            'divisi.department.users.position',
            'divisi.department.section.users.position',
            'divisi.department.section.unit.users.position',
            'department.users.position',
            'department.section.users.position',
            'department.section.unit.users.position'
        ])->get();


        $tree = [];

        foreach ($directors as $director) {
            $dir = $director->toArray();
            $dir['users'] = $director->users->toArray();
            $tree[] = $dir;
        }
        return $tree;
    }
    private function filterUsersAtLevel($users, $level)
    {
        return array_values(array_filter($users, function ($user) use ($level) {
            return (
                ($level === 'director' && is_null($user['divisi_id_divisi']) && is_null($user['department_id_department']) && is_null($user['section_id_section']) && is_null($user['unit_id_unit'])) ||
                ($level === 'divisi' && !is_null($user['divisi_id_divisi']) && is_null($user['department_id_department']) && is_null($user['section_id_section']) && is_null($user['unit_id_unit'])) ||
                ($level === 'department' && !is_null($user['department_id_department']) && is_null($user['section_id_section']) && is_null($user['unit_id_unit'])) ||
                ($level === 'section' && !is_null($user['section_id_section']) && is_null($user['unit_id_unit'])) ||
                ($level === 'unit' && !is_null($user['unit_id_unit']))
            );
        }));
    }

    private function getUserText($user, $context)
    {
        $rawPosition = isset($user['position']['nm_position']) ? $user['position']['nm_position'] : '-';

        // Format position - remove parentheses and create abbreviations
        if ($rawPosition !== '-') {
            // Remove parentheses and content inside them, then clean up extra spaces
            $position = preg_replace('/\s*\([^)]*\)\s*/', ' ', $rawPosition);
            $position = trim(preg_replace('/\s+/', ' ', $position));

            // Create abbreviations for common positions
            if (!in_array($position, ['Staff', 'Direktur'])) {
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
                    if (strpos($position, $full) !== false) {
                        $position = str_replace($full, $abbrev, $position);
                        break;
                    }
                }
            }
        } else {
            $position = '-';
        }

        $hierarki = collect([
            $context['unit'] ?? null,
            $context['section'] ?? null,
            $context['department'] ?? null,
            $context['divisi'] ?? null,
            $context['director'] ?? null
        ])->filter()->first() ?? '-';

        $firstname = $user['firstname'] ?? ($user['nm_user'] ?? '-');
        $lastname = $user['lastname'] ?? '';

        return "$position $hierarki ($firstname $lastname)";
    }

    private function convertToJsTree($tree)
    {
        $result = [];
        foreach ($tree as $director) {
            $dirNode = [
                'id' => 'director-' . $director['id_director'],
                'text' => $director['name_director'],
                'children' => []
            ];

            $usersAtDirector = $this->filterUsersAtLevel($director['users'], 'director');
            foreach ($usersAtDirector as $user) {
                $dirNode['children'][] = [
                    'id' => 'user-' . $user['id'],
                    'text' => $this->getUserText($user, ['director' => $director['name_director']]),
                    'icon' => 'fa fa-user'

                ];
            }

            if (empty($director['divisi'])) {
                foreach ($director['department'] ?? [] as $dept) {
                    $deptNode = ['id' => 'dept-' . $dept['id_department'], 'text' => $dept['name_department'], 'children' => []];
                    $usersAtDepartment = $this->filterUsersAtLevel($dept['users'] ?? [], 'department');
                    foreach ($usersAtDepartment as $user) {
                        $deptNode['children'][] = [
                            'id' => 'user-' . $user['id'],
                            'text' => $this->getUserText($user, [
                                'director' => $director['name_director'],
                                'department' => $dept['name_department']
                            ]),
                            'icon' => 'fa fa-user'
                        ];
                    }
                    foreach ($dept['section'] ?? [] as $section) {
                        $sectionNode = ['id' => 'section-' . $section['id_section'], 'text' => $section['name_section'], 'children' => []];
                        $usersAtSection = $this->filterUsersAtLevel($section['users'] ?? [], 'section');
                        foreach ($usersAtSection as $user) {
                            $sectionNode['children'][] = [
                                'id' => 'user-' . $user['id'],
                                'text' => $this->getUserText($user, [
                                    'director' => $director['name_director'],
                                    'department' => $dept['name_department'],
                                    'section' => $section['name_section']
                                ]),
                                'icon' => 'fa fa-user'
                            ];
                        }
                        foreach ($section['unit'] ?? [] as $unit) {
                            $unitNode = ['id' => 'unit-' . $unit['id_unit'], 'text' => $unit['name_unit'], 'children' => []];
                            $usersAtUnit = $this->filterUsersAtLevel($unit['users'] ?? [], 'unit');
                            foreach ($usersAtUnit as $user) {
                                $unitNode['children'][] = [
                                    'id' => 'user-' . $user['id'],
                                    'text' => $this->getUserText($user, [
                                        'director' => $director['name_director'],
                                        'department' => $dept['name_department'],
                                        'section' => $section['name_section'],
                                        'unit' => $unit['name_unit']
                                    ]),
                                    'icon' => 'fa fa-user'
                                ];
                            }
                            $sectionNode['children'][] = $unitNode;
                        }
                        $deptNode['children'][] = $sectionNode;
                    }
                    $dirNode['children'][] = $deptNode;
                }
            } else {
                foreach ($director['divisi'] ?? [] as $divisi) {
                    $divNode = ['id' => 'divisi-' . $divisi['id_divisi'], 'text' => $divisi['nm_divisi'], 'children' => []];
                    $usersAtDivisi = $this->filterUsersAtLevel($divisi['users'] ?? [], 'divisi');
                    foreach ($usersAtDivisi as $user) {
                        $divNode['children'][] = [
                            'id' => 'user-' . $user['id'],
                            'text' => $this->getUserText($user, [
                                'director' => $director['name_director'],
                                'divisi' => $divisi['nm_divisi']
                            ]),
                            'icon' => 'fa fa-user'
                        ];
                    }
                    foreach ($divisi['department'] ?? [] as $dept) {
                        $deptNode = ['id' => 'dept-' . $dept['id_department'], 'text' => $dept['name_department'], 'children' => []];
                        $usersAtDepartment = $this->filterUsersAtLevel($dept['users'] ?? [], 'department');
                        foreach ($usersAtDepartment as $user) {
                            $deptNode['children'][] = [
                                'id' => 'user-' . $user['id'],
                                'text' => $this->getUserText($user, [
                                    'director' => $director['name_director'],
                                    'divisi' => $divisi['nm_divisi'],
                                    'department' => $dept['name_department']
                                ]),
                                'icon' => 'fa fa-user'
                            ];
                        }
                        foreach ($dept['section'] ?? [] as $section) {
                            $sectionNode = ['id' => 'section-' . $section['id_section'], 'text' => $section['name_section'], 'children' => []];
                            $usersAtSection = $this->filterUsersAtLevel($section['users'] ?? [], 'section');
                            foreach ($usersAtSection as $user) {
                                $sectionNode['children'][] = [
                                    'id' => 'user-' . $user['id'],
                                    'text' => $this->getUserText($user, [
                                        'director' => $director['name_director'],
                                        'divisi' => $divisi['nm_divisi'],
                                        'department' => $dept['name_department'],
                                        'section' => $section['name_section']
                                    ]),
                                    'icon' => 'fa fa-user'
                                ];
                            }
                            foreach ($section['unit'] ?? [] as $unit) {
                                $unitNode = ['id' => 'unit-' . $unit['id_unit'], 'text' => $unit['name_unit'], 'children' => []];
                                $usersAtUnit = $this->filterUsersAtLevel($unit['users'] ?? [], 'unit');
                                foreach ($usersAtUnit as $user) {
                                    $unitNode['children'][] = [
                                        'id' => 'user-' . $user['id'],
                                        'text' => $this->getUserText($user, [
                                            'director' => $director['name_director'],
                                            'divisi' => $divisi['nm_divisi'],
                                            'department' => $dept['name_department'],
                                            'section' => $section['name_section'],
                                            'unit' => $unit['name_unit']
                                        ]),
                                        'icon' => 'fa fa-user'
                                    ];
                                }
                                $sectionNode['children'][] = $unitNode;
                            }
                            $deptNode['children'][] = $sectionNode;
                        }
                        $divNode['children'][] = $deptNode;
                    }
                    $dirNode['children'][] = $divNode;
                }
            }
            $result[] = $dirNode;
        }
        return json_encode($result);
    }
    private function getDivDeptKode($user)
    {
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

        return ($divisiName);
    }
    private function containsEmoji($text)
    {
        if (empty($text)) return false;

        // Regex pattern untuk detect emoji
        $emojiPattern = '/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u';

        // Pattern tambahan untuk emoji lainnya
        $additionalEmojiPattern = '/[\x{1F900}-\x{1F9FF}]|[\x{1FA70}-\x{1FAFF}]|[\x{1F780}-\x{1F7FF}]|[\x{1F800}-\x{1F8FF}]/u';

        return preg_match($emojiPattern, $text) || preg_match($additionalEmojiPattern, $text);
    }

    // Method helper untuk validasi emoji pada field tertentu
    private function validateNoEmoji($request)
    {
        $fieldsToCheck = ['judul', 'isi_undangan', 'waktu_mulai', 'waktu_selesai', 'tempat'];
        $errors = [];

        foreach ($fieldsToCheck as $field) {
            if ($request->filled($field) && $this->containsEmoji($request->input($field))) {
                $fieldName = $this->getFieldDisplayName($field);
                $errors[$field] = "Field {$fieldName} tidak boleh mengandung emoji.";
            }
        }

        return $errors;
    }

    // Helper untuk nama field yang user-friendly sesuai label di form
    private function getFieldDisplayName($field)
    {
        $names = [
            'judul' => 'Judul',
            'isi_undangan' => 'Agenda',  // Sesuai label yang user lihat
            'waktu_mulai' => 'Waktu Mulai',
            'waktu_selesai' => 'Waktu Selesai',
            'tempat' => 'Tempat'
        ];

        return $names[$field] ?? ucfirst($field);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $emojiErrors = $this->validateNoEmoji($request);
        if (!empty($emojiErrors)) {
            return redirect()->back()
                ->withErrors($emojiErrors)
                ->withInput();
        }
        // Ubah validasi tujuan jadi array
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:70',
            'isi_undangan' => 'required|string',
            'tujuan' => 'required|array|min:1',
            'nomor_undangan' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|exists:users,id', // harus user id
            'pembuat' => 'required|int|max:255',
            'catatan' => 'nullable|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'kode' => 'required|string|max:10',
            'tgl_disahkan' => 'nullable|date',
            'tgl_rapat' => 'required|date',
            'tempat' => 'required|string',
            'waktu_mulai' => 'required|string',
            'waktu_selesai' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tgl_dibuat.required' => 'Tanggal dibuat wajib diisi.',
            'tujuan.required' => 'Minimal satu tujuan harus dipilih.',
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




        //SIMPAN NOMER SERI
        $user = Auth::user();

        if ($user->divisi_id_divisi) {
            $divisi = \App\Models\Divisi::find($user->divisi_id_divisi);
            $divDeptKode = $divisi?->kode_divisi;
        } elseif ($user->id_department) {
            $department = \App\Models\Department::find($user->id_department);
            $divDeptKode = $department?->kode_department;
        } else {
            $divDeptKode = null;
        }
        $seri = Seri::where('kode', $divDeptKode)
            ->where('tahun', now()->year)
            ->latest()
            ->first();
        $seri = Seri::getNextSeri(true);
        if (!$seri) {
            return back()->with('error', 'Nomor seri tidak ditemukan.');
        }




        //PROSES PECAH ID DARI 'tujuan'
        $tujuanArray = is_array($request->tujuan) ? $request->tujuan : explode(';', $request->tujuan);
        $tujuanString = implode(';', $tujuanArray);


        //dd($request->all());
        // PROSES UNTUK Simpan undangan KE DATABASE
        $manager = User::findOrFail($request->input('nama_bertandatangan')); // Ambil user manager yang dipilih
        //dd($request->all());
        $undangan = Undangan::create([
            'judul' => $request->input('judul'),
            'tujuan' => $tujuanString,
            'isi_undangan' => $request->input('isi_undangan'),
            'nomor_undangan' => $request->input('nomor_undangan'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'pembuat' => $request->input('pembuat'),
            'catatan' => $request->input('catatan'),
            'seri_surat' => $request->input('seri_surat'),
            'kode' => $request->input('kode'),
            'status' => 'pending',
            'tgl_rapat' => $request->input('tgl_rapat'),
            'tempat' => $request->input('tempat'),
            'waktu_mulai' => $request->input('waktu_mulai'),
            'waktu_selesai' => $request->input('waktu_selesai'),
            // Simpan nama lengkap manager, bukan id
            'nama_bertandatangan' => $manager->firstname . ' ' . $manager->lastname,
            'lampiran' => $filePath,
        ]);

        //PROSES PENGIRIMAN DOKUMEN
        $creator = Auth::user();
        if (Auth::user()->role_id_role == 3) { // Manager

            // PROSES TTD OLEH MANAGER
            $qrText = "Disetujui oleh: " . Auth::user()->firstname . ' ' . Auth::user()->lastname . "\nTanggal: " . now()->translatedFormat('l, d F Y');
            $qrImage = QrCode::format('svg')->generate($qrText);
            $qrBase64 = base64_encode($qrImage);

            $undangan->qr_approved_by = $qrBase64;
            $undangan->status = 'approve';
            $undangan->tgl_disahkan = now();
            $undangan->save();

            $tujuanUserId = is_array($undangan->tujuan) ? $undangan->tujuan : explode(';', $undangan->tujuan);



            foreach ($tujuanUserId as $user) {
                if ($user == $creator->id) continue;
                $recipients = User::where('id', $user)->get();
                foreach ($recipients as $recipient) {
                    $sudahDikirim = Kirim_Document::where([
                        ['id_document', $undangan->id_undangan],
                        ['jenis_document', 'undangan'],
                        ['id_pengirim', $creator->id],
                        ['id_penerima', $recipient->id],
                        ['status', 'approve'],
                        ['updated_at', now()] // Cek apakah sudah dikirim dalam 5 menit terakhir
                    ])->exists();

                    if (!$sudahDikirim) {
                        Kirim_Document::create([
                            'id_document' => $undangan->id_undangan,
                            'jenis_document' => 'undangan',
                            'id_pengirim' => $creator->id,
                            'id_penerima' => $recipient->id,
                            'status' => 'approve',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    //Notifikasi
                    Notifikasi::create([
                        'judul' => "Undangan Masuk",
                        'judul_document' => $undangan->judul,
                        'id_user' => $recipient->id,
                        'updated_at' => now()
                    ]);
                }
            }


            //Notifikasi
            Notifikasi::create([
                'judul' => "Undangan Terkirim",
                'judul_document' => $undangan->judul,
                'id_user' => $undangan->pembuat,
                'updated_at' => now()
            ]);
        } else {
            // Kirim ke manager yang dipilih (approval masih pending)
            Kirim_Document::create([
                'id_document' => $undangan->id_undangan,
                'jenis_document' => 'undangan',
                'id_pengirim' => Auth::user()->id,
                'id_penerima' => $manager->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Notifikasi::create([
                'judul' => "Undangan Dalam Proses Persetujuan",
                'judul_document' => $undangan->judul,
                'id_user' => $undangan->pembuat,
                'updated_at' => now()
            ]);
            Notifikasi::create([
                'judul' => "Undangan Menunggu Approval",
                'judul_document' => $undangan->judul,
                'id_user' => $manager->id,
                'updated_at' => now()
            ]);
        }



        return redirect()->route('undangan.' . Auth::user()->role->nm_role)
            ->with('success', 'Dokumen berhasil dibuat.');
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
            // PROSES TTD OLEH MANAGER
            $qrText = "Disetujui oleh: " . Auth::user()->firstname . ' ' . Auth::user()->lastname . "\nTanggal: " . now()->translatedFormat('l, d F Y');
            $qrImage = QrCode::format('svg')->generate($qrText);
            $qrBase64 = base64_encode($qrImage);

            $undangan->qr_approved_by = $qrBase64;
            $undangan->status = 'approve';
            $undangan->tgl_disahkan = now();
            $undangan->save();

            $tujuanUserId = is_array($undangan->tujuan) ? $undangan->tujuan : explode(';', $undangan->tujuan);



            foreach ($tujuanUserId as $user) {
                if ($user == $undangan->pembuat) continue;
                $sudahDikirim = Kirim_Document::where([
                    ['id_document', $undangan->id_undangan],
                    ['jenis_document', 'undangan'],
                    ['id_pengirim', $undangan->pembuat],
                    ['id_penerima', $user],
                    ['status', 'approve'],
                    ['updated_at', now()] // Cek apakah sudah dikirim dalam 5 menit terakhir
                ])->exists();

                if (!$sudahDikirim) {
                    Kirim_Document::create([
                        'id_document' => $undangan->id_undangan,
                        'jenis_document' => 'undangan',
                        'id_pengirim' => $undangan->pembuat,
                        'id_penerima' => $user,
                        'status' => 'approve',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                Notifikasi::create([
                    'judul' => "Undangan Masuk",
                    'judul_document' => $undangan->judul,
                    'id_user' => $user,
                    'updated_at' => now()
                ]);
            }
            // Notifikasi
            Notifikasi::create([
                'judul' => "Undangan Disetujui dan Telah Terkirim",
                'judul_document' => $undangan->judul,
                'id_user' => $undangan->pembuat,
                'updated_at' => now()
            ]);
            // Notifikasi
            Notifikasi::create([
                'judul' => "Undangan Terkirim",
                'judul_document' => $undangan->judul,
                'id_user' => $userId,
                'updated_at' => now()
            ]);
            // Notifikasi

        } elseif ($request->status == 'reject') {
            Notifikasi::create([
                'judul' => "Undangan Tidak Disetujui",
                'judul_document' => $undangan->judul,
                'id_user' => $undangan->pembuat,
                'updated_at' => now()
            ]);
        } elseif ($request->status == 'correction') {
            Notifikasi::create([
                'judul' => "Undangan Perlu Dikoreksi",
                'judul_document' => $undangan->judul,
                'id_user' => $undangan->pembuat,
                'updated_at' => now()
            ]);
        }
        return redirect()->route('undangan.' . Auth::user()->role->nm_role)
            ->with('success', 'Dokumen berhasil dibuat.');
        //return redirect()->route('undangan.manager')->with('success', 'Status undangan berhasil diperbarui.');
    }


    public function updateDocumentApprovalDate(Undangan $undangan)
    {
        if ($undangan->status !== 'pending') {
            $undangan->update(['tanggal_disahkan' => now()]);
        }
    }
    public function approve(Undangan $undangan)
    {
        $undangan->update([
            'status' => 'approve',
            'tanggal_disahkan' => now() // Set tanggal disahkan
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }
    public function reject(Undangan $undangan)
    {
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
        $user = Auth::user();
        $managers = User::where('role_id_role', 3)
            ->where(function ($q) use ($user) {
                $q->where('divisi_id_divisi', $user->divisi_id_divisi)
                    ->orWhere('department_id_department', $user->department_id_department);
                // ->orWhere('section_id_section', $user->section_id_section);
            })
            ->get(['id', 'firstname', 'lastname']);

        $orgTree = $this->getOrgTreeWithUsers();
        $jsTreeDataJson = $this->convertToJsTree($orgTree); // hasilnya string JSON
        $jsTreeData = json_decode($jsTreeDataJson, true);   // decode khusus edit()
        $mainDirector = $orgTree[0] ?? null;

        $tujuanArray = [];
        if (!empty($undangan->tujuan)) {
            $tujuanArray = explode(';', $undangan->tujuan);
        }

        return view(Auth::user()->role->nm_role . '.undangan.edit-undangan', compact(
            'undangan',
            'divisi',
            'seri',
            'managers',
            'tujuanArray',
            'jsTreeData'
        ));
    }
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $undangan = Undangan::findOrFail($id);
        // Bersihkan isi_undangan dari tag HTML & whitespace
        $isiUndanganBersih = trim(strip_tags($request->isi_undangan));

        $request->merge([
            'isi_undangan' => $isiUndanganBersih
        ]);

        // VALIDASI EMOJI DULU SEBELUM VALIDASI LAINNYA
        $emojiErrors = $this->validateNoEmoji($request);
        if (!empty($emojiErrors)) {
            return redirect()->back()
                ->withErrors($emojiErrors)
                ->withInput();
        }
        // Validasi input
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
            'tgl_rapat' => 'required|date',
            'tempat' => 'required|string',
            'waktu_mulai' => 'required|string',
            'waktu_selesai' => 'required|string',
        ]);
        //dd($request->errors());


        if ($request->filled('judul')) {
            $undangan->judul = $request->judul;
        }
        if ($request->filled('isi_undangan')) {
            $undangan->isi_undangan = $request->isi_undangan;
        }
        if ($request->filled('tujuan')) {
            $undangan->tujuan = implode(';', $request->tujuan);
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
        if ($request->filled('tgl_rapat')) {
            $undangan->tgl_rapat = $request->tgl_rapat;
        }
        if ($request->filled('tempat')) {
            $undangan->tempat = $request->tempat;
        }
        if ($request->filled('waktu_mulai')) {
            $undangan->waktu_mulai = $request->waktu_mulai;
        }
        if ($request->filled('waktu_selesai')) {
            $undangan->waktu_selesai = $request->waktu_selesai;
        }


        $undangan->save();
        \Log::info('Update undangan berhasil', $undangan->toArray());
        // Update status pada kirim_document juga jika ada
        \App\Models\Kirim_Document::where('id_document', $undangan->id_undangan)
            ->where('jenis_document', 'undangan')
            ->update(['status' => 'pending', 'updated_at' => now()]);

        return redirect()->route('undangan.' . Auth::user()->role->nm_role)->with('success', 'Undangan updated successfully');
    }
    public function destroy($id)
    {
        $undangan = Undangan::findOrFail($id);

        // Hapus kirim_document terkait
        $undangan->delete();
        Kirim_Document::where('id_document', $id)->where('jenis_document', 'undangan')->delete();
        return redirect()->route('undangan.' . Auth::user()->role->nm_role)->with('success', 'Undangan deleted successfully.');
    }
    public function view($id)
    {
        $userId = Auth::id(); // Ambil ID user yang sedang login
        $undangan = Undangan::where('id_undangan', $id)->firstOrFail();
        $divDeptKode = $this->getDivDeptKode(Auth::user());

        // EDIT: Tambahkan eager loading untuk relationship pembuat
        $undangan = Undangan::with('user')->findOrFail($id);

        // Konversi tujuan ID menjadi array
        $idArray = is_array($undangan->tujuan)
            ? $undangan->tujuan
            : explode(';', $undangan->tujuan);

        $users = User::whereIn('id', $idArray)->with('position')->get();
        $pdfController = new CetakPDFController();
        // Ambil user lengkap beserta relasi organisasi
        $listNama = User::with(['position', 'director', 'divisi', 'department', 'section', 'unit'])
            ->whereIn('id', $idArray)
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

        $undangan->tujuan = $listNama->map(function ($user, $index) {
            return ($index + 1) . '. '
                . $user->position->nm_position . ' '
                . $user->bagian_text . ' '
                . '(' . $user->firstname . ' ' . $user->lastname . ')';
        })->implode("\n");

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

        return view(Auth::user()->role->nm_role . '.undangan.view-undangan', compact('undangan'));
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
        } elseif ($request->status == 'correction') {
            $undangan->tgl_disahkan = now();
        } else {
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
            'id_user' => $undangan->pembuat,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Status undangan berhasil diperbarui.');
    }
}
