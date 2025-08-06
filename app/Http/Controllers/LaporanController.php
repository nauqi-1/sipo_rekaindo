<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Laporan;
use App\Models\Memo;
use App\Models\Undangan;
use App\Models\Risalah;
use App\Models\Role;
use App\Models\Seri;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use App\Http\Controllers\MemoController;

class LaporanController extends Controller
{

    public function filterMemosByDate(Request $request)
    {
        $divisi = Divisi::all(); // Menambahkan variabel divisi
        $kode = Memo::whereNotNull('kode')
            ->pluck('kode')
            ->unique();
        $memoController = new MemoController();
        $kodeUser = null;

        //ambil kode user klo rolenya admin
        if (Auth::user()->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }
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
        $memos = Memo::where(function ($query) use ($request, $kodeUser) {

            if (!$kodeUser) {
                if ($request->filled('kode') && $request->kode != 'pilih') {
                    $query->where('kode', $request->kode);
                    $kodeUser = $request->kode;
                }
            } else {
                $query->where('kode', $kodeUser);
            }
        })->whereDate('tgl_dibuat', '>=', $request->tgl_awal)
            ->whereDate('tgl_dibuat', '<=', $request->tgl_akhir)
            ->orderBy('tgl_dibuat', 'asc')
            ->get();

        return view('superadmin.laporan.cetak-laporan-memo', [
            'memos' => $memos,
            'divisi' => $divisi,
            'kode' => $kode
        ]);
    }

    public function filterUndanganByDate(Request $request)
    {
        $divisi = Divisi::all(); // Menambahkan variabel divisi
        $request->validate([
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal'
        ]);
        $kode = Undangan::whereNotNull('kode')
            ->pluck('kode')
            ->unique();

        $memoController = new MemoController();
        $kodeUser = null;
        if (Auth::user()->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }
        // Store dates in session
        $request->session()->put('filter_dates', [
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir
        ]);

        // Get filtered memos
        $undangans = Undangan::where(function ($query) use ($request, $kodeUser) {

            if (!$kodeUser) {
                if ($request->filled('kode') && $request->kode != 'pilih') {
                    $query->where('kode', $request->kode);
                    $kodeUser = $request->kode;
                }
            } else {
                $query->where('kode', $kodeUser);
            }
        })->whereDate('tgl_dibuat', '>=', $request->tgl_awal)
            ->whereDate('tgl_dibuat', '<=', $request->tgl_akhir)
            ->orderBy('tgl_dibuat', 'asc')
            ->get();
        return view('superadmin.laporan.cetak-laporan-undangan', [
            'undangans' => $undangans,
            'divisi' => $divisi,
            'kode' => $kode
        ]);
    }

    public function filterRisalahByDate(Request $request)
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
        $kode = Risalah::whereNotNull('kode')
            ->pluck('kode')
            ->unique();

        $memoController = new MemoController();
        $kodeUser = null;
        if (Auth::user()->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }
        // Get filtered memos
        $risalahs = Risalah::where(function ($query) use ($request, $kodeUser) {

            if (!$kodeUser) {
                if ($request->filled('kode') && $request->kode != 'pilih') {
                    $query->where('kode', $request->kode);
                    $kodeUser = $request->kode;
                }
            } else {
                $query->where('kode', $kodeUser);
            }
        })->whereDate('tgl_dibuat', '>=', $request->tgl_awal)
            ->whereDate('tgl_dibuat', '<=', $request->tgl_akhir)
            ->orderBy('tgl_dibuat', 'asc')
            ->get();

