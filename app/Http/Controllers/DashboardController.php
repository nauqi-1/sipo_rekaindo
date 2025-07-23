<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\Risalah;
use App\Models\Undangan;
use App\Models\Kirim_Document;
use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung jumlah memo yang sudah dibuat

        $userDivisiId = auth()->user()->divisi_id_divisi; // Ambil divisi user yang login
        $kirimDocuments = Kirim_Document::where('id_penerima', Auth::user()->id)->get();

        $jumlahMemo = Kirim_Document::where('jenis_document', 'memo')
            ->where(function ($query) {
                $query->where('id_pengirim', Auth::user()->id)
                    ->orWhere('id_penerima', Auth::user()->id);
            })
            ->select('id_document')
            ->groupBy('id_document')
            ->get()
            ->count();

        $jumlahRisalah = Kirim_Document::where('jenis_document', 'risalah')
            ->where(function ($query) {
                $query->where('id_pengirim', Auth::user()->id)
                    ->orWhere('id_penerima', Auth::user()->id);
            })
            ->select('id_document')
            ->groupBy('id_document')
            ->get()
            ->count();


        $jumlahUndangan = Kirim_Document::where('jenis_document', 'undangan')
            ->where(function ($query) {
                $query->where('id_pengirim', Auth::user()->id)
                    ->orWhere('id_penerima', Auth::user()->id);
            })
            ->select('id_document')
            ->groupBy('id_document')
            ->get()
            ->count();

        $Memo = Memo::all()->count();
        $Undangan = Undangan::all()->count();
        $Risalah = Risalah::all()->count();

        // Kirim data ke view
        return view(Auth::user()->role->nm_role . '.dashboard', compact('jumlahMemo', 'jumlahRisalah', 'jumlahUndangan', 'Memo', 'Undangan', 'Risalah'));
    }
}
