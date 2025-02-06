<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Divisi;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;

class UserManageController extends Controller
{
    public function index($id = null)
{
    // Ambil data dari Divisi, Role, dan Position
    $divisi = Divisi::all();  
    $roles = Role::all();  
    $positions = Position::all();  
    $users = User::with(['role', 'divisi', 'position'])->paginate(6);

    if (request('search')) {
        $users = User::where('firstname', 'like', '%' . request('search') . '%')->orWhere('lastname', 'like', '%' . request('search') . '%')->paginate(6);
    }
    // Kirim data ke view user-manage
    return view('superadmin.user-manage', compact('divisi', 'roles', 'positions', 'users'));
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
        $users = User::orderBy('firstname', $sortOrder)->get();

        // Mengirim data user ke view
        return view('user.index', compact('users', 'sortOrder'));
    }



}
