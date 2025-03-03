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
        $user = Auth::user(); // Ambil user yang sedang login

        // Validasi data
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'phone_number' => 'nullable|string|max:15',
            'password' => 'nullable|min:6|confirmed',
            'image' => 'nullable|image|max:2048', // Maksimal 2MB
        ]);

        // Cek jika ada file profile_image
        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($user->image) {
                Storage::disk('public')->delete('images/' . $user->image);
            }

            // Simpan foto baru
            $file = $request->file('image');
            $filePath = $file->store('images', 'public'); // Simpan di storage/profile_images/
            $user->image = basename($filePath);
        }

        // Update data user
        $userData = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        // Update user data
        User::where('id', $user->id)->update($userData);

        return redirect()->route('edit-profile.superadmin')->with('success', 'Profil berhasil diperbarui');
    }
}
