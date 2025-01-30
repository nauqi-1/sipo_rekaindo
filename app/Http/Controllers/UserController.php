<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Position;

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
         
         return view('superadmin.edit', compact('user', 'divisi', 'roles', 'positions'));
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
        'divisi_id_divisi' => 'nullable|exists:divisi,id_divisi',
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
    if ($request->filled('divisi_id_divisi')) {
        $user->divisi_id_divisi = $request->divisi_id_divisi;
    }
    if ($request->filled('position_id_position')) {
        $user->position_id_position = $request->position_id_position;
    }
    if ($request->filled('role_id_role')) {
        $user->role_id_role = $request->role_id_role;
    }

    $user->save();
 
         return redirect()->route('user.manage')->with('success', 'User updated successfully');
     }
     public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.manage')->with('success', 'User deleted successfully.');
    }
}
