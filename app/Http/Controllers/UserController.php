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

        return view('superadmin.edit', compact('mainDirector', 'user', 'divisi', 'roles', 'positions'));
    }

    // Menangani update data user
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            // Validasi input
            $validated = $request->validate([
                'firstname'         => 'required|string|max:50|regex:/^[A-Za-z.\s]+$/',
                'lastname'          => 'required|string|max:50|regex:/^[A-Za-z.\s]+$/',
                'username'          => 'required|string|max:25|unique:users,username,' . $id,
                'email'             => 'required|string|email|max:70|unique:users,email,' . $id,
                'password'          => 'nullable|min:8|confirmed',
                'phone_number'      => 'nullable|numeric',
                'role_id_role'      => 'required|exists:role,id_role',
                'position'       => 'required|exists:position,id_position',
                'parent_id'         => 'required',
                'parent_type'       => 'required|in:director,divisi,department,section,unit',
            ]);

            // Update field dasar
            $user->firstname       = $validated['firstname'];
            $user->lastname        = $validated['lastname'];
            $user->username        = $validated['username'];
            $user->email           = $validated['email'];
            $user->phone_number    = $validated['phone_number'] ?? $user->phone_number;
            $user->role_id_role    = $validated['role_id_role'];
            $user->position_id_position = $validated['position'];

            if (!empty($validated['password'])) {
                $user->password = bcrypt($validated['password']);
            }

            // Handle parent/struktur organisasi
            $bagian = $validated['parent_id'];
            $type   = $validated['parent_type'];

            switch ($type) {
                case "director":
                    $user->director_id_director = $bagian;
                    $user->divisi_id_divisi     = null;
                    $user->department_id_department = null;
                    $user->section_id_section   = null;
                    $user->unit_id_unit         = null;
                    break;

                case "divisi":
                    $divisi = Divisi::find($bagian);
                    $user->director_id_director = $divisi?->director_id_director;
                    $user->divisi_id_divisi     = $bagian;
                    $user->department_id_department = null;
                    $user->section_id_section   = null;
                    $user->unit_id_unit         = null;
                    break;

                case "department":
                    $department = Department::find($bagian);
                    $user->director_id_director = $department?->director_id_director;
                    $user->divisi_id_divisi     = $department?->divisi_id_divisi;
                    $user->department_id_department = $bagian;
                    $user->section_id_section   = null;
                    $user->unit_id_unit         = null;
                    break;

                case "section":
                    $section = Section::find($bagian);
                    $department = $section?->department;
                    $user->department_id_department = $section?->department_id_department;
                    $user->director_id_director = $department?->director_id_director;
                    $user->divisi_id_divisi     = $department?->divisi_id_divisi;
                    $user->section_id_section   = $bagian;
                    $user->unit_id_unit         = null;
                    break;

                case "unit":
                    $unit = Unit::find($bagian);
                    $department = $unit?->department;
                    $user->department_id_department = $unit?->department_id_department;
                    $user->section_id_section   = $unit?->section_id_section;
                    $user->director_id_director = $department?->director_id_director;
                    $user->divisi_id_divisi     = $department?->divisi_id_divisi;
                    $user->unit_id_unit         = $bagian;
                    break;
            }


            $user->save();

            return redirect()->route('user.manage')->with('success', 'User berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {

        try {
            $user = User::findOrFail($id);
            $user->delete(); // ini hanya soft delete (update deleted_at)

            return response()->json(['success' => true, 'message' => 'User berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
