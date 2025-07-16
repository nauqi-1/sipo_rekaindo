<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Position;
use App\Models\Director;
use App\Models\Department;
use App\Models\Section;
use App\Models\Unit;

class UserController extends Controller
{
    public function showRole()
    {
        
            $role = Role::all();
            return view('user.role', compact('role'));
    
    }
     // Menampilkan form edit dengan data user
     public function edit($id)
     {
         $user = User::findOrFail($id);
         $divisi = Divisi::all();  
         $roles = Role::all();  
         $positions = Position::all();

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
         
         return view('superadmin.edit', compact('mainDirector','user', 'divisi', 'roles', 'positions'));
     }
 
     // Menangani update data user
     public function update(Request $request, $id)
     {
        $user = User::findOrFail($id);

        $request->validate([
            'firstname' => 'nullable|string|max:50',
            'lastname' => 'nullable|string|max:50',
            'username' => 'nullable|string|max:25',
            'email' => 'nullable|string|email|max:70|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'phone_number' => 'nullable|numeric',
            'role_id_role' => 'nullable|exists:role,id_role',
            'position_id_position' => 'nullable|exists:position,id_position',
            'parent_id' => 'required',
            'parent_type' => 'required',
        ]);

        if ($request->filled('firstname')) {
            $user->firstname = $request->firstname;
        }
        if ($request->filled('lastname')) {
            $user->lastname = $request->lastname;
        }
        if ($request->filled('username')) {
            $user->username = $request->username;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        if ($request->filled('position_id_position')) {
            $user->position_id_position = $request->position_id_position;
        }
        if ($request->filled('role_id_role')) {
            $user->role_id_role = $request->role_id_role;
        }

        $bagian = $request->parent_id;
        $type = $request->parent_type;
        // dd($jabatan);
        
        if($type == "director"){ // Direktur
            $user->director_id_director = $bagian;
            $user->divisi_id_divisi = NULL;
            $user->department_id_department = NULL;
            $user->section_id_section = NULL;
            $user->unit_id_unit = NULL;
        } elseif ($type == "divisi") { // Divisi
            $director = Divisi::where('id_divisi', $bagian)->value('director_id_director');
            $user->divisi_id_divisi = $bagian;
            $user->department_id_department = NULL;
            $user->section_id_section = NULL;
            $user->unit_id_unit = NULL;
        } elseif ($type == "department") { // Department
            $user->director_id_director = Department::where('id_department', $bagian)->value('director_id_director');
            $user->divisi_id_divisi = Department::where('id_department', $bagian)->value('divisi_id_divisi') ?? NULL;
            $user->department_id_department = $bagian;
            $user->section_id_section = NULL;
            $user->unit_id_unit = NULL;
        } elseif ($type == "section") { // Section
            $user->department_id_department = Section::where('id_section', $bagian)->value('department_id_department');
            $user->director_id_director = Department::where('id_department', $user->department_id_department)->value('director_id_director');
            $user->divisi_id_divisi = Department::where('id_department', $user->department_id_department)->value('divisi_id_divisi') ?? NULL;
            $user->section_id_section = $bagian;
            $user->unit_id_unit = NULL;
        } elseif ($type == "unit") { //Unit
            $user->department_id_department = Unit::where('id_unit', $bagian)->value('department_id_department') ?? NULL;
            $user->section_id_section = Unit::where('id_unit', $bagian)->value('section_id_section') ?? NULL;
            $user->director_id_director = Department::where('id_department', $user->department_id_department)->value('director_id_director');
            $user->divisi_id_divisi = Department::where('id_department', $user->department_id_department)->value('divisi_id_divisi') ?? NULL;
            $user->unit_id_unit = $bagian;
        }

        $user->save();
 
         return redirect()->route('user.manage')->with('success', 'User updated successfully');
     }
     public function destroy($id)
     {
         $user = User::find($id);
         if (!$user) {
             return response()->json(['error' => 'User tidak ditemukan'], 404);
         }
 
         $user->delete();
 
         return response()->json(['success' => 'User berhasil dihapus'], 200);
     }

}
