<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\Risalah;
use App\Models\Undangan;
use App\Models\Kirim_Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung jumlah memo yang sudah dibuat
        
        $userDivisiId = auth()->user()->divisi_id_divisi; // Ambil divisi user yang login
        $kirimDocuments = Kirim_Document::where('id_penerima', Auth::user()->id)->get();

        $jumlahMemo = Memo::where('divisi_id_divisi', $userDivisiId)->count();
        $jumlahRisalah = Risalah::where('divisi_id_divisi', $userDivisiId)->count();
        $jumlahUndangan = Undangan::where('divisi_id_divisi', $userDivisiId)->count();

        $Memo = Memo::all()->count();
        $Undangan = Undangan::all()->count();
        $Risalah = Risalah::all()->count();

        // Kirim data ke view
        return view(Auth::user()->role->nm_role.'.dashboard', compact('jumlahMemo','jumlahRisalah','jumlahUndangan','Memo','Undangan','Risalah'));  
        
    }
}   