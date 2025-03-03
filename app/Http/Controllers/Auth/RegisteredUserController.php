<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
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
        
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'username' => 'required|string|max:25',
            'email' => 'required|string|email|max:70|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone_number' => 'required|numeric',
            'role_id_role' => 'required|exists:role,id_role',
            'position_id_position' => 'required|exists:position,id_position',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
        ]);
        

        $user = User::create([
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
