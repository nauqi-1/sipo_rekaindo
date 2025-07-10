<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Undangan Rapat</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/supervisor/view-undangan.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('undangan.manager')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Undangan Rapat</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="{{route ('undangan.manager')}}" >Undangan Rapat</a>/ <a href="#" style="color: #565656;">Detail Undangan Rapat</a>
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
                        <input type="text" id="seri" value="{{$undangan->seri_surat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="diterima">Diterima</label>
                        <div class="separator"></div>
                        <input type="text" id="diterima" value="{{ is_array($undangan->tujuan) ? implode('; ', $undangan->tujuan) : (string) $undangan->tujuan }}" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">Status Surat</label>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        @if ($undangan->final_status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($undangan->final_status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @elseif ($undangan->final_status == 'correction')
                            <span class="badge bg-danger">Dikoreksi</span>
                        @elseif ($undangan->final_status == 'approve')
                            <span class="badge bg-success">Diterima</span>
                        @else
                            <span class="badge bg-secondary">-</span>
                        @endif
                    </div>
                    <div class="card-white">
                        <label for="tanggal">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tanggal" value="{{$undangan->tgl_disahkan ? \Carbon\Carbon::parse($undangan->tgl_disahkan)->format('d-m-Y') : '-'  }}" readonly>
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
                        <input type="text" id="nomor" value="{{$undangan->nomor_undangan }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="divisi" value="{{$undangan->divisi->nm_divisi }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{$undangan->judul }}" readonly> 
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{$undangan->tgl_dibuat->translatedFormat('d F Y')}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="lampiran">Lampiran</label>

                        <div class="separator"></div>
                        <input type="text" id="kepada" value="{{ is_array($undangan->tujuan) ? implode('; ', $undangan->tujuan) : (string) $undangan->tujuan }}">
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="btn-file" onclick="window.location.href='{{ route('view-undanganPDF', $undangan->id_undangan) }}'"><img src="/img/mata.png" alt="view">Lihat</button>
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/undangan/info.png" alt="info surat">Informasi Pimpinan
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="status">Pengesahan</label>
                        <div class="separator"></div>
                        @if($undangan->divisi->id_divisi != Auth::user()->divisi->id_divisi)
                            @if ($undangan->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($undangan->status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @elseif ($undangan->status == 'correction')
                            <span class="badge bg-danger">Dikoreksi</span>
                        @else 
                            <span class="badge bg-success">Diterima</span>
                        @endif
                        @else
                            @if ($undangan->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($undangan->status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @elseif ($undangan->status == 'correction')
                            <span class="badge bg-danger">Dikoreksi</span>
                        @else 
                            <span class="badge bg-success">Diterima</span>
                        @endif
                        @endif
                    </div>
                    
                </div>
            </div>
            {{--  --}}
                @if ($undangan->status === 'pending' || $undangan->status === 'correction')
                <form id="approvalForm" method="POST" action="{{ route('undangan.updateStatus', $undangan->id_undangan) }}">
                @csrf
                @method('PUT')

                <div class="row mb-4" style="gap: 20px;">
                    <div class="col">
                        <div class="label1 card-blue1">
                            <label for="pengesahan" class="label">Pengesahan</label>
                            <div class="form-check1">
                                <label class="form-check-label" for="approve">Diterima</label>
                                <input type="radio" class="form-check-input approval-checkbox" id="approve" name="status" value="approve">
                            </div>
                            <div class="form-check2">
                                <label class="form-check-label" for="reject">Ditolak</label>
                                <input type="radio" class="form-check-input approval-checkbox" id="reject" name="status" value="reject">
                            </div>
                            <div class="form-check3">
                                <label class="form-check-label" for="correction">Dikoreksi</label>
                                <input type="radio" class="form-check-input approval-checkbox" id="correction" name="status" value="correction">
                            </div>
                        </div>
                    </div>

                    <div class="col" id="catatanCol" style="display:none;">
                        <div class="card-blue1">Catatan <span class="text-danger">*</span></div>
                        <textarea type="text" id="catatan" name="catatan" placeholder="Berikan Catatan"></textarea>        
                    </div>
                </div>

                <div class="row mb-4" id="tujuanDivisiRow" style="display:none;">
                    <div class="col">
                        <div class="card-blue1">
                            <label for="tindakan">Konfirmasi Daftar Penerima</label>
                            <label for="isi" style="color: #FF000080; font-size: 10px; margin-left: 5px;">
                                *Berikut adalah daftar divisi tujuan yang akan menerima undangan.
                            </label>
                        </div>
                        <div class="card-white">
                            <label for="diterima">Diterima</label>
                            <div class="separator"></div>
                            <ol>
                            @foreach(explode(';', $undangan->tujuan) as $divisi)
                                <li>{{ trim($divisi) }}</li>
                            @endforeach
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <button type="button" class="btn back" id="backBtn" onclick="window.location.href='{{ route('undangan.manager') }}'">Kembali</button>
                    <button type="button" class="btn submit" id="submitBtn">Kirim</button>
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
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><a href="{{route ('undangan.manager')}}" style="color: white; text-decoration: none">Kembali ke Halaman Undangan</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.approval-checkbox');
        const catatanCol = document.getElementById('catatanCol');
        const catatanInput = document.getElementById('catatan');
        const tujuanDivisiRow = document.getElementById('tujuanDivisiRow');
        const submitBtn = document.getElementById('submitBtn');
        let statusValue = null;

        // Radio button logic
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
                statusValue = this.value;
                if (statusValue === 'approve') {
                    catatanCol.style.display = 'none';
                    catatanInput.required = false;
                    tujuanDivisiRow.style.display = 'flex';
                } else if (statusValue === 'reject' || statusValue === 'correction') {
                    catatanCol.style.display = 'block';
                    catatanInput.required = true;
                    tujuanDivisiRow.style.display = 'none';
                } else {
                    catatanCol.style.display = 'none';
                    catatanInput.required = false;
                    tujuanDivisiRow.style.display = 'none';
                }
            });
        });

        // Submit button logic
        submitBtn.addEventListener('click', function () {
            if (!statusValue) {
                alert('Pilih status pengesahan terlebih dahulu!');
                return;
            }
            // Untuk approve, tetap submit dan tampilkan modal sukses (biarkan reload)
            if (statusValue === 'approve') {
                document.getElementById('approvalForm').submit();
                setTimeout(function() {
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                }, 300);
            } else {
                // Untuk reject/correction, submit lalu redirect manual ke halaman undangan manager setelah submit
                document.getElementById('approvalForm').submit();
                setTimeout(function() {
                    window.location.href = "{{ route('undangan.manager') }}";
                }, 500);
            }
        });

        // Jika ada notifikasi sukses dari server, tampilkan modal sukses (fallback jika redirect back)
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
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const diterimaCheckbox = document.getElementById('approve');
        const tindakLanjutSelect = document.getElementById('nextAction');
        const formPengiriman = document.getElementById('formPengiriman');

        function togglePengiriman() {
            if (diterimaCheckbox.checked && tindakLanjutSelect.value === 'dilanjutkan') {
                formPengiriman.style.display = 'block';
            } else {
                formPengiriman.style.display = 'none';
                document.getElementById('posisi_penerima').value = '';
                document.getElementById('divisi_penerima').value = '';

            }
        }

        diterimaCheckbox.addEventListener('change', togglePengiriman);
        tindakLanjutSelect.addEventListener('change', togglePengiriman);
    });
    document.addEventListener('DOMContentLoaded', function () {
    const formPengiriman = document.getElementById('formPengiriman');
    if (formPengiriman) {
        formPengiriman.style.display = 'none';
    }
});
</script>
</html>