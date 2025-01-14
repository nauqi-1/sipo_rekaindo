<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;
use App\Models\Role;

class UserManageController extends Controller
{
    public function index()
    {
        // Ambil data dari Divisi dan Role
        $divisi = Divisi::all();  // Ambil data divisi dari database
        $roles = Role::all();  // Ambil data roles dari database

        // Kirim data ke view user-manage
        return view('superadmin.user-manage', compact('divisi', 'roles'));
    }
}
