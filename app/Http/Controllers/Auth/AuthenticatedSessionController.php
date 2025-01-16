<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

         // Dapatkan user yang sedang login
    $user = $request->user();

    // Cek role user dan redirect sesuai role
    switch ($user->role->nm_role) {
        case 'superadmin':
            return redirect()->route('superadmin.dashboard');
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'manager':
            return redirect()->route('manager.dashboard');
        default:
            return redirect()->route('dashboard'); // Route default jika role tidak sesuai
    }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
