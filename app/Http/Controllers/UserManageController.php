<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Divisi;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use App\Models\Unit;
use App\Models\Director;
use App\Models\Section;

class UserManageController extends Controller
{
    public function index(Request $request, $id = null)
    {
        // Ambil data dari Divisi, Role, dan Position
        $divisi = Divisi::all();  
        $roles = Role::all();  
        $positions = Position::all();

        // Mengambil parameter sorting, default 'asc'
        $sortOrder = $request->query('sort', 'asc');

        // Query awal dengan relasi yang diperlukan
        $users = User::with(['role', 'divisi', 'position']);

        // Jika ada pencarian, tambahkan kondisi pencarian
        if ($request->filled('search')) {
            $users->where(function ($query) use ($request) {
                $query->where('firstname', 'like', '%' . $request->search . '%')
                      ->orWhere('lastname', 'like', '%' . $request->search . '%');
            });
        }

        // Jika ada parameter sorting, lakukan pengurutan berdasarkan firstname
        $users->orderBy('firstname', $sortOrder);

        // Paginate hasil query
        $perPage = $request->get('per_page', 10); // Default ke 10 jika tidak ada input
        $users = $users->paginate($perPage);

        $mainDirector = Director::with([
            'subDirectors.divisi.department.section.unit',
            'subDirectors.divisi.department.unit',
            'subDirectors.department.section.unit',
            'subDirectors.department.unit',
            'divisi.department.section.unit',
            'divisi.department.unit',
            'department.section.unit',
            'department.unit'
        ])->where('is_main', 1)->first();

        // Kirim data ke view user-manage
        return view('superadmin.user-manage', compact('divisi', 'roles', 'positions', 'users', 'sortOrder', 'mainDirector'));
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:15',
            'divisi_id_divisi' => 'required|exists:divis,id_divisi',
            'position_id_position' => 'required|exists:position,id_position',
            'role_id_role' => 'required|exists:role,id_role',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'divisi_id_divisi' => $request->divisi_id_divisi,
            'position_id_position' => $request->position_id_position,
            'role_id_role' => $request->role_id_role,
        ]);

        
        return redirect()->route('user-manage')->with('success', 'User added successfully.');
        }
    
        public function filter(Request $request)
        {
            // Mendapatkan parameter sorting dari request
            $sortOrder = $request->query('sort', 'asc');

            // Melakukan query ke database dengan pengurutan berdasarkan firstname
            $users = User::orderBy('firstname', $sortOrder)->paginate(6);

            // Mengirim data user ke view
            return view('superadmin.user-manage', compact('users', 'sortOrder'));
        }

        
        
    }
