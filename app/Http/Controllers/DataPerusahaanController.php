<?php

namespace App\Http\Controllers;
use App\Models\DataPerusahaan;

use Illuminate\Http\Request;

class DataPerusahaanController extends Controller
{
    public function index()
    {
        return view('data-perusahaan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nm_prshn' => 'required|string|max:255',
            'alamat_web' => 'required|string|max:255',
            'telepon' => 'required|string|max:15',
            'email' => 'required|string|max:255',
            'alamat' => 'required|text',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data_perusahaan = new DataPerusahaan();
        $data_perusahaan->nm_prshn = $request->input('nm_prshn');
        $data_perusahaan->alamat_web = $request->input('alamat_web');
        $data_perusahaan->telepon = $request->input('telepon');
        $data_perusahaan->email = $request->input('email');
        $data_perusahaan->alamat = $request->input('alamat');

        // menyimpan data file yang diupload
        if ($request->hasFile('logo')){ 
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move('uploads/logo/', $filename);
        }
    }
}
