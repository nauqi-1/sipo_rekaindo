<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Risalah Setuju </title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/supervisor/view-memoDiterima.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('risalah.manager')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Kirim Risalah Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route('manager.dashboard') }}">Beranda</a>/<a href="{{route ('risalah.manager')}}">Risalah Rapat</a>/<a href="#" style="color: #565656;">Kirim Risalah Rapat</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">No Agenda</label>
                    </div>
                    <div class="card-white">
                        <label for="seri">No Seri</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{ $risalah->seri_surat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="diterima">Diterima</label>
                        <div class="separator"></div>
                        <input type="text"
                            id="diterima"
                            value="{{ str_replace(';', ', ', $undangan->tujuan) }}"
                            title="{{ str_replace(';', ', ', $undangan->tujuan) }}"
                            readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">Status Surat</label>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        @if($risalah->status == 'pending')
                            <button class="status">Diproses</button>
                        @elseif($risalah->status == 'approve')
                            <button class="status">Diterima</button>
                        @elseif($risalah->status == 'reject')
                            <button class="status">Ditolak</button>
                        @elseif($risalah->status == 'correction')
                            <button class="status">Dikoreksi</button>
                        @endif
                    </div>
                    <div class="card-white">
                        <label for="tanggal">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tanggal" value="{{ $risalah->tgl_disahkan ? \Carbon\Carbon::parse($risalah->tgl_disahkan)->format('d-m-Y') : '-'  }}" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/undangan/info.png" alt="info surat">Informasi Detail Surat
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Dokumen</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor" value="{{ $risalah->nomor_risalah }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="divisi" value="{{ $risalah->divisi->nm_divisi }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $risalah->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $risalah->tgl_dibuat->translatedFormat('d F Y')  }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="btn-file"  onclick="window.location.href='{{ route('view-risalahPDF', $risalah->id_risalah) }}'"><img src="/img/mata.png" alt="view">Lihat</button>
                    </div>
                    <!-- <div class="card-white">
                        <label for="lampiran">Lampiran</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada">
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="btn-file"><img src="/img/mata.png" alt="view"><a href="#">Lihat</a></button>
                        <button class="down btn-file"><img src="/img/download.png" alt="down"><a href="#">Unduh</a></button>
                    </div> -->
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($risalah->status == 'pending' || $risalah->status == 'reject' || $risalah->status == 'correction')
                <form id="approvalForm" method="POST" action="{{ route('risalah.updateStatus', $risalah->id_risalah) }}">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4" style="gap: 20px;">
                        <div class="col">
                            <div class="label1 card-blue1">
                                <label for="pengesahan" class="label">Pengesahan</label>
                                
                                <div class="form-check1">
                                    <label class="form-check-label" for="approve">Diterima</label>
                                    <input type="radio" class="form-check-input approval-checkbox" id="approve" name="status" value="approve"
                                        {{ $risalah->status == 'approve' ? 'checked' : '' }}>
                                </div>
                                <div class="form-check2">
                                    <label class="form-check-label" for="reject">Ditolak</label>
                                    <input type="radio" class="form-check-input approval-checkbox" id="reject" name="status" value="reject"
                                        {{ $risalah->status == 'reject' ? 'checked' : '' }}>
                                </div>
                                <div class="form-check3">
                                    <label class="form-check-label" for="correction">Dikoreksi</label>
                                    <input type="radio" class="form-check-input approval-checkbox" id="correction" name="status" value="correction"
                                        {{ $risalah->status == 'correction' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="col" id="catatanSection" style="{{ $risalah->status == 'correction' || $risalah->status == 'reject' ? '' : 'display: none;' }}">
                            <div class="card-blue1">Catatan</div>
                            <textarea type="text" id="catatan" name="catatan" placeholder="Berikan Catatan">{{ $risalah->catatan }}</textarea>        
                        </div>             
                    </div>
                <div class="footer">
                    <button type="button" class="btn back" id="backBtn" onclick="window.location.href='{{ route('risalah.manager') }}'">Kembali</button>
                    <button type="button" class="btn submit" id="submitBtn" data-bs-toggle="modal" data-bs-target="#submit">Kirim</button>
                </div>
            </form>
        @endif
        <!-- Modal kirim -->
        <div class="modal fade" id="submit" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <!-- Close Button -->
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                        <img src="/img/undangan/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
                        <h5 class="modal-title mb-4"><b>Kirim Risalah Rapat?</b></h5>
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
                        <p class="mt-2">Berhasil Mengirimkan Risalah Rapat</p>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><a href="{{route ('risalah.manager')}}" style="color: white; text-decoration: none">Kembali ke Halaman Risalah</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('input[name="status"]');
        const catatanSection = document.getElementById('catatanSection');

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'correction' || this.value === 'reject') {
                    catatanSection.style.display = 'block';
                } else {
                    catatanSection.style.display = 'none';
                }
            });
        });
    });
        
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.approval-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            });
        });
    });

    // Overlay kirim
    document.addEventListener('DOMContentLoaded', function () {
        const approvalForm = document.getElementById('approvalForm');
        const confirmSubmitButton = document.getElementById('confirmSubmit');

        confirmSubmitButton.addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah submit default
            
            // Kirim form secara normal
            approvalForm.submit();
        });

        // Jika ada notifikasi sukses dari server, tampilkan modal sukses
        const successMessage = "{{ session('success') }}";
        if (successMessage) {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        }
    });

    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>