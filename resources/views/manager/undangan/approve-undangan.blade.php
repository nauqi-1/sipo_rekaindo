<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Undangan Rapat</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/css/supervisor/approve-undangan.css') }}">
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
                    <a href="#">Beranda</a>/<a href="{{route ('undangan.manager')}}">Undangan Rapat</a>/<a href="#" style="color: #565656;">Tindak Lanjut Undangan</a>
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
                        <input type="text" id="seri" value="{{ $undangan->seri_surat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="diterima">Diterima</label>
                        <div class="separator"></div>
                        <input type="text" id="diterima" value="{{ $undangan->tujuan }}" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">Status Surat</label>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        <button class="status">Diproses</button>
                    </div>
                    <div class="card-white">
                        <label for="tanggal">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tanggal" value="{{ $undangan->tgl_disahkan ? \Carbon\Carbon::parse($undangan->tgl_disahkan)->format('d-m-Y') : '-'  }}" readonly>
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
                        <input type="text" id="nomor" value="{{ $undangan->nomor_undangan }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="divisi" value="{{ $undangan->divisi->nm_divisi }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $undangan->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $undangan->tgl_dibuat }}" readonly>
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
                    <div class="card-blue1">Konfirmasi Pengiriman</div>
                    <div class="card-white">
                        <label>Undangan akan dikirim ke divisi berikut:</label>
                        <ul>
                            @foreach(explode(';', $undangan->tujuan) as $divisiTujuan)
                                <li>{{ trim($divisiTujuan) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="footer">
                <button type="button" class="btn back" id="backBtn">Kembali</button>
                <button type="button" class="btn submit" id="submitBtn">Kirim</button>
            </div>
        </form>

        <!-- Modal kirim hanya untuk diterima, akan dihandle via JS -->

        <!-- Modal Berhasil -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Tombol Close -->
                    <button type="button" class="btn-close ms-auto m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body text-center">
                        <!-- Ikon atau Gambar -->
                        <img src="/img/undangan/success.png" alt="Berhasil Ikon" class="mb-3" style="width: 80px;">
                        <!-- Tulisan -->
                        <h5 class="mb-4" style="color: #545050;"><b>Berhasil Mengirim</b></h5>
                        <!-- Tombol -->
                        <button type="button" class="btn backPage" data-bs-dismiss="modal"><a href="{{route ('undangan.manager')}}">Kembali</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                } else if (statusValue === 'reject' || statusValue === 'pending') {
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
            if (statusValue === 'approve') {
                // Tampilkan konfirmasi tujuan divisi sebelum submit
                if (confirm('Undangan akan dikirim ke divisi tujuan. Lanjutkan?')) {
                    document.getElementById('approvalForm').submit();
                }
            } else {
                // Untuk reject/pending, langsung submit (catatan required dihandle oleh browser)
                document.getElementById('approvalForm').submit();
            }
        });
    });
    </script>
</body>
</html>