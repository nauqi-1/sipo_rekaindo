<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Director;
use App\Models\Divisi;
use App\Models\Department;
use App\Models\Section;
use App\Models\Unit;
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
        try {
            $request->validate([
                'firstname' => 'required|string|max:50|regex:/^[A-Za-z.\s]+$/',
                'lastname' => 'required|string|max:50|regex:/^[A-Za-z.\s]+$/',
                'username' => 'required|string|max:25|unique:users',
                'email' => 'required|string|email|max:70|unique:users',
                'password' => 'required|min:8|confirmed|',
                'phone_number' => 'required|numeric|regex:/^[0-9+\-]+$/',
                'role_id_role' => 'required|exists:role,id_role',
                'position_id_position' => 'required|exists:position,id_position',
                'parent_id' => 'required',
                'parent_type' => 'required',
                'profile_image'     => 'nullable|image|max:2048',
            ], [
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
                'role_id_role.required' => 'Role wajib dipilih.',
                'role_id_role.exists' => 'Role yang dipilih tidak valid.',
                'position_id_position.required' => 'Posisi wajib dipilih.',
                'position_id_position.exists' => 'Posisi yang dipilih tidak valid.',
                'parent_id.required' => 'Bagian wajib dipilih.',
            ]);
            $bagian = $request->parent_id;
            $type = $request->parent_type;

            if ($type == "director") {
                $direktur = $bagian;
                $divisi = $department = $section = $unit = null;
            } elseif ($type == "divisi") {
                $direktur = Divisi::where('id_divisi', $bagian)->value('director_id_director');
                $divisi = $bagian;
                $department = $section = $unit = null;
            } elseif ($type == "department") {
                $direktur = Department::where('id_department', $bagian)->value('director_id_director');
                $divisi = Department::where('id_department', $bagian)->value('divisi_id_divisi') ?? null;
                $department = $bagian;
                $section = $unit = null;
            } elseif ($type == "section") {
                $department = Section::where('id_section', $bagian)->value('department_id_department');
                $direktur = Department::where('id_department', $department)->value('director_id_director');
                $divisi = Department::where('id_department', $department)->value('divisi_id_divisi') ?? null;
                $section = $bagian;
                $unit = null;
            } elseif ($type == "unit") {
                $department = Unit::where('id_unit', $bagian)->value('department_id_department') ?? null;
                $section = Unit::where('id_unit', $bagian)->value('section_id_section') ?? null;
                $direktur = Department::where('id_department', $department)->value('director_id_director');
                $divisi = Department::where('id_department', $department)->value('divisi_id_divisi') ?? null;
                $unit = $bagian;
            }

            User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'role_id_role' => $request->role_id_role,
                'position_id_position' => $request->position_id_position,
                'director_id_director' => $direktur ?? null,
                'divisi_id_divisi' => $divisi ?? null,
                'department_id_department' => $department ?? null,
                'section_id_section' => $section ?? null,
                'unit_id_unit' => $unit ?? null,
            ]);

            return redirect()->route('user.manage')->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
