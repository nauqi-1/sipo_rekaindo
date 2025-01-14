<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    public function index()
    {
        $divisi = Divisi::all(); // Mengambil semua data divisi
        return view('superadmin.user-manage', compact('divisi'));
    }
}
