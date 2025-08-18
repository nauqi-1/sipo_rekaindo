<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\kategori_barang;
use App\Models\Memo;
use App\Models\Seri;
use App\Models\Arsip;
use App\Models\User;
use App\Models\Unit;
use App\Models\Section;
use App\Models\Divisi;
use App\Models\Notifikasi;
use App\Models\Kirim_Document;
use App\Models\Backup_Document;
use App\Models\Director;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MemoController extends Controller
{
    public function index(Request $request)
    {   
        $divisi = Divisi::all();
        $seri = Seri::all();
        $user = User::all();
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
            ->whereNotIn('id_memo', $memoDiarsipkan); // Filter memo yang belum diarsipkan

        // Filter by self (own/other/both) if requested
        // Advanced filter: 3 types
        // 1. both: memo milik sendiri dan kiriman orang lain
        // 2. own: memo yang dibuat diri sendiri saja
        // 3. received: memo yang dibuat orang lain saja

        $filterType = $request->get('divisi_filter', 'both');

        if ($filterType === 'own') {
            // Only memos where current user is the sender
            $query->whereHas('kirimDocument', function ($q) use ($userId) {
                $q->where('id_pengirim', $userId)
                    ->where('jenis_document', 'memo')
                    ->whereHas('memo', function ($q) {
                        $q->where('kode', $this->getDivDeptKode(Auth::user()));
                    });
            });
        } elseif ($filterType === 'received' || $filterType === 'other') {
            // Only memos received by current user
            $query->whereHas('kirimDocument', function ($q) use ($userId) {
                $q->where('id_penerima', $userId)
                    ->where('jenis_document', 'memo')
                    //->whereHas('memo', function ($q) {
                    //    $q->where('kode', '!=', $this->getDivDeptKode(Auth::user()));
                    //})
                ;
            });
        } else {
            // Both sent and received memos by the user
            $query->whereHas('kirimDocument', function ($q) use ($userId) {
                $q->where(function ($subQ) use ($userId) {
                    $subQ->where('id_pengirim', $userId)
                        ->orWhere('id_penerima', $userId);
                })->where('jenis_document', 'memo');
            });
        }



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

            $creator = \App\Models\User::where('id', $userId)
                ->first();
            if ($creator && $creator->id === $userId) {
                $memo->final_status = $memo->status; // Memo diri sendiri
            } else {
                $statusKirim = Kirim_Document::where('id_document', $memo->id_memo)
                    ->where('jenis_document', 'memo')
                    ->where('id_penerima', $userId)
                    ->first();
                $memo->final_status = $statusKirim ? $statusKirim->status : '-';
                // Cari status kiriman untuk user login
            }
            return $memo;
        });

        // Ambil id penerima dan pengirim melalui relasi user
        $kirimDocuments = Kirim_Document::where('jenis_document', 'memo')
            ->where(function ($query) use ($userId) {
                $query->where('id_pengirim', $userId)
                    ->orWhere('id_penerima', $userId);
            })
            ->with('memo') // eager-load related memo
            ->orderBy('id_kirim_document', 'desc')
            ->get();
        return view(Auth::user()->role->nm_role . '.memo.memo-' . Auth::user()->role->nm_role, compact('memos', 'divisi', 'seri', 'sortDirection', 'kirimDocuments'));
    }

    public function superadmin(Request $request)
    {
        $divisi = Divisi::all();
        $kode = Memo::whereNotNull('kode')
            ->pluck('kode')
            ->filter()
            ->unique()
            ->values();
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

        if ($request->filled('kode') && $request->kode != 'pilih') {
            $query->where('kode', $request->kode);
        }

        // Pencarian berdasarkan nama dokumen atau nomor memo
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('nomor_memo', 'like', '%' . $request->search . '%');
            });
        }
        $perPage = $request->get('per_page', 10);
        $memos = $query->paginate($perPage);


        return view('superadmin.memo.memo-superadmin', compact('memos', 'divisi', 'kode', 'seri', 'sortDirection'));
    }

    public function show($id)
    {
        $userId = Auth::id();
        $memo = Memo::with('divisi')->findOrFail($id);

        $memo->getCollection()->transform(function ($memo) use ($userId) {
            if ($memo->divisi_id_divisi === Auth::user()->divisi_id_divisi) {
                $memo->final_status = $memo->status; // Memo dari divisi sendiri
            } else {
                $statusKirim = Kirim_Document::where('id_document', $memo->id_memo)
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
        //$divisiId = auth()->user()->divisi_id_divisi;

        //$divisiName = auth()->user()->divisi->nm_divisi;
        $divisiList = Divisi::all();

        $user = Auth::user();
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
            "%d.%d/REKA%s/GEN/%s/%s/%d",
            $nextSeri['seri_tahunan'],
            $nextSeri['seri_bulanan'],
            strtoupper($kodeDirektur),
            strtoupper($divDeptKode),
            $bulanRomawi,
            now()->year
        );
        // Daftar manager yang satu divisi, department, section, dan unit dengan admin yg membuat suratnya
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
        } else {
            $managers = User::with('position:id_position,nm_position')
                ->where('role_id_role', 3)
                ->where('director_id_director', $user->director_id_director)
                ->get(['id', 'firstname', 'lastname', 'position_id_position']);
        }

        // Ambil seluruh user dan struktur organisasi (untuk dropdown tree)
        $users = User::select('id', 'firstname', 'lastname', 'divisi_id_divisi', 'department_id_department', 'section_id_section', 'unit_id_unit')->get();
        $orgTree = $this->getOrgTreeWithUsers();
        $jsTreeData = $this->convertToJsTree($orgTree);
        $mainDirector = $orgTree[0] ?? null; // assuming the first node is the main director
        return view(Auth::user()->role->nm_role . '.memo.add-memo', [
            'nomorSeriTahunan' => $nextSeri['seri_tahunan'],
            'nomorDokumen' => $nomorDokumen,
            'managers' => $managers,
            'divisiList' => $divisiList,
            'users' => $users,
            'orgTree' => $orgTree,
            'jsTreeData' => $jsTreeData,
            'mainDirector' => $mainDirector,
        ]);
    }
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
                'children' => [],
                'state' => ['disabled' => true], // matikan root nodes
                'checkbox_disabled' => true // disable checkbox for director nodes
            ];

            // Filter user hanya yang di level director saja
            $dirNode['children'] = [];
            $usersAtDirector = $this->filterUsersAtLevel($director['users'], 'director');
            foreach ($usersAtDirector as $user) {
                $dirNode['children'][] = [
                    'id' => 'user-' . $user['id'],
                    'text' => $this->getUserText($user, ['director' => $director['name_director']]),
                    'icon' => 'fa fa-user'
                ];
            }

            // Tanpa Divisi (langsung Department)
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
                // Dengan Divisi
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

    public function getDivDeptKode($user)
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
        if (empty($text))
            return false;

        // Regex pattern untuk detect emoji
        $emojiPattern = '/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u';

        // Pattern tambahan untuk emoji lainnya
        $additionalEmojiPattern = '/[\x{1F900}-\x{1F9FF}]|[\x{1FA70}-\x{1FAFF}]|[\x{1F780}-\x{1F7FF}]|[\x{1F800}-\x{1F8FF}]/u';

        return preg_match($emojiPattern, $text) || preg_match($additionalEmojiPattern, $text);
    }
    private function validateNoEmoji($request)
    {
        // Fields you want to check
        $fieldsToCheck = ['judul', 'isi_memo', 'barang', 'satuan', 'barang*', 'satuan*'];
        $errors = [];

        foreach ($fieldsToCheck as $field) {
            if ($request->has($field)) {
                $value = $request->input($field);

                // If it's an array (like barang[]), loop through each value
                if (is_array($value)) {
                    foreach ($value as $index => $item) {
                        if ($this->containsEmoji($item)) {
                            $fieldName = $this->getFieldDisplayName($field);
                            $errors["{$field}.{$index}"] = "Kolom {$fieldName} nomor " . ($index + 1) . " tidak boleh mengandung emoji.";
                        }
                    }
                }
                // If it's a single string, check directly
                else {
                    if ($this->containsEmoji($value)) {
                        $fieldName = $this->getFieldDisplayName($field);
                        $errors[$field] = "Kolom {$fieldName} tidak boleh mengandung emoji.";
                    }
                }
            }
        }

        return $errors;
    }


    private function getFieldDisplayName($field)
    {
        $names = [
            'judul' => 'perihal',
            'isi_memo' => 'isi memo',  // Sesuai label yang user lihat
            'barang' => 'barang',
            'satuan' => 'satuan',
            'barang*' => 'barang',
            'satuan*' => 'satuan'
        ];

        return $names[$field] ?? ucfirst($field);
    }
    public function store(Request $request)
    {
        $emojiErrors = $this->validateNoEmoji($request);
        if (!empty($emojiErrors)) {
            return redirect()->back()
                ->withErrors($emojiErrors)
                ->withInput();
        }
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'isi_memo' => [
                'required',
                function ($attribute, $value, $fail) {
                    $clean = strip_tags($value);

                    $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    $clean = preg_replace('/\xc2\xa0|\s+/u', '', $clean);
                    if ($clean === '') {
                        $fail('Isi memo tidak boleh kosong.');
                    }
                }
            ],
            'tujuan' => 'required|array|min:1',
            'tujuanString' => 'required|array|min:1',
            'nomor_memo' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'manager_user_id' => 'required|exists:users,id',
            'pembuat' => 'required|string|max:255',
            'catatan' => 'nullable|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'nullable',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max
            'barang' => 'sometimes|required|array',
            'barang.*' => 'required|string', // each item required if array exists

            'qty' => 'sometimes|required|array',
            'qty.*' => 'required|numeric|min:1',

            'satuan' => 'sometimes|required|array',
            'satuan.*' => 'required|string'
        ], [
            'lampiran.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
            'lampiran.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
            'barang.required' => 'Nama barang harus diisi.',
            'qty.required' => 'Qty barang harus diisi.',
            'satuan.required' => 'Satuan barang harus diisi.',

            'barang.*.required' => 'Nama barang harus diisi.',
            'qty.*.required' => 'Qty barang harus diisi.',
            'satuan.*.required' => 'Satuan barang harus diisi.'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tujuanId = $this->convertTujuanToUserId($request->tujuan);
        $filePath = null;
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $fileData = base64_encode(file_get_contents($file->getRealPath()));
            $filePath = $fileData;
        }

        $divDeptKode = $this->getDivDeptKode(Auth::user());



        $seri = Seri::where('kode', $divDeptKode)
            ->where('tahun', now()->year)
            ->latest()
            ->first();
        $seri = Seri::getNextSeri(true);
        if (!$seri) {
            return back()->with('error', 'Nomor seri tidak ditemukan.');
        }

        // Simpan dokumen
        $memo = Memo::create([
            'judul' => $request->input('judul'),
            'tujuan' => implode(';', $tujuanId),
            'tujuan_string' => implode(';', $request->input('tujuanString')),
            'isi_memo' => $request->input('isi_memo'),
            'nomor_memo' => $request->input('nomor_memo'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'kode' => $divDeptKode,
            'pembuat' => $request->input('pembuat'),
            'catatan' => $request->input('catatan'),
            'seri_surat' => $request->input('seri_surat'),
            'status' => 'pending',
            'nama_bertandatangan' => $request->input('nama_bertandatangan'),
            'lampiran' => $filePath,

        ]);
        if ($request->has('jumlah_kolom') && !empty($request->nomor)) {
            foreach ($request->nomor as $key => $nomor) {
                kategori_barang::create([
                    'memo_id_memo' => $memo->id_memo,
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

        if ($creator->role_id_role == 2) {
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
                Notifikasi::create([
                    'judul' => "Memo Dalam Proses Persetujuan",
                    'judul_document' => $memo->judul,
                    'id_user' => $memo->pembuat,
                    'updated_at' => now()
                ]);
                Notifikasi::create([
                    'judul' => "Memo Menunggu Persetujuan",
                    'judul_document' => $memo->judul,
                    'id_user' => $manager->id,
                    'updated_at' => now()
                ]);
                if ($kirim) {
                    $sentCount++;
                }
            }
        } elseif ($creator->role_id_role == 3) {

            $kirim = Kirim_document::create([
                'id_document' => $memo->id_memo,
                'jenis_document' => 'memo',
                'id_pengirim' => $creator->id,
                'id_penerima' => $creator->id,
                'status' => 'approve',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Notifikasi::create([
                'judul' => "Memo Terkirim",
                'judul_document' => $memo->judul,
                'id_user' => $memo->pembuat,
                'updated_at' => now()
            ]);
            $memo->status = 'approve';
            $memo->tgl_disahkan = now();
            $qrText = "Disetujui oleh: " . Auth::user()->firstname . ' ' . Auth::user()->lastname . "\nTanggal: " . now()->translatedFormat('l, d F Y');
            $qrImage = QrCode::format('svg')->generate($qrText);
            $qrBase64 = base64_encode($qrImage);
            $memo->qr_approved_by = $qrBase64;
            $memo->save();

            $tujuanUserIds = is_array($memo->tujuan) ? $memo->tujuan : explode(';', $memo->tujuan);
            //dd($tujuanUserIds);
            foreach ($tujuanUserIds as $userId) {
                if ($userId == $creator->id)
                    continue;
                $recipients = User::where('id', $userId)->get();
                foreach ($recipients as $recipient) {
                    Kirim_document::create([
                        'id_document' => $memo->id_memo,
                        'jenis_document' => 'memo',
                        'id_pengirim' => $creator->id,
                        'id_penerima' => $recipient->id,
                        'status' => 'approve',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Notifikasi::create([
                        'judul' => "Memo Masuk",
                        'judul_document' => $memo->judul,
                        'id_user' => $recipient->id,
                        'updated_at' => now()
                    ]);
                }
            }
        }

        if (Auth::user()->role_id_role == 2) {
            return redirect()->route('memo.' . Auth::user()->role->nm_role)->with('success', 'Dokumen berhasil dibuat.');
        } else {
            return redirect()->route('memo.terkirim')->with('success', 'Dokumen berhasil dibuat.');
        }
    }

    public function convertTujuanToUserId(array $rawTujuan)
    {

        $departments = [];
        $sections = [];
        $divisions = [];
        $units = [];
        $users = [];

        foreach ($rawTujuan as $item) {
            if (Str::startsWith($item, 'dept-')) {
                $departments[] = (int) Str::after($item, 'dept-');
            } elseif (Str::startsWith($item, 'section-')) {
                $sections[] = (int) Str::after($item, 'section-');
            } elseif (Str::startsWith($item, 'divisi-')) {
                $divisions[] = (int) Str::after($item, 'divisi-');
            } elseif (Str::startsWith($item, 'unit-')) {
                $units[] = (int) Str::after($item, 'unit-');
            } elseif (Str::startsWith($item, 'user-')) {
                $users[] = (int) Str::after($item, 'user-');
            }
        }

        // Now query the users who match any of the IDs
        $users = User::where(function ($query) use ($departments, $sections, $divisions, $units, $users) {
            if (!empty($departments)) {
                $query->orWhereIn('department_id_department', $departments);
            }
            if (!empty($sections)) {
                $query->orWhereIn('section_id_section', $sections);
            }
            if (!empty($divisions)) {
                $query->orWhereIn('divisi_id_divisi', $divisions);
            }
            if (!empty($units)) {
                $query->orWhereIn('unit_id_unit', $units);
            }
            if (!empty($users)) {
                $query->orWhereIn('id', $users);
            }
        })->pluck('id')->toArray();

        // Final tujuan result:
        $tujuanId = $users;
        return $tujuanId;
    }

    //fungsi untuk ngubah daftar penerima dari per-user jadi per hierarki, public supaya bisa dipake di controller CetakMemoPDF juga
    public function simplifyRecipients($tujuanString)
    {
        $userIds = explode(';', $tujuanString);
        $userIds = array_filter($userIds); // remove empty

        // Group user IDs by unit
        $units = DB::table('users')
            ->whereIn('id', $userIds)
            ->get()
            ->groupBy('unit_id');

        $selectedUnitIds = [];
        $remainingUserIds = [];

        foreach ($units as $unitId => $usersInUnit) {
            $totalUsersInUnit = DB::table('users')->where('unit_id', $unitId)->count();

            if (count($usersInUnit) == $totalUsersInUnit) {
                $selectedUnitIds[] = $unitId;
            } else {
                $remainingUserIds = array_merge($remainingUserIds, $usersInUnit->pluck('id')->toArray());
            }
        }

        // Now group selected unit IDs by section
        $sections = DB::table('units')
            ->whereIn('id', $selectedUnitIds)
            ->get()
            ->groupBy('section_id');

        $selectedSectionIds = [];
        $remainingUnitIds = [];

        foreach ($sections as $sectionId => $unitsInSection) {
            $totalUnitsInSection = DB::table('units')->where('section_id', $sectionId)->count();

            if (count($unitsInSection) == $totalUnitsInSection) {
                $selectedSectionIds[] = $sectionId;
            } else {
                $remainingUnitIds = array_merge($remainingUnitIds, $unitsInSection->pluck('id')->toArray());
            }
        }

        // Now group selected section IDs by department
        $departments = DB::table('section')
            ->whereIn('id', $selectedSectionIds)
            ->get()
            ->groupBy('department_id_department');

        $selectedDepartmentIds = [];
        $remainingSectionIds = [];

        foreach ($departments as $departmentId => $sectionsInDept) {
            $totalSectionsInDept = DB::table('section')->where('department_id_department', $departmentId)->count();

            if (count($sectionsInDept) == $totalSectionsInDept) {
                $selectedDepartmentIds[] = $departmentId;
            } else {
                $remainingSectionIds = array_merge($remainingSectionIds, $sectionsInDept->pluck('id')->toArray());
            }
        }

        // Now group selected departments by divisi
        $divisis = DB::table('departments')
            ->whereIn('id', $selectedDepartmentIds)
            ->get()
            ->groupBy('divisi_id');

        $selectedDivisiIds = [];
        $remainingDepartmentIds = [];

        foreach ($divisis as $divisiId => $departmentsInDiv) {
            $totalDeptsInDiv = DB::table('department')->where('divisi_id_divisi', $divisiId)->count();

            if (count($departmentsInDiv) == $totalDeptsInDiv) {
                $selectedDivisiIds[] = $divisiId;
            } else {
                $remainingDepartmentIds = array_merge($remainingDepartmentIds, $departmentsInDiv->pluck('id')->toArray());
            }
        }

        return [
            'divisi' => $selectedDivisiIds,
            'departments' => $remainingDepartmentIds,
            'sections' => $remainingSectionIds,
            'units' => $remainingUnitIds,
            'users' => $remainingUserIds,
        ];
    }
    public function collapseRecipients2(array $selectedUserIds)
    {
        $selected = collect($selectedUserIds)->map(fn($id) => (int) $id);

        // Start from users and move up
        $collapsed = $this->collapseAtLevel($selected, 'unit_id_unit', 'unit');
        $collapsed = $this->collapseAtLevel($collapsed, 'section_id_section', 'section');
        $collapsed = $this->collapseAtLevel($collapsed, 'department_id_department', 'department');
        $collapsed = $this->collapseAtLevel($collapsed, 'divisi_id_divisi', 'divisi');

        return $collapsed->implode(';'); // return string
    }

    protected function collapseAtLevel2(Collection $items, string $parentKey, string $table)
    {
        // Group by parent
        $grouped = DB::table('users')
            ->whereIn('id', $items)
            ->get()
            ->groupBy($parentKey);

        $collapsed = collect();

        foreach ($grouped as $parentId => $children) {
            $allUserIds = DB::table('users')
                ->where($parentKey, $parentId)
                ->pluck('id');

            $selectedIds = $children->pluck('id');

            if ($selectedIds->sort()->values()->all() === $allUserIds->sort()->values()->all()) {
                // All children under this parent are selected → collapse
                $collapsed->push($parentKey . ':' . $parentId);
            } else {
                $collapsed = $collapsed->merge($selectedIds);
            }
        }

        return $collapsed;
    }

    public function collapseRecipients3(array $selectedUserIds)
    {
        $selected = collect($selectedUserIds)->map(fn($id) => (int) $id);

        // Load all user data with parent info
        $users = DB::table('users')
            ->whereIn('id', $selected)
            ->get(['id', 'unit_id_unit', 'section_id_section', 'department_id_department', 'divisi_id_divisi']);

        // Collapse upward
        $result = $this->collapseAtLevel($users, 'unit_id_unit', 'users');
        $result = $this->collapseAtLevel($result, 'section_id_section', 'users');
        $result = $this->collapseAtLevel($result, 'department_id_department', 'users');
        $result = $this->collapseAtLevel($result, 'divisi_id_divisi', 'users');

        // Final clean-up: extract IDs or tags like "unit:5"
        return $result->map(fn($item) => is_array($item) ? "{$item['level']}:{$item['id']}" : $item)->implode(';');
    }

    protected function collapseAtLevel3($items, $levelKey, $userTable)
    {
        $grouped = collect();

        foreach ($items as $item) {
            // item can be user_id (int) or ['level' => ..., 'id' => ...]
            if (is_int($item)) {
                $user = DB::table($userTable)->where('id', $item)->first([$levelKey, 'id']);
                $grouped[$user->$levelKey][] = $user->id;
            } else {
                // Already collapsed higher — pass through
                $grouped[] = $item;
            }
        }

        $collapsed = collect();

        foreach ($grouped as $parentId => $userIds) {
            if (is_numeric($parentId)) {
                $allUsersUnderParent = DB::table($userTable)->where($levelKey, $parentId)->pluck('id')->all();

                sort($allUsersUnderParent);
                sort($userIds);

                if ($userIds == $allUsersUnderParent) {
                    // All users selected — collapse
                    $collapsed->push(['level' => str_replace('_id', '', $levelKey), 'id' => $parentId]);
                } else {
                    $collapsed = $collapsed->merge($userIds);
                }
            } else {
                // This is already collapsed at a higher level
                $collapsed->push($parentId);
            }
        }

        return $collapsed;
    }
    protected function collapseHierarchies($selectedUserIds)
    {
        $levels = [
            'unit_id_unit' => 'unit',
            'section_id_section' => 'section',
            'department_id_department' => 'department',
            'divisi_id_divisi' => 'divisi',
        ];

        $userTable = 'users';
        $selected = collect($selectedUserIds); // These are user IDs (integers)

        // Step 1: Collapse at each level progressively from bottom to top
        foreach ($levels as $levelKey => $levelName) {
            $selected = $this->collapseAtLevel($selected, $levelKey, $userTable);
        }

        return $selected;
    }
    protected function collapseAtLevel($items, $levelKey, $userTable)
    {
        $grouped = collect();

        foreach ($items as $item) {
            if (is_int($item)) {
                $user = DB::table($userTable)->where('id', $item)->first([$levelKey, 'id']);
                if ($user && $user->$levelKey !== null) {
                    $grouped[$user->$levelKey][] = $user->id;
                }
            } elseif (is_array($item) && isset($item['level'], $item['id'])) {
                // Already collapsed at higher level, just push as-is
                $grouped[] = $item;
            }
        }

        $collapsed = collect();

        foreach ($grouped as $parentId => $userIds) {
            if (is_numeric($parentId)) {
                $allUsersUnderParent = DB::table($userTable)->where($levelKey, $parentId)->pluck('id')->all();

                // Sort both arrays for accurate comparison
                sort($allUsersUnderParent);
                sort($userIds);

                if ($userIds == $allUsersUnderParent) {
                    $collapsed->push([
                        'level' => str_replace(['_id_', '_id'], '', $levelKey), // handles `unit_id_unit` etc.
                        'id' => $parentId
                    ]);
                } else {
                    $collapsed = $collapsed->merge($userIds);
                }
            } else {
                $collapsed->push($userIds); // $userIds is actually a collapsed object here
            }
        }

        return $collapsed;
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
    public function updateDocumentStatus(Memo $memo)
    {
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

    public function updateDocumentApprovalDate(Memo $memo)
    {
        if ($memo->status !== 'pending') {
            $memo->update(['tanggal_disahkan' => now()]);
        }
    }
    public function updateStatus(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);
        $userDivDeptKode = $this->getDivDeptKode(Auth::user());
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


        if ($userDivDeptKode == $memo->kode) {
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

                    // Kirim otomatis ke tujuan jika status approve
                    $tujuanUserIds = is_array($memo->tujuan) ? $memo->tujuan : explode(';', $memo->tujuan);

                    foreach ($tujuanUserIds as $userId) {
                        $userId = trim($userId);

                        // Lewati jika sama dengan divisi pengirim
                        if ($userId == Auth::user()->id) {
                            continue;
                        }
                        // SETELAH DI APPROVE MANAGER DIVISI SENDIRI, LANGSUNG KIRIM KE SEMUA USER DI TUJUAN DENGAN STATUS APPROVE
                        // Ambil semua user di divisi terkait
                        $penerima = \App\Models\User::where('id', $userId)
                            ->get();

                        foreach ($penerima as $penerima) {
                            if (Auth::user()->position_id_position)
                                \App\Models\Kirim_Document::create([
                                    'id_document' => $memo->id_memo,
                                    'jenis_document' => 'memo',
                                    'id_pengirim' => $currentKirim->id_pengirim,
                                    'id_penerima' => $penerima->id,
                                    'status' => 'approve',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            Notifikasi::create([
                                'judul' => "Memo Masuk",
                                'judul_document' => $memo->judul,
                                'id_user' => $penerima->id,
                                'updated_at' => now()
                            ]);
                        }
                    }



                    Notifikasi::create([
                        'judul' => "Memo Disetujui",
                        'judul_document' => $memo->judul,
                        'id_user' => $memo->pembuat,
                        'updated_at' => now()
                    ]);
                } elseif ($request->status == 'reject') {
                    $memo->tgl_disahkan = now();
                    Notifikasi::create([
                        'judul' => "Memo Ditolak",
                        'judul_document' => $memo->judul,
                        'id_user' => $memo->pembuat,
                        'updated_at' => now()
                    ]);
                } elseif ($request->status == 'correction') {
                    Notifikasi::create([
                        'judul' => "Memo Perlu Revisi",
                        'judul_document' => $memo->judul,
                        'id_user' => $memo->pembuat,
                        'updated_at' => now()
                    ]);
                } else {
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
                if (($request->status == 'approve')) {
                    Notifikasi::create([
                        'judul' => "Memo Ditindak Lanjuti",
                        'judul_document' => $memo->judul,
                        'id_divisi' => $memo->divisi_id_divisi,
                        'updated_at' => now()
                    ]);

                    $adminDivisiId = Auth::user()->divisi_id_divisi;

                    $admins = \App\Models\User::where('divisi_id_divisi', $adminDivisiId)
                        ->where('position_id_position', 1)
                        ->get();

                    foreach ($admins as $admin) {
                        \App\Models\Kirim_Document::create([
                            'id_document' => $memo->id_memo,
                            'jenis_document' => 'memo',
                            'id_pengirim' => $currentKirim->id_pengirim,
                            'id_penerima' => $admin->id,
                            'status' => 'approve',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
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



        return redirect()->back()->with('success', 'Status memo berhasil diperbarui.');
    }

    public function edit($id)
    {
        $memo = Memo::findOrFail($id);
        $divisi = Divisi::all();
        $divisiId = auth()->user()->divisi_id_divisi;
        $seri = Seri::all();


        $managers = User::where('role_id_role', 3)
            ->get(['id', 'firstname', 'lastname']);

        $orgTree = $this->getOrgTreeWithUsers();
        $jsTreeData = $this->convertToJsTree($orgTree);
        $mainDirector = $orgTree[0] ?? null;

        $tujuanArray = $memo->tujuan_string ? explode(';', $memo->tujuan_string) : [];
        return view(Auth::user()->role->nm_role . '.memo.edit-memo', compact('memo', 'divisi', 'seri', 'managers', 'orgTree', 'jsTreeData', 'mainDirector', 'tujuanArray'));
    }
    public function update(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);
        $emojiErrors = $this->validateNoEmoji($request);
        if (!empty($emojiErrors)) {
            return redirect()->back()
                ->withErrors($emojiErrors)
                ->withInput();
        }
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi_memo' => [
                'required',
                function ($attribute, $value, $fail) {
                    $clean = strip_tags($value);

                    $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    $clean = preg_replace('/\xc2\xa0|\s+/u', '', $clean);
                    if ($clean === '') {
                        $fail('Isi memo tidak boleh kosong.');
                    }
                }
            ],
            'tujuan' => 'required|array|min:1',
            'tujuanString' => 'required|array|min:1',
            'nomor_memo' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'kategori_barang' => 'sometimes|required|array|min:1',
            'kategori_barang.*.barang' => 'sometimes|required|string',
            'kategori_barang.*.qty' => 'sometimes|required|integer|min:1',
            'kategori_barang.*.satuan' => 'sometimes|required|string',
        ], [
            'kategori_barang.*.barang.required' => 'Nama barang harus diisi.',
            'kategori_barang.*.qty.required' => 'Qty barang harus diisi.',
            'kategori_barang.*.satuan.required' => 'Satuan barang harus diisi.'
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
        if ($request->filled('tujuanString')) {
            $memo->tujuan_string = implode(';', $request->tujuanString);
        }
        if ($request->filled('nomor_memo')) {
            $memo->nomor_memo = $request->nomor_memo;
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
        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $memo->lampiran = file_get_contents($file->getRealPath());
        }

        $memo->status = 'pending'; // Set status ke pending saat update
        $memo->save();

        // Update status pada kirim_document juga jika ada
        \App\Models\Kirim_Document::where('id_document', $memo->id_memo)
            ->where('jenis_document', 'memo')
            ->update(['status' => 'pending', 'updated_at' => now()]);

        if ($request->has('kategori_barang')) {
            foreach ($request->kategori_barang as $dataBarang) {
                if (isset($dataBarang['id_kategori_barang']) && $dataBarang['id_kategori_barang'] != null) {
                    // Cek apakah barang sudah ada di database
                    $barang = $memo->kategoriBarang()->find($dataBarang['id_kategori_barang']);
                    if ($barang) {
                        $barang->update([
                            'memo_id_memo' => $memo->id_memo,
                            'nomor' => $dataBarang['nomor'],
                            'barang' => $dataBarang['barang'],
                            'qty' => $dataBarang['qty'],
                            'satuan' => $dataBarang['satuan'],
                        ]);
                    }
                }
            }
        }
        return redirect()->route('memo.' . Auth::user()->role->nm_role)->with('success', 'Memo updated successfully');
    }
    //HAPUS SEMENTARA
    public function delete($id)
    {
        $memo = Memo::findOrFail($id);
        $memo->delete();
        Kirim_Document::where('id_document', $id)->where('jenis_document', 'memo')->delete();
        return redirect()->route('memo.' . Auth::user()->role->nm_role)->with('success', 'Memo berhasil dihapus.');
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
            ->whereHas('memo')
            ->with('memo') // Pastikan ada relasi 'memo' di model Kirim_Document
            ->firstOrFail();
        $memo2 = Memo::where('id_memo', $id)->firstOrFail();
        return view('manager.memo.view-memoDiterima', compact('memo', 'memo2'));
    }
    public function view($id)
    {
        $userId = auth()->id(); // Ambil ID user yang sedang login
        $memo = Memo::where('id_memo', $id)->firstOrFail();
        // get kode divisi/ department
        $divDeptKode = $this->getDivDeptKode(Auth::user());
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

        return view(Auth::user()->role->nm_role . '.memo.view-memo', compact('memo', 'divDeptKode'));
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
            'dibaca' => false,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Status memo berhasil diperbarui.');
    }
}
