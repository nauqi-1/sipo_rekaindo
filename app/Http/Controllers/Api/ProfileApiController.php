<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    public function profileDetails(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'username' => $user->username,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'profile_image' => $user->profile_image
                ? 'data:image/jpeg;base64,' . $user->profile_image
                : null,
            'role_id' => $user->role_id_role,
            'divisi_id' => $user->divisi_id_divisi,
        ]);
    }
}
