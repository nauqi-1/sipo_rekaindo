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
         $divisi = Divisi::all(); // Asumsi Anda memiliki model Divisi
         $positions = Position::all(); // Asumsi Anda memiliki model Position
         $roles = Role::all(); // Asumsi Anda memiliki model Role
         
         return view('user-manage.edit', compact('user', 'divisi', 'positions', 'roles'));
     }
 
     // Menangani update data user
     public function update(Request $request, $id)
     {
         $request->validate([
             'id' => 'required',
             'email' => 'required|email',
             'firstname' => 'required',
             'lastname' => 'required',
             'username' => 'required',
             'phone_number' => 'required',
             'password' => 'nullable|confirmed',
             'divisi' => 'required',
             'position' => 'required',
             'role' => 'required',
         ]);
 
         $user = User::findOrFail($id);
         $user->id = $request->id;
         $user->email = $request->email;
         $user->firstname = $request->firstname;
         $user->lastname = $request->lastname;
         $user->username = $request->username;
         $user->phone_number = $request->phone_number;
 
         if ($request->password) {
             $user->password = bcrypt($request->password);
         }
 
         $user->divisi_id_divisi = $request->divisi_id_divisi;
         $user->position_id_position = $request->position_id_position;
         $user->role_id_role = $request->role_id_role;
         $user->save();
 
         return redirect()->route('user-manage.index')->with('success', 'User updated successfully');
     }
}
