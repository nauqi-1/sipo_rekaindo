<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Arsip Undangan Superadmin</title>
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
                <a href="{{route ('arsip.undangan')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Detail Arsip Undangan</h1>
        </div>
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                <a href="{{route(Auth::user()->role->nm_role.'.dashboard')}}">Beranda</a>/
                <a href="{{route ('arsip.undangan')}}">Arsip Undangan</a>/
                <a style="color:#565656" href="#">Detail Arsip Undangan</a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/memo-admin/info.png" alt="date">Informasi Detail Undangan Rapat
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor" value="{{$undangan->nomor_undangan}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="seri">Seri Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{$undangan->seri_surat}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{$undangan->judul}}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl_rapat">Hari, tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl_rapat" value="{{\Carbon\Carbon::parse($undangan->tgl_rapat)->translatedFormat('l, d F Y')}}" readonly>
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
                        <input type="text" id="pembuat" value="{{ $undangan->user ? $undangan->user->firstname . ' ' . $undangan->user->lastname : 'N/A' }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>

                            @if($undangan->pembuat != Auth::user()->id)
                            @if ($undangan->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($undangan->status == 'pending')
                            <span class="badge bg-info">Diproses</span>
                        @elseif ($undangan->status == 'correction')
                            <span class="badge bg-warning">Dikoreksi</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                        @else
                            @if ($undangan->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($undangan->status == 'pending')
                            <span class="badge bg-info">Diproses</span>
                        @elseif ($undangan->status == 'correction')
                            <span class="badge bg-warning">Dikoreksi</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                        @endif
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="view" onclick="window.location.href='{{ route('view-undanganPDF', $undangan->id_undangan) }}'"> <img src="/img/memo-admin/view.png" alt="view">Lihat</button>
                        @if ($undangan->status=='approve')
                        <a style="text-decoration: none;" class="down" onclick="window.location.href='{{ route('cetakundangan',['id' => $undangan->id_undangan]) }}'"><img src="/img/memo-admin/down.png" alt="down">Unduh</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="diterima" class="form-label">
                            <img src="/img/memo-admin/detail.png" alt="date" style="margin-right: 5px;">Daftar Tujuan
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="diterima">Diterima</label>
                        <div class="separator"></div>
                        <pre style="font-family: Arial, sans-serif; font-size: 15px;padding: 10px 15px;">{{ $undangan->tujuan }}</pre>
                    </div>
                </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue1">Catatan</div>
                    <textarea type="text" for="catatan" id="catatan"  readonly>{{$undangan->catatan}}</textarea>
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
                setTimeout(function () {
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                }, 300);
            } else {
                // Untuk reject/correction, submit lalu redirect manual ke halaman undangan manager setelah submit
                document.getElementById('approvalForm').submit();
                setTimeout(function () {
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
