<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\Risalah;
use App\Models\Undangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung jumlah memo yang sudah dibuat
        $jumlahMemo = Memo::count();
        $jumlahRisalah = Risalah::count();
        $jumlahUndangan = Undangan::count();

        // Kirim data ke view
        return view(Auth::user()->role->nm_role.'.dashboard', compact('jumlahMemo','jumlahRisalah','jumlahUndangan'));
        
    }
}   