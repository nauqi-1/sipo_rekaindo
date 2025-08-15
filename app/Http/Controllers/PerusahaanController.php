<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Storage;

class PerusahaanController extends Controller
{
    // Tampilkan data perusahaan
    public function index()
    {
        $perusahaan = Perusahaan::first(); // Ambil data pertama
        return view('superadmin.data-perusahaan', compact('perusahaan'));
    }

    // Simpan atau update data perusahaan
    public function update(Request $request)
    {
        $perusahaan = Perusahaan::firstOrNew([]);

        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'alamat_web'    => 'required|string|max:255',
            'telepon'       => 'required|string|max:15|regex:/^[0-9\+\-]+$/',
            'email'         => 'required|email|max:255',
            'alamat'        => 'required|string',
            'logo'          => 'nullable|mimes:jpg,jpeg,png,svg|max:2048',
        ], [
            'logo.mimes' => 'Logo harus berupa file gambar dengan format: JPG, JPEG, PNG, atau SVG.',
            'logo.max'   => 'Ukuran logo tidak boleh lebih dari 2 MB.',
        ]);

        try {
            $dataUpdate = [
                'nama_instansi' => $request->nama_instansi,
                'alamat_web'    => $request->alamat_web,
                'telepon'       => $request->telepon,
                'email'         => $request->email,
                'alamat'        => $request->alamat,
            ];

            // Kalau ada logo baru, masukkan juga
            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');
                $logoContent = file_get_contents($logoFile->getRealPath());
                $dataUpdate['logo'] = base64_encode($logoContent);
            }

            // Cek apakah data berbeda
            $isDifferent = false;
            foreach ($dataUpdate as $key => $value) {
                if ($perusahaan->$key != $value) {
                    $isDifferent = true;
                    break;
                }
            }

            if (!$isDifferent) {
                return redirect()->back()
                    ->with('success', 'Tidak ada perubahan data yang disimpan.')
                    ->withInput();
            }

            // Simpan perubahan
            $perusahaan->fill($dataUpdate);

            if ($perusahaan->save()) {
                return redirect()->route('data-perusahaan')
                    ->with('success', 'Data perusahaan berhasil diperbarui');
            } else {
                return redirect()->back()
                    ->with('error', 'Data perusahaan gagal diperbarui, silakan coba lagi.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
