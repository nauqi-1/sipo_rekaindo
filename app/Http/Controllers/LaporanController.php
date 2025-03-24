<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Laporan;
use App\Models\Memo;
use App\Models\Undangan;
use App\Models\Role;
use App\Models\Seri;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // ... (previous methods remain unchanged)

    public function filterMemosByDate(Request $request)
    {
        $divisi = Divisi::all(); // Menambahkan variabel divisi
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal'
        ]);

        // Store dates in session
        $request->session()->put('filter_dates', [
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir
        ]);

        // Get filtered memos
        $memos = Memo::where(function($query) {
            $query->where('status', 'diterima')
                  ->orWhere('status', 'approve');
        })->whereDate('tgl_dibuat', '>=', $request->tgl_awal)
          ->whereDate('tgl_dibuat', '<=', $request->tgl_akhir)
          ->orderBy('tgl_dibuat', 'desc')
          ->get();

        return view('superadmin.laporan.cetak-laporan-memo', [
            'memos' => $memos,
            'divisi' => $divisi
        ]);
    }

    public function filterUndanganByDate(Request $request)
    {
        $divisi = Divisi::all(); // Menambahkan variabel divisi
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal'
        ]);

        // Store dates in session
        $request->session()->put('filter_dates', [
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir
        ]);

        // Get filtered memos
        $undangans = Undangan::where(function($query) {
            $query->where('status', 'diterima')
                  ->orWhere('status', 'approve');
        })->whereDate('tgl_dibuat', '>=', $request->tgl_awal)
          ->whereDate('tgl_dibuat', '<=', $request->tgl_akhir)
          ->orderBy('tgl_dibuat', 'desc')
          ->get();

        return view('superadmin.laporan.cetak-laporan-undangan', [
            'undangans' => $undangans,
            'divisi' => $divisi
        ]);
    }

    public function index(Request $request)
    {
        $divisi = Divisi::all();
        $seri = Seri::all();  
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        $memos = Memo::query()
            ->where(function($query) {
                $query->where('status', 'diterima')
                    ->orWhere('status', 'approve');
            });

        // Filter berdasarkan tanggal dari session
        if (session()->has('filter_dates')) {
            $dates = session('filter_dates');
            $memos->whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir']);
        }

        // Filter divisi jika ada
        if ($request->filled('divisi_id_divisi')) {
            $memos->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Filter search jika ada
        if ($request->filled('search')) {
            $memos->where('judul', 'like', '%' . $request->search . '%');
        }

        $memos = $memos->orderBy('tgl_dibuat', 'desc')->get();

        // Jika masuk route cetak, arahkan ke cetak view
        if (request()->route()->getName() === 'cetak-laporan-memo.superadmin' || request()->is('cetak-laporan-memo')) {
            return view('superadmin.laporan.cetak-laporan-memo', [
                'memos' => $memos,
                'divisi' => $divisi
            ]);
        }

    }


    public function undangan(Request $request)
    {
        $divisi = Divisi::all();
        $seri = Seri::all();  
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        $undangans = Undangan::query()
            ->where(function($query) {
                $query->where('status', 'diterima')
                    ->orWhere('status', 'approve');
            });

        // Filter tanggal dari session
        if (session()->has('filter_dates')) {
            $dates = session('filter_dates');
            $undangans->whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                    ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir']);
        }

        // Filter divisi jika ada
        if ($request->filled('divisi_id_divisi')) {
            $undangans->where('divisi_id_divisi', $request->divisi_id_divisi);
        }

        // Filter search jika ada
        if ($request->filled('search')) {
            $undangans->where('judul', 'like', '%' . $request->search . '%');
        }

        $undangans = $undangans->orderBy('tgl_dibuat', 'desc')->get();

        // Jika masuk route cetak, tampilkan cetak-laporan-undangan
        if (request()->route()->getName() === 'cetak-laporan-undangan.superadmin' || request()->is('cetak-laporan-undangan')) {
            return view('superadmin.laporan.cetak-laporan-undangan', [
                'undangans' => $undangans,
                'divisi' => $divisi
            ]);
        }

        
    }


}