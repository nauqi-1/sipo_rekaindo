<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * 
     */
    public function store(Request $request): RedirectResponse
    {
        // if (Auth::user()->role !== 'superadmin') {
        //     return redirect()->route('user.manage')->with('error', 'You do not have permission to add users.');
        // }
        // dd($request->all());
        $request->validate([
            'id'=>'required|integer|unique:users',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'username' => 'required|string|max:25',
            'email' => 'required|string|email|max:70|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone_number' => 'required|numeric',
            'role_id_role' => 'required|exists:role,id_role',
            'position_id_position' => 'required|exists:position,id_position',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
        ],[
            'firstname.required' => 'Nama depan wajib diisi.',
            'firstname.max' => 'Nama depan tidak boleh lebih dari 50 karakter.',
            
            'lastname.required' => 'Nama belakang wajib diisi.',
            'lastname.max' => 'Nama belakang tidak boleh lebih dari 50 karakter.',
            
            'username.required' => 'Username wajib diisi.',
            'username.max' => 'Username tidak boleh lebih dari 25 karakter.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
    
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 70 karakter.',
            'email.unique' => 'Email sudah digunakan, silakan gunakan email lain.',
    
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password harus minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
    
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.numeric' => 'Nomor telepon harus berupa angka.',
            'phone_number.digits_between' => 'Nomor telepon harus antara 10 hingga 15 digit.',
    
            'role_id_role.required' => 'Role wajib dipilih.',
            'role_id_role.exists' => 'Role yang dipilih tidak valid.',
    
            'position_id_position.required' => 'Posisi wajib dipilih.',
            'position_id_position.exists' => 'Posisi yang dipilih tidak valid.',
    
            'divisi_id_divisi.required' => 'Divisi wajib dipilih.',
            'divisi_id_divisi.exists' => 'Divisi yang dipilih tidak valid.',
        ]);
    
        
        

        $user = User::create([
            'id' => $request->id,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role_id_role' => $request->role_id_role,
            'position_id_position' => $request->position_id_position,
            'divisi_id_divisi' => $request->divisi_id_divisi,
        ]);
        

        // event(new Registered($user));

        // Auth::login($user);

        return redirect()->route('user.manage')->with('success', 'User added successfully!');
    }
    
}
