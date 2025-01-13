<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\role;
use App\Models\user;

class DashboardController extends Controller
{
    public function index($role)
    {
        $role = Role::where('role', $role)->firstOrFail();
        $jenjang = user::where('role_id', $role->id)->get();
        
        return view('dashboard', compact('role'));
    }
}
