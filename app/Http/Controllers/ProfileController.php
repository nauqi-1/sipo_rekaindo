<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller {
    // Tampilkan halaman edit profil
    public function editProfile() {
        $user = Auth::user();
        return view('superadmin.edit-profileSuperadmin', compact('user'));
    }

    // Simpan atau update profil user
    public function updateProfile(Request $request) {
        /** @var User $user */
        $user = Auth::user();
    
        // Validasi data input
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'phone_number' => 'nullable|string|max:15',
            'password' => 'nullable|min:6|confirmed',
            'profile_image' => 'nullable|image|max:2048',
        ]);
    
        // Persiapkan data yang akan diupdate
        $userData = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
        ];
    
        // Jika password diisi, update password
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }
    
        // Jika ada file gambar yang diunggah
        if ($request->hasFile('image')) {
            $file = $request->file('image');
    
            // Encode gambar jadi base64
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
    
            // Simpan ke field image_blob
            $userData['profile_image'] = $imageData;
        }
    
        // Update user di database
        User::where('id', $user->id)->update($userData);
    
        return redirect()->route('edit-profile.superadmin')->with('success', 'Profil berhasil diperbarui');
    }

    public function deletePhoto(Request $request) {
        $user = Auth::user();
        $user->profile_image = null;
        $user->save();

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}    
