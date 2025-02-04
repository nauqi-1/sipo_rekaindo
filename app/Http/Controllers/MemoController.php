<?php

namespace App\Http\Controllers;

use App\Models\kategori_barang;
use App\Models\Memo;
use App\Models\Seri;
use App\Models\User;
use Illuminate\Http\Request;

class MemoController extends Controller
{
    public function create()
    {
        $divisiId = auth()->user()->divisi_id_divisi;
    $divisiName = auth()->user()->divisi->nm_divisi;

    // Ambil nomor seri berikutnya
    $seri = Seri::getNextSeri($divisiId);

    // Konversi bulan ke angka Romawi
    $bulanRomawi = $this->convertToRoman(now()->month);

    // Format nomor dokumen
    $nomorDokumen = sprintf(
        "%d.%d/REKA/GEN/%s/%s/%d",
        $seri['nomor_bulanan'],
        $seri['nomor_tahunan'],
        strtoupper($divisiName),
        $bulanRomawi,
        now()->year
    );

    $managers = User::where('divisi_id_divisi', $divisiId)
        ->where('position_id_position', '2')
        ->get(['id', 'firstname', 'lastname']);

    return view('superadmin.memo.add-memo', [
        'nomorSeriTahunan' => $seri['nomor_tahunan'], // Tambahkan nomor seri tahunan
        'nomorDokumen' => $nomorDokumen,
        'managers' => $managers
    ]);  
    }
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:70',
            'isi_memo' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'nomor_memo' => 'required|string|max:255',
            'nama_pimpinan' => 'required|string|max:255',
            'tgl_dibuat' => 'required|date',
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

        // Simpan dokumen
        $memo = Memo::create([
            'divisi_id_divisi' => $request->input('divisi_id_divisi'),
            'judul' => $request->input('judul'),
            'tujuan' => $request->input('tujuan'),
            'isi_memo' => $request->input('isi_document'),
            'nomor_memo' => $request->input('nomor_document'),
            'tgl_dibuat' => $request->input('tgl_dibuat'),
            'tgl_disahkan' => $request->input('tgl_disahkan'),
            'status' => 'pending',
            'nama_pimpinan' => $request->input('nama_pimpinan'),
            'tanda_identitas' => $fileContent,

        ]);
        if ($request->has('jumlah_kolom')) {
            for ($i = 0; $i < $request->jumlah_kolom; $i++) {
                kategori_barang::create([
                    'document_id_document' => $memo->id_memo,
                    'nomor' => $request->input('nomor_' . $i),
                    'barang' => $request->input('barang_' . $i),
                    'qty' => $request->input('qty_' . $i),
                    'satuan' => $request->input('satuan_' . $i),
                    
                ]);
            }
        }
    
        return redirect()->route('memo.superadmin')->with('success', 'Dokumen berhasil dibuat.');
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
}
