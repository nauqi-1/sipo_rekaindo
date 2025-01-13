<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class UserController extends Controller
{
    public function showRole()
    {
        
            $jenjang = Role::all();
            return view('user.role', compact('role'));
    
    }
}
