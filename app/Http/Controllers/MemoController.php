<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\kategori_barang;
use App\Models\Memo;
use App\Models\Seri;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class MemoController extends Controller
{
    public function index()
    {
        $divisi = Divisi::all();
        $seri = Seri::all();  
        $memos = Memo::with('divisi')->orderBy('tgl_dibuat', 'desc')->paginate(6);
        
    
        return view( Auth::user()->role->nm_role.'.memo.memo-'. Auth::user()->role->nm_role, compact('memos','divisi','seri'));
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

       

    return view(Auth::user()->role->nm_role.'.memo.add-memo', [
        'nomorSeriTahunan' => $nextSeri['seri_tahunan'], // Tambahkan nomor seri tahunan
        'nomorDokumen' => $nomorDokumen,
        'managers' => $managers
    ]);  
    }
    public function store(Request $request)
    {
        //  dd($request->all());


        $request->validate([
            'judul' => 'required|string|max:70',
            'isi_memo' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_memo' => 'required|string|max:255',
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
        $memo = Memo::create([
            'divisi_id_divisi' => $request->input('divisi_id_divisi'),
            'judul' => $request->input('judul'),
            'tujuan' => $request->input('tujuan'),
            'isi_memo' => $request->input('isi_memo'),
            'nomor_memo' => $request->input('nomor_memo'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'seri_surat' => $request->input('seri_surat'),
            'status' => 'pending',
            'nama_bertandatangan' => $request->input('nama_bertandatangan'),
            'tanda_identitas' => $fileContent,

        ]);
        if ($request->has('jumlah_kolom')&& !empty($request->nomor)) {
            foreach ($request->nomor as $key => $nomor) {
                kategori_barang::create([
                    'memo_id_memo' => $memo->id_memo,
                    'memo_divisi_id_divisi' => $memo->divisi_id_divisi, // Gunakan dari memo
                    'nomor' => $nomor, // Ambil dari array
                    'barang' => $request->barang[$key] ?? null,
                    'qty' => $request->qty[$key] ?? null,
                    'satuan' => $request->satuan[$key] ?? null,
                ]);
            }
        }
    
        return redirect()->route('memo.'. Auth::user()->role->nm_role)->with('success', 'Dokumen berhasil dibuat.');
    }
    private function convertToRoman($number) {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $map[$number];
    }
    public function updateDocumentStatus(Memo $memo) {
        $recipients = $memo->recipients;
    
        if ($recipients->every(fn($recipient) => $recipient->status === 'approve')) {
            $memo->update(['status' => 'approve']);
        } elseif ($recipients->contains(fn($recipient) => $recipient->status === 'reject')) {
            $memo->update(['status' => 'reject']);
        } else {
            $memo->update(['status' => 'pending']);
        }
    }
    
    public function updateDocumentApprovalDate(Memo $memo) {
        if ($memo->status !== 'pending') {
            $memo->update(['tanggal_disahkan' => now()]);
        }
    }
    public function approve(Memo $memo) {
        $memo->update([
            'status' => 'approve',
            'tanggal_disahkan' => now() // Set tanggal disahkan
        ]);
    
        return redirect()->back()->with('success', 'Dokumen berhasil disetujui.');
    }
    public function reject(Memo $memo) {
        $memo->update([
            'status' => 'reject',
            'tanggal_disahkan' => now() // Set tanggal disahkan
        ]);
    
        return redirect()->back()->with('error', 'Dokumen ditolak.');
    }
    public function edit($id)
     {
         $memo = Memo::findOrFail($id);
         $divisi = Divisi::all();
         $seri = Seri::all();  
         
         return view(Auth::user()->role->nm_role. '.memo.edit-memo', compact('memo', 'divisi', 'seri'));
     }
     public function update(Request $request, $id)
     {
        $memo = Memo::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:70',
            'isi_memo' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_memo' => 'required|string|max:255',
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
            $memo->judul = $request->judul;
        }
        if ($request->filled('isi_memo')) {
            $memo->isi_memo = $request->isi_memo;
        }
        if ($request->filled('tujuan')) {
            $memo->tujuan = $request->tujuan;
        }
        if ($request->filled('nomor_memo')) {
            $memo->nomor_memo = $request->nomor_memo;
        }
        if ($request->filled('nama_bertandatangan')) {
            $memo->nama_bertandatangan = $request->nama_bertandatangan;
        }
        if ($request->filled('tgl_surat')) {
            $memo->tgl_dibuat = bcrypt($request->tgl_dibuat);
        }
        if ($request->filled('seri_surat')) {
            $memo->seri_memo = $request->seri_memo;
        }
        if ($request->filled('tgl_disahkan')) {
            $memo->tgl_disahkan = $request->tgl_disahkan;
        }
        if ($request->filled('divisi_id_divisi')) {
            $memo->divisi_id_divisi = $request->divisi_id_divisi;
        }
        

        $memo->save();
 
         return redirect()->route('memo.'.Auth::user()->role->nm_role)->with('success', 'User updated successfully');
     }
     public function destroy($id)
     {
         $memo = Memo::findOrFail($id);
         $memo->delete();
 
         return redirect()->route('memo.' .Auth::user()->role->nm_role)->with('success', 'Memo deleted successfully.');
     }
     
}