        return view('superadmin.laporan.cetak-laporan-risalah', [
            'risalahs' => $risalahs,
            'divisi' => $divisi,
            'kode' => $kode
        ]);
    }

    public function index(Request $request)
    {
        $divisi = Divisi::all();
        $kode = Memo::whereNotNull('kode')
            ->pluck('kode')
            ->filter()
            ->unique()
            ->values();
        $seri = Seri::all();
        $memoController = new MemoController();
        $kodeUser = null;
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        if ($user->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }

        $memos = Memo::query()
            ->where(function ($query) use ($request, $kodeUser) {
                if (!$kodeUser) {
                    if ($request->filled('kode') && $request->kode != 'pilih') {
                        $query->where('kode', $request->kode);
                        $kodeUser = $request->kode;
                    }
                } else {
                    $query->where('kode', $kodeUser);
                }
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

        if ($request->filled('kode') && $request->kode != 'pilih') {
            $memos->where('kode', $request->kode);
        }


        // Filter search jika ada
        if ($request->filled('search')) {
            $memos->where('judul', 'like', '%' . $request->search . '%');
        }

        $memos = $memos->orderBy('tgl_dibuat', 'asc')->get();

        // Jika masuk route cetak, arahkan ke cetak view
        if (request()->route()->getName() === 'cetak-laporan-memo.superadmin' || request()->is('cetak-laporan-memo')) {
            return view('superadmin.laporan.cetak-laporan-memo', [
                'memos' => $memos,
                'divisi' => $divisi,
                'kode' => $kode
            ]);
        }
    }


    public function undangan(Request $request)
    {
        $divisi = Divisi::all();
        $kode = Undangan::whereNotNull('kode')
            ->pluck('kode')
            ->filter()
            ->unique()
            ->values();
        $seri = Seri::all();
        $memoController = new MemoController();
        $kodeUser = null;
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        if ($user->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }

        $undangans = Undangan::query()
            ->where(function ($query) use ($request, $kodeUser) {
                if (!$kodeUser) {
                    if ($request->filled('kode') && $request->kode != 'pilih') {
                        $query->where('kode', $request->kode);
                        $kodeUser = $request->kode;
                    }
                } else {
                    $query->where('kode', $kodeUser);
                }
            });

        // Filter tanggal dari session
        if (session()->has('filter_dates')) {
            $dates = session('filter_dates');
            $undangans->whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir']);
        }


        // Filter search jika ada
        if ($request->filled('search')) {
            $undangans->where('judul', 'like', '%' . $request->search . '%');
        }

        $undangans = $undangans->orderBy('tgl_dibuat', 'asc')->get();

        // Jika masuk route cetak, tampilkan cetak-laporan-undangan
        if (request()->route()->getName() === 'cetak-laporan-undangan.superadmin' || request()->is('cetak-laporan-undangan')) {
            return view('superadmin.laporan.cetak-laporan-undangan', [
                'undangans' => $undangans,
                'divisi' => $divisi,
                'kode' => $kode
            ]);
        }
    }

    public function risalah(Request $request)
    {
        $divisi = Divisi::all();
        $kode = Undangan::whereNotNull('kode')
            ->pluck('kode')
            ->filter()
            ->unique()
            ->values();
        $seri = Seri::all();
        $memoController = new MemoController();
        $kodeUser = null;
        $user = Auth::user();
        if (!$user) {
            return redirect('/');
        }

        if ($user->role->nm_role == 'admin') {
            $kodeUser = $memoController->getDivDeptKode(Auth::user());
        }

        $risalahs = Risalah::query()
            ->where(function ($query) use ($request, $kodeUser) {
                if (!$kodeUser) {
                    if ($request->filled('kode') && $request->kode != 'pilih') {
                        $query->where('kode', $request->kode);
                        $kodeUser = $request->kode;
                    }
                } else {
                    $query->where('kode', $kodeUser);
                }
            });

        // Filter tanggal dari session
        if (session()->has('filter_dates')) {
            $dates = session('filter_dates');
            $risalahs->whereDate('tgl_dibuat', '>=', $dates['tgl_awal'])
                ->whereDate('tgl_dibuat', '<=', $dates['tgl_akhir']);
        }

        // Filter search jika ada
        if ($request->filled('search')) {
            $risalahs->where('judul', 'like', '%' . $request->search . '%');
        }

        $risalahs = $risalahs->orderBy('tgl_dibuat', 'asc')->get();

        // Jika masuk route cetak, tampilkan cetak-laporan-undangan
        if (request()->route()->getName() === 'cetak-laporan-risalah.superadmin' || request()->is('cetak-laporan-risalah')) {
            return view('superadmin.laporan.cetak-laporan-risalah', [
                'risalahs' => $risalahs,
                'divisi' => $divisi,
                'kode' => $kode
            ]);
        }
    }
}
