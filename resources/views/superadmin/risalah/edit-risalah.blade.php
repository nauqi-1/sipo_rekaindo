<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Risalah Rapat Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/edit-risalah.css') }}">
</head> 
<body>
    <div class="container">
        <div class="header">
            <div class="back-button">
                <a href="{{ route('risalah.superadmin') }}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Risalah Rapat</h1>
        </div>
        
        <form action="{{ route('risalah.update', $risalah->id_risalah) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="font-size: 18px;"><b>Formulir Edit Risalah Rapat</b></h5>
            </div>
            <div class="card-body">
                <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="tgl_dibuat">Tgl. Surat</label>
                            <input type="date" name="tgl_dibuat" class="form-control" required>
                            <input type="hidden" name="tgl_disahkan" >
                        </div>
                        <div class="col-md-6">
                            <label for="seri_surat">Seri Surat</label>
                            <input type="text" name="seri_surat" id="seri_surat" class="form-control" value="{{ $risalah->seri_surat }}" readonly>
                            <input type="hidden" name="divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">
                            <input type="hidden" name="pembuat" value="{{ auth()->user()->position->nm_position .' '. auth()->user()->role->nm_role }}">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="nomor_risalah">Nomor Surat</label>
                            <input type="text" name="nomor_risalah" id="nomor_risalah" class="form-control" value="{{ $risalah->nomor_risalah }}" readonly>
                        </div>
                        <div class="col-md-6">
                                <label for="judul" class="form-label">Judul</label>
                                <input type="text" name="judul" id="judul" class="form-control" value="{{ $risalah->judul }}" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label for="agenda">Agenda</label>
                            <input type="text" name="agenda" id="agenda" class="form-control" value="{{ $risalah->agenda }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tempat">Tempat</label>
                            <input type="text" name="tempat" id="tempat" class="form-control" value="{{ $risalah->tempat }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="waktu" class="form-label">Waktu</label>
                            <div class="d-flex align-items-center">
                                <input type="text" name="waktu_mulai" id="waktu_mulai" class="form-control me-2" placeholder="waktu mulai" value="{{ $risalah->waktu_mulai }}" required>
                                <span class="fw-bold">s/d</span>
                                <input type="text" name="waktu_selesai" id="waktu_selesai" class="form-control ms-2" placeholder="waktu selesai" value="{{ $risalah->waktu_selesai }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="tujuan" class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" id="tujuan" class="form-control" value="{{ $risalah->tujuan }}" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                            <label for="nama_bertandatangan">Nama yang Bertanda Tangan</label>
                            <select name="nama_bertandatangan" id="nama_bertandatangan" class="form-control select2" required>
                                <option value="" disabled>--Pilih--</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->firstname . ' ' . $manager->lastname }}" 
                                        {{ (old('nama_bertandatangan', $risalah->nama_bertandatangan ?? '') == $manager->firstname . ' ' . $manager->lastname) ? 'selected' : '' }}>
                                        {{ $manager->firstname . ' ' . $manager->lastname }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                    </div>
                    <div id="risalahContainer">
                    @if(isset($risalah->risalahDetails) && $risalah->risalahDetails->count() > 0)
                        @foreach ($risalah->risalahDetails as $detail)
                <div class="isi-surat-row">
                    <div class="col-md-1">
                        <label for="no">No</label>
                        <textarea class="form-control no-auto" name="nomor[]" rows="2" readonly>{{ $detail->nomor }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label for="topik">Topik</label>
                        <textarea class="form-control" name="topik[]" rows="2">{{ $detail->topik }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label for="pembahasan">Pembahasan</label>
                        <textarea class="form-control" name="pembahasan[]" rows="2">{{ $detail->pembahasan }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label for="tindak-lanjut">Tindak Lanjut</label>
                        <textarea class="form-control" name="tindak_lanjut[]" rows="2">{{ $detail->tindak_lanjut }}</textarea>
                    </div>
                    <div class="col-md-2">
                        <label for="target">Target</label>
                        <textarea class="form-control" name="target[]" rows="2">{{ $detail->target }}</textarea>
                    </div>
                    <div class="col-md-2">
                        <label for="pic">PIC</label>
                        <textarea class="form-control" name="pic[]" rows="2">{{ $detail->pic }}</textarea>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
                <div class="card-tambah" style="margin-bottom: 10px;">
                <button type="button" class="btn btn-tambah" id="tambahIsiRisalahBtn">Tambah Isi Risalah</button>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-cancel"><a href="{{route ('risalah.superadmin')}}">Batal</a></button>
                <button type="submit" class="btn btn-save">Simpan</button>
            </div>
        </div>
        </form>
    </div>
    
    <!-- Modal Upload File -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">
                        <img src="/img/risalah/cloud-add.png" alt="Icon" style="width: 24px; margin-right: 10px;">
                        Unggah file
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-subtitle">Pilih dan unggah file pilihan Anda</p>
                    <div class="upload-container">
                        <div class="upload-box" id="uploadBox">
                            <img src="/img/risalah/cloud-add.png" alt="Cloud Icon" style="width: 40px; margin-bottom: 10px;">
                            <p class="upload-text">Pilih file atau seret & letakkan di sini</p>
                            <p class="upload-note">Ukuran file PDF tidak lebih dari 20MB</p>
                            <button class="btn btn-outline-primary" id="selectFileBtn">Pilih File</button>
                            <input type="file" id="fileInput" accept=".pdf" style="display: none;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="uploadBtn">Unggah</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
    $('.select2').select2({
        placeholder: "Pilih Nama",
        allowClear: true
    });
});

        $(document).ready(function() {
            $('#dropdownMenuButton').on('change', function() {
                $(this).css('text-align', 'left');
                if($(this).val() === null || $(this).val() === "") {
                    $(this).css('text-align', 'center');
                }
            });
        });

        document.getElementById('selectFileBtn').addEventListener('click', function () {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function () {
    const file = this.files[0];
    const allowedFormats = ['application/pdf', 'image/jpeg', 'image/png'];
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (file) {
        if (!allowedFormats.includes(file.type)) {
            alert('Format file tidak didukung. Harap unggah PDF, JPG, atau PNG.');
            this.value = "";
        } else if (file.size > maxSize) {
            alert('Ukuran file melebihi 2MB.');
            this.value = "";
        } else {
            document.getElementById('uploadBtn').disabled = false;
        }
    }
});


        $(document).ready(function() {
            $('#summernote').summernote({
                height: 300,
                toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear', 'fontname', 'fontsize', 'color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
                ],
                fontNames: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman'], 
                fontNamesIgnoreCheck: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman']
            });
        });

    document.getElementById('tambahIsiRisalahBtn').addEventListener('click', function(event) {
        event.preventDefault();

    var risalahContainer = document.getElementById('risalahContainer');
    var newRow = document.createElement('div');
    newRow.classList.add('isi-surat-row', 'row', 'align-items-center');
    newRow.style.gap = '0';

    newRow.innerHTML = `
        <div class="col-md-1">
            <textarea class="form-control no-auto" name="nomor[]" rows="2" readonly></textarea>
        </div>
        <div class="col-md-3">
            <textarea class="form-control" name="topik[]" placeholder="Topik Pembahasan" rows="2"></textarea>
        </div>
        <div class="col-md-3">
            <textarea class="form-control" name="pembahasan[]" placeholder="Pembahasan" rows="2"></textarea>
        </div>
        <div class="col-md-3">
            <textarea class="form-control" name="tindak_lanjut[]" placeholder="Tindak Lanjut" rows="2"></textarea>
        </div>
        <div class="col-md-2">
            <textarea class="form-control" name="target[]" placeholder="Target" rows="2"></textarea>
        </div>
        <div class="col-md-2">
            <textarea class="form-control" name="pic[]" placeholder="PIC" rows="2"></textarea>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
        </div>
    `;

    risalahContainer.appendChild(newRow);
    updateNomor();
});

function updateNomor() {
    const nomorInputs = document.querySelectorAll('.isi-surat-row .no-auto');
    nomorInputs.forEach((input, index) => {
        input.value = index + 1;
    });
}

document.addEventListener('click', function(event) {
    if (event.target.classList.contains('remove-row')) {
        event.preventDefault();
        event.target.closest('.isi-surat-row').remove();
        updateNomor();
    }
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>