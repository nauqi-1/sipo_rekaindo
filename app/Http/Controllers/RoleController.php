<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function create()
    {
        $roles = Role::all(); // Mengambil semua data dari tabel role
        return view('superadmin.user-manage', compact('role'));
    }
}
