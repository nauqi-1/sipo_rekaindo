<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Tampilkan halaman edit profil
    public function editProfile()
    {
        $user = Auth::user();
        $position = Position::where('id_position', $user->position_id_position)->value('nm_position');
        
        return view('superadmin.edit-profileSuperadmin', compact('user', 'position'));
    }

    // Simpan atau update profil user
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validasi data input
        $request->validate([
            'firstname'         => 'required|string|max:50|regex:/^[A-Za-z\s]+$/|not_regex:/[\x{1F600}-\x{1F64F}]/u|not_regex:/[\x{1F300}-\x{1F5FF}]/u|not_regex:/[\x{1F680}-\x{1F6FF}]/u|not_regex:/[\x{2600}-\x{26FF}]/u|not_regex:/[\x{2700}-\x{27BF}]/u',
            'lastname'          => 'required|string|max:50|regex:/^[A-Za-z\s]+$/|not_regex:/[\x{1F600}-\x{1F64F}]/u|not_regex:/[\x{1F300}-\x{1F5FF}]/u|not_regex:/[\x{1F680}-\x{1F6FF}]/u|not_regex:/[\x{2600}-\x{26FF}]/u|not_regex:/[\x{2700}-\x{27BF}]/u',
            'username'          => 'required|string|max:255|unique:users,username,' . $user->id,
            'phone_number'      => 'nullable|string|max:15|regex:/^[0-9\+]+$/',
            'password'          => 'nullable|min:6|confirmed',
            'profile_image'     => 'nullable|image|max:2048', // pastikan sesuai name input
        ]);

        // Siapkan data yang akan diupdate
        $userData = [
            'firstname'     => $request->firstname,
            'lastname'      => $request->lastname,
            'username'      => $request->username,
            'phone_number'  => $request->phone_number,
        ];

        // Simpan password jika ada input
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        // Simpan gambar profil jika ada file upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $userData['profile_image'] = $imageData;
        }

        // Update data user
        User::where('id', $user->id)->update($userData);

        return redirect()->route('edit-profile.superadmin')->with('success', 'Profil berhasil diperbarui.');
    }

    // Hapus foto profil
    public function deletePhoto(Request $request)
    {
        $user = Auth::user();
        $user->profile_image = null;
        $user->save();

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
