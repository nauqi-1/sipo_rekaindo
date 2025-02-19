<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Seri;
use App\Models\User;
use App\Models\Divisi;
use App\Models\Undangan;

use Illuminate\Http\Request;

class UndanganController extends Controller
{
    public function index()
    {
        $divisi = Divisi::all();
        $seri = Seri::all();  
        $undangans = Undangan::with('divisi')->orderBy('tgl_dibuat', 'desc')->get();

    
        return view(Auth::user()->role->nm_role.'.undangan.undangan', compact('undangans','divisi','seri'));
    }
    public function create()
    {
        $divisiId = auth()->user()->divisi_id_divisi;
        $divisiName = auth()->user()->divisi->nm_divisi;

    // Ambil nomor seri berikutnya
    $nextSeri = Seri::getNextSeri(false);

    // Konversi bulan ke angka Romawi
    $bulanRomawi = $this->convertToRoman(now()->month);

    // Format nomor dokumen
    $nomorDokumen = sprintf(
        "%d.%d/REKA/GEN/%s/%s/%d",
        $nextSeri['seri_bulanan'],
        $nextSeri['seri_tahunan'],
        strtoupper($divisiName),
        $bulanRomawi,
        now()->year
    );

    $managers = User::where('divisi_id_divisi', $divisiId)
        ->where('position_id_position', '2')
        ->get(['id', 'firstname', 'lastname']);

    return view(Auth::user()->role->nm_role.'.undangan.add-undangan', [
        'nomorSeriTahunan' => $nextSeri['seri_tahunan'], // Tambahkan nomor seri tahunan
        'nomorDokumen' => $nomorDokumen,
        'managers' => $managers
    ]);  
    }
    public function store(Request $request)
    {
        // dd($request->all());


        $request->validate([
            'judul' => 'required|string|max:70',
            'isi_undangan' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_undangan' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'pembuat' => 'required|string|max:255',
            'catatan'=>'required|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
            'tanda_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ],[
            'tanda_identitas.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
            'tanda_identitas.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
        ]);

        $fileContent = null;
        if ($request->hasFile('tanda_identitas')) {
            $file = $request->file('tanda_identitas');
            $fileContent = file_get_contents($file->getRealPath()); // Membaca file sebagai binary
        }

        $divisiId = auth()->user()->divisi_id_divisi;
        $seri = Seri::getNextSeri(true);
        $seri = Seri::where('divisi_id_divisi', $divisiId)
                ->where('tahun', now()->year)
                ->latest()
                ->first();

        if (!$seri) {
            return back()->with('error', 'Nomor seri tidak ditemukan.');
        }
        


        // Simpan dokumen
        $Undangan = Undangan::create([
            'divisi_id_divisi' => $request->input('divisi_id_divisi'),
            'judul' => $request->input('judul'),
            'tujuan' => $request->input('tujuan'),
            'isi_undangan' => $request->input('isi_undangan'),
            'nomor_undangan' => $request->input('nomor_undangan'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'pembuat' => $request->input('pembuat'),
            'catatan' => $request->input('catatan'),
            'seri_surat' => $request->input('seri_surat'),
            'status' => 'pending',
            'nama_bertandatangan' => $request->input('nama_bertandatangan'),
            'tanda_identitas' => $fileContent,

        ]);
    
        return redirect()->route('undangan.'. Auth::user()->role->nm_role)->with('success', 'Dokumen berhasil dibuat.');
    }
    private function convertToRoman($number) {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$number];
    }
    public function updateDocumentStatus(Undangan $undangan) {
        $recipients = $undangan->recipients;
    
        if ($recipients->every(fn($recipient) => $recipient->status === 'approve')) {
            $undangan->update(['status' => 'approve']);
        } elseif ($recipients->contains(fn($recipient) => $recipient->status === 'reject')) {
            $undangan->update(['status' => 'reject']);
        } else {
            $undangan->update(['status' => 'pending']);
        }
    }
    
    public function updateDocumentApprovalDate(Undangan $undangan) {
        if ($undangan->status !== 'pending') {
            $undangan->update(['tanggal_disahkan' => now()]);
        }
    }
    public function approve(Undangan $undangan) {
        $undangan->update([
            'status' => 'approve',
            'tanggal_disahkan' => now() // Set tanggal disahkan
        ]);
    
        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }
    public function reject(Undangan $undangan) {
        $undangan->update([
            'status' => 'reject',
            'tanggal_disahkan' => now() // Set tanggal disahkan
        ]);
    
        return redirect()->back()->with('error', 'Dokumen ditolak.');
    }
    public function edit($id)
     {
         $undangan = Undangan::findOrFail($id);
         $divisi = Divisi::all();
         $seri = Seri::all();  
         
         return view(Auth::user()->role->nm_rol.'.undangan.edit-undangan', compact('undangan', 'divisi', 'seri'));
     }
     public function update(Request $request, $id)
     {
        $undangan = Undangan::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:70',
            'isi_undangan' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_undangan' => 'required|string|max:255',
            'nama_bertandatangan' => 'required|string|max:255',
            'tgl_dibuat' => 'required|date',
            'seri_surat' => 'required|numeric',
            'tgl_disahkan' => 'nullable|date',
            'divisi_id_divisi' => 'required|exists:divisi,id_divisi',
            'tanda_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ],[
            'tanda_identitas.mimes' => 'File harus berupa PDF, JPG, atau PNG.',
            'tanda_identitas.max' => 'Ukuran file tidak boleh lebih dari 2 MB.',
        ]);

        if ($request->filled('judul')) {
            $undangan->judul = $request->judul;
        }
        if ($request->filled('isi_memo')) {
            $undangan->isi_undangan = $request->isi_undangan;
        }
        if ($request->filled('tujuan')) {
            $undangan->tujuan = $request->tujuan;
        }
        if ($request->filled('nomor_undangan')) {
            $undangan->nomor_undangan = $request->nomor_undangan;
        }
        if ($request->filled('nama_bertandatangan')) {
            $undangan->nama_bertandatangan = $request->nama_bertandatangan;
        }
        if ($request->filled('tgl_surat')) {
            $undangan->tgl_dibuat = bcrypt($request->tgl_dibuat);
        }
        if ($request->filled('seri_surat')) {
            $undangan->seri_undangan = $request->seri_undangan;
        }
        if ($request->filled('tgl_disahkan')) {
            $undangan->tgl_disahkan = $request->tgl_disahkan;
        }
        if ($request->filled('divisi_id_divisi')) {
            $undangan->divisi_id_divisi = $request->divisi_id_divisi;
        }
        

        $undangan->save();
 
         return redirect()->route('undangan.'. Auth::user()->role->nm_role)->with('success', 'Undangan updated successfully');
     }
     public function destroy($id)
     {
         $undangan = Undangan::findOrFail($id);
         $undangan->delete();
 
         return redirect()->route('undangan.'. Auth::user()->role->nm_role)->with('success', 'Undangan deleted successfully.');
     }
}
