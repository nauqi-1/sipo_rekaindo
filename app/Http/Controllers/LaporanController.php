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
        ]);
    }

    public function filterUndanganByDate(Request $request)
    {
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal'
        ]);

        // Store dates in session
        $request->session()->put('filter_dates', [
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir
        ]);

        // Get filtered undangans
        $dates = session('filter_dates');
        $undangans = Undangan::whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
            ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir'])
            ->where(function($query) {
                $query->where('status', 'diterima')
                      ->orWhere('status', 'approve');
            })
            ->orderBy('tgl_dibuat', 'desc')
            ->get();

        return redirect()->route('cetak-laporan-undangan.superadmin')->with('undangans', $undangans);
    }

    public function index()
    {
        $divisi = Divisi::all();
        $seri = Seri::all();  
        $memos = Memo::where(function($query) {
            $query->where('status', 'diterima')
                  ->orWhere('status', 'approve');
        })->orderBy('tgl_dibuat', 'desc')->get();
        $laporans = Laporan::with('divisi')->orderBy('tgl_dibuat', 'desc')->paginate(6);
        
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // For cetak-laporan-memo view
        if (request()->route()->getName() === 'cetak-laporan-memo.superadmin' || request()->is('cetak-laporan-memo')) {
            $memos = Memo::where(function($query) {
                $query->where('status', 'diterima')
                      ->orWhere('status', 'approve');
            })->orderBy('tgl_dibuat', 'desc')->get();
            
            // Check if filter dates exist in session
            if (session()->has('filter_dates')) {
                $dates = session('filter_dates');
                $memos = Memo::whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                    ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir'])
                    ->orderBy('tgl_dibuat', 'desc')
                    ->get();
                
                // Clear filter dates from session
                session()->forget('filter_dates');
            }
            
            $memos = $memos ?? collect(); // Ensure $memos is always defined
            return view('superadmin.laporan.cetak-laporan-memo', [
                'memos' => $memos,
                'laporans' => $memos
            ]);
        }

        // For cetak-laporan-undangan view
        if (request()->route()->getName() === 'cetak-laporan-undangan.superadmin' || request()->is('cetak-laporan-undangan')) {
            $undangans = Undangan::where(function($query) {
                $query->where('status', 'diterima')
                      ->orWhere('status', 'approve');
            })->orderBy('tgl_dibuat', 'desc')->get();
            
            // Check if filter dates exist in session
            if (session()->has('filter_dates')) {
                $dates = session('filter_dates');
                $undangans = Undangan::whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                    ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir'])
                    ->where(function($query) {
                        $query->where('status', 'diterima')
                              ->orWhere('status', 'approve');
                    })
                    ->orderBy('tgl_dibuat', 'desc')
                    ->get();
                
                // Clear filter dates from session
                session()->forget('filter_dates');
            }
            
            $undangans = $undangans ?? collect(); // Ensure $undangans is always defined
            return view('superadmin.laporan.cetak-laporan-undangan', [
                'undangans' => $undangans,
                'laporans' => $undangans
            ])
            ->with('tgl_awal', session('filter_dates.tgl_awal'))
            ->with('tgl_akhir', session('filter_dates.tgl_akhir'));
        }

        return view('format-surat.format-cetakLaporan-memo', compact('laporans','divisi','seri','memos'))
            ->with('tgl_awal', session('filter_dates.tgl_awal'))
            ->with('tgl_akhir', session('filter_dates.tgl_akhir'));
    }
    public function undangan()
    {
        $divisi = Divisi::all();
        $seri = Seri::all();  
        $undangans = Undangan::where(function($query) {
            $query->where('status', 'diterima')
                  ->orWhere('status', 'approve');
        })->orderBy('tgl_dibuat', 'desc')->get();
        $laporans = Laporan::with('divisi')->orderBy('tgl_dibuat', 'desc')->paginate(6);
        
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // For cetak-laporan-undangan view
        if (request()->route()->getName() === 'cetak-laporan-undangan.superadmin' || request()->is('cetak-laporan-undangan')) {
            $undangans = Undangan::where(function($query) {
                $query->where('status', 'diterima')
                      ->orWhere('status', 'approve');
            })->orderBy('tgl_dibuat', 'desc')->get();
            
            // Check if filter dates exist in session
            if (session()->has('filter_dates')) {
                $dates = session('filter_dates');
                $undangans = Undangan::whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                    ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir'])
                    ->orderBy('tgl_dibuat', 'desc')
                    ->get();
                
                // Clear filter dates from session
                session()->forget('filter_dates');
            }
            
            $undangans = $undangans ?? collect(); // Ensure $undangans is always defined
            return view('superadmin.laporan.cetak-laporan-undangan', [
                'undangans' => $undangans,
                'laporans' => $undangans
            ]);
        }

        // For cetak-laporan-undangan view
        if (request()->route()->getName() === 'cetak-laporan-undangan.superadmin' || request()->is('cetak-laporan-undangan')) {
            $undangans = Undangan::where(function($query) {
                $query->where('status', 'diterima')
                      ->orWhere('status', 'approve');
            })->orderBy('tgl_dibuat', 'desc')->get();
            
            // Check if filter dates exist in session
            if (session()->has('filter_dates')) {
                $dates = session('filter_dates');
                $undangans = Undangan::whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                    ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir'])
                    ->where(function($query) {
                        $query->where('status', 'diterima')
                              ->orWhere('status', 'approve');
                    })
                    ->orderBy('tgl_dibuat', 'desc')
                    ->get();
                
                // Clear filter dates from session
                session()->forget('filter_dates');
            }
            
            $undangans = $undangans ?? collect(); // Ensure $undangans is always defined
            return view('superadmin.laporan.cetak-laporan-undangan', [
                'undangans' => $undangans,
                'laporans' => $undangans
            ])
            ->with('tgl_awal', session('filter_dates.tgl_awal'))
            ->with('tgl_akhir', session('filter_dates.tgl_akhir'));
        }

        return view('format-surat.format-cetakLaporan-undangan', compact('laporans','divisi','seri','undangans'))
            ->with('tgl_awal', session('filter_dates.tgl_awal'))
            ->with('tgl_akhir', session('filter_dates.tgl_akhir'));
    }
}