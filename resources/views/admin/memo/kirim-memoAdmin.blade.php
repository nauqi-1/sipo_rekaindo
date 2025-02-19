<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Memo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/admin/kirim-admin.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('memo.admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Kirim Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="#">Memo</a>/<a href="#" style="color: #565656;">Kirim Memo</a>
                </div>
            </div>
        </div>
        <form action="{{ route('documents.send') }}" method="POST">
        @csrf
        <input type="hidden" name="id_document" value="{{ $memo->id_memo }}">
        <input type="hidden" name="jenis_document" value="memo">
        <div class="card-body">
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/memo-admin/info.png" alt="date">Informasi Detail Memo
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor" value="{{ $memo->nomor_memo }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="seri">Seri Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{ $memo->seri_surat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $memo->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $memo->tgl_dibuat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="kepada">Kepada</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada" value="{{ $memo->tujuan }}" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/memo-admin/detail.png" alt="date" style="margin-right: 5px;">Detail
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="pembuat">Pembuat</label>
                        <div class="separator"></div>
                        <input type="text" id="pembuat" value="{{ $memo->pembuat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        <button class="status">Diproses</button>
                    </div>
                    <div class="card-white">
                        <label for="tgl-buat">Dibuat Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl-buat" value="{{ $memo->tgl_dibuat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="view"> <img src="/img/memo-admin/view.png" alt="view">Lihat</button>
                        <button class="down"><img src="/img/memo-admin/down.png" alt="down">Unduh</button>
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue1">
                        <label for="tindakan">Tindakan Selanjutnya</label>
                        <label for="isi" style="color: #FF000080; font-size: 10px; margin-left: 5px;">
                            *Pilih opsi posisi dan divisi tujuan untuk mengirimkan memo kepada pihak yang dituju.
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="posisi_penerima">Posisi Penerima</label>
                        <div class="separator"></div>
                        <select name="posisi_penerima" id="posisi_penerima" class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" required autofocus autocomplete="posisi_penerima">
                            <option disabled selected style="text-align: left;">--Pilih--</option>
                            @foreach($position as $p)
                                <option value="{{ $p->id_position }}">{{ $p->nm_position }}</option>
                            @endforeach
                        </select>                    
                    </div>
                    <div class="card-white">
                        <label for="divisi_penerima" class="form-label">Divisi Penerima</label>
                        <div class="separator"></div>
                         <select name="divisi_penerima" id="divisi_penerima" class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" required autofocus autocomplete="divisi_penerima">
                         <option disabled selected style="text-align: left;">--Pilih--</option>
                         @foreach($divisi as $d)
                            <option value="{{ $d->id_divisi }}">{{ $d->nm_divisi }}</option>
                        @endforeach
                        </select>                  
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <button type="button" class="btn back" id="backBtn">Kembali</button>
            <button type="submit" class="btn submit" id="submitBtn" data-bs-toggle="modal" data-bs-target="#submit">Kirim</button>
        </div>
        </form>

        <!-- Modal kirim -->
        <div class="modal fade" id="submit" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/memo-admin/konfirmasi.png" alt="Hapus Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Kirim Memo?</b></h5>
                        <!-- Tombol -->
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn cancel" data-bs-dismiss="modal"><a href="#">Batal</a></button>
                            <button type="button" class="btn ok" id="confirmDelete">Oke</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Berhasil -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/memo-admin/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Berhasil Mengirimkan Memo</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn backPage" data-bs-dismiss="modal"><a href="{{route ('memo.admin')}}">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $("#confirmDelete").click(function () {
                // Tutup modal kirim
                $("#submit").modal("hide");

                // Tunggu sebentar sebelum menampilkan modal berhasil
                setTimeout(function () {
                    $("#successModal").modal("show");
                }, 500); // Delay 500ms agar transition lebih smooth
            });
        });
    </script>
</body>
</html>