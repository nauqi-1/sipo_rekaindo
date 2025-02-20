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

        return redirect()->route('cetak-laporan-memo.superadmin');
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
            ->orderBy('tgl_dibuat', 'desc')
            ->get();

        return redirect()->route('cetak-laporan-undangan.superadmin')->with('undangans', $undangans);
    }

    public function index()
    {
        $divisi = Divisi::all();
        $seri = Seri::all();  
        $laporans = Laporan::with('divisi')->orderBy('tgl_dibuat', 'desc')->paginate(6);
        
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // For cetak-laporan-memo view
        if (request()->route()->getName() === 'cetak-laporan-memo.superadmin' || request()->is('cetak-laporan-memo')) {
            $memos = Memo::orderBy('tgl_dibuat', 'desc')->get();
            
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
            
            return view('superadmin.laporan.cetak-laporan-memo', [
                'memos' => $memos,
                'laporans' => $memos
            ]);
        }

        // For cetak-laporan-undangan view
        if (request()->route()->getName() === 'cetak-laporan-undangan.superadmin' || request()->is('cetak-laporan-undangan')) {
            // Initialize undangans query
            $undangans = Undangan::query();
            
            // Check if filter dates exist in session
            if (session()->has('filter_dates')) {
                $dates = session('filter_dates');
                $undangans->whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                    ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir']);
                
                // Clear filter dates from session
                session()->forget('filter_dates');
            }
            
            // Get the filtered or all undangans
            $undangans = $undangans->orderBy('tgl_dibuat', 'desc')->get();
            
            // Check if undangans data was passed via redirect
            if (session()->has('undangans')) {
                $undangans = session('undangans');
                session()->forget('undangans');
            }
            
            // Ensure undangans is always passed to the view
            return view('superadmin.laporan.cetak-laporan-undangan', compact('undangans'));
        }

        return view($user->role->nm_role.'.laporan.laporan-'.$user->role->nm_role, compact('laporans','divisi','seri'));
    }
}
