<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Storage;

class PerusahaanController extends Controller {
    // Tampilkan data perusahaan
    public function index() {
        $perusahaan = Perusahaan::first(); // Ambil data pertama
        return view('superadmin.data-perusahaan', compact('perusahaan'));
    }

    // Simpan atau update data perusahaan
    public function update(Request $request) {
        $perusahaan = Perusahaan::firstOrNew([]); // Jika belum ada, buat baru

        // Validasi data
        $request->validate([
            'nama_instansi' => 'required',
            'alamat_web' => 'required',
            'telepon' => 'required',
            'email' => 'required|email',
            'alamat' => 'required',
            'logo' => 'nullable|image|max:2048', // Validasi file gambar max 2MB
        ]);

        // Cek jika ada file logo
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($perusahaan->logo) {
                Storage::disk('public')->delete('logos/' . $perusahaan->logo);
            }

            // Simpan logo baru
            $logo = $request->file('logo');
            $logoPath = $logo->store('logos', 'public'); // Simpan di storage/logos/
            $perusahaan->logo = basename($logoPath);
        }

        // Update data
        $perusahaan->fill([
            'nama_instansi' => $request->nama_instansi,
            'alamat_web' => $request->alamat_web,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ])->save();

        return redirect()->route('data-perusahaan')->with('success', 'Data perusahaan berhasil diperbarui');
    }
}