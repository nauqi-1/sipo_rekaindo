<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Undangan Rapat Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/kirim-admin.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('undangan.admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Kirim Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route('admin.dashboard') }}">Beranda</a>/<a href="{{route ('undangan.admin')}}">Undangan</a>/<a href="#" style="color: #565656;">Kirim Undangan Rapat</a>
                </div>
            </div>
        </div>
        <form id="undanganSend" action="{{ route('documents.send') }}" method="POST">
        @csrf
        <input type="hidden" name="id_document" value="{{ $undangan->id_undangan }}">
        <input type="hidden" name="jenis_document" value="undangan">
        <div class="card-body">
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/undangan/info.png" alt="date">Informasi Detail Undangan Rapat
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor" value="{{ $undangan->nomor_undangan }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="seri">Seri Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{ $undangan->seri_surat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $undangan->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $undangan->tgl_dibuat->translatedFormat('d F Y') }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="kepada">Kepada</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada"  value="{{ $undangan->tujuan }}" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/undangan/detail.png" alt="date" style="margin-right: 5px;">Detail
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="pembuat">Pembuat</label>
                        <div class="separator"></div>
                        <input type="text" id="pembuat"  value="{{ $undangan->pembuat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        @if ($undangan->final_status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($undangan->final_status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                    </div>
                    <div class="card-white">
                        <label for="tgl-buat">Dibuat Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl-buat"  value="{{ $undangan->tgl_dibuat->translatedFormat('d F Y')}}" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue1">
                        <label for="tindakan">Tindakan Selanjutnya</label>
                        <label for="isi" style="color: #FF000080; font-size: 10px; margin-left: 5px;">
                            *Pilih opsi posisi dan divisi tujuan untuk mengirimkan undangan kepada pihak yang dituju.
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="posisi_penerima">Posisi Penerima</label>
                        <div class="separator"></div>
                        <select name="posisi_penerima" id="posisi_penerima" class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" id="dropdownMenuButton">
                            <option disabled selected style="text-align: left;">--Pilih--</option>
                            @foreach($position as $p)
                                <option value="{{ $p->id_position }}">{{ $p->nm_position }}</option>
                            @endforeach
                        </select>                    
                    </div>
                    <div class="card-white">
                        <label for="divisi_penerima">Divisi Penerima</label>
                        <div class="separator"></div>
                        <select name="divisi_penerima" id="divisi_penerima" class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" id="dropdownMenuButton">
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
            <button type="button" class="btn back" id="backBtn" onclick="window.location.href='{{ route('undangan.admin') }}'">Kembali</button>
            <button type="button" class="btn submit" id="submitBtn" data-bs-toggle="modal" data-bs-target="#submit">Kirim</button>
        </div>
        </form>

        <!-- Modal kirim -->
        <div class="modal fade" id="submit" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <!-- Close Button -->
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                        <img src="/img/undangan/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
                        <h5 class="modal-title mb-4"><b>Kirim Undangan Rapat?</b></h5>
                        <div class="d-flex justify-content-center mt-3">
                            <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="confirmSubmit" data-bs-toggle="modal">Oke</button>
                        </div>    
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Berhasil -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <img src="/img/undangan/success.png" alt="Success Icon" class="my-3" style="width: 80px;">
                        <!-- Success Message -->
                        <h5 class="modal-title"><b>Sukses</b></h5>
                        <p class="mt-2">Berhasil Mengirimkan Undangan</p>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><a href="{{route ('undangan.admin')}}" style="color: white; text-decoration: none">Kembali ke Halaman Undangan</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
    <script>
    // Overlay kirim
    document.addEventListener('DOMContentLoaded', function () {
        const undanganSend = document.getElementById('undanganSend');
        const confirmSubmitButton = document.getElementById('confirmSubmit');

        confirmSubmitButton.addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah submit default
            
            // Kirim form secara normal
            undanganSend.submit();
        });

        // Jika ada notifikasi sukses dari server, tampilkan modal sukses
        const successMessage = "{{ session('success') }}";
        if (successMessage) {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        }
    });
    </script>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>