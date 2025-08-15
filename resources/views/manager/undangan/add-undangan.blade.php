<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Undangan Rapat Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/admin/edit-memo.css') }}">
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route('undangan.' . Auth::user()->role->nm_role)}}"><img
                        src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Tambah Undangan Rapat</h1>
        </div>
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route(Auth::user()->role->nm_role . '.dashboard') }}">Beranda</a>/<a
                        href="{{ route('undangan.admin') }}">Undangan Rapat</a>/<a href="#"
                        style="color: #565656;">Tambah Undangan Rapat</a>
                </div>
            </div>
        </div>

        <!-- form add undangan -->
        <form id="addUndanganForm" method="POST" action="{{ route('undangan-superadmin.store') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title" style="font-size: 18px;"><b>Formulir Tambah Undangan</b></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="tgl_surat" class="form-label">
                                <img src="/img/undangan/date.png" alt="date" style="margin-right: 5px;">Tanggal Surat
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date" name="tgl_dibuat" id="tgl_dibuat" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" readonly>

                            @error('tgl_dibuat')
                                <div class="form-control text-danger">{{ $message }}</div>
                            @enderror
                            <input type="hidden" name="tgl_disahkan">
                            <input type="hidden" name="catatan">


                        </div>
                        <div class="col-md-6">
                            <label for="seri_surat" class="form-label">Seri Surat</label>
                            <input type="text" name="seri_surat" id="seri_surat" class="form-control"
                                value="{{ $nomorSeriTahunan ?? '' }}" readonly>
                            <input type="hidden" name="kode" value="{{ $kode }}">
                            <input type="hidden" name="pembuat" value="{{ auth()->user()->id }}">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="nomor_undangan" class="form-label">Nomor Surat</label>
                            <input type="text" name="nomor_undangan" id="nomor_undangan" class="form-control"
                                value="{{ $nomorDokumen }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="judul" class="form-label">Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="judul" id="judul" class="form-control"
                                placeholder="Masukkan Perihal / Judul Surat" value="{{ old('judul') }}">
                            @error('judul')
                                <div class="form-control text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    {{-- <div class="row mb-4"> --}}
                        <!--Checkboxes kepada (tujuan)-->
                        <div class="row mb-4">
                            <div class="col-md-12 d-flex justify-content-center">
                                <div style="width: 95%">
                                    <label for="kepada" class="form-label">
                                        <img src="/img/undangan/kepada.png" alt="kepada" class="form-label"
                                            style="margin-right: 5px; color: #1f4178;">Kepada <span
                                            class="text-danger">*</span>
                                        <span class="label-kepada">Pilih user atau struktur, semua user di bawah
                                            struktur akan otomatis terpilih</span>
                                    </label>
                                    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto; font">
                                        <div style="font-size: small" class="form-label" id="org-tree"></div>
                                        <style>
                                            #org-tree .jstree-anchor {
                                                color: #1f4178;
                                                font-weight: 500;
                                            }
                                        </style>
                                        <small id="tujuanError" class="text-danger" style="display:none;">Minimal pilih
                                            satu tujuan!</small>
                                    </div>
                                </div>
                                <script>
                                    $(function () {
                                        $('#org-tree').jstree({
                                            'core': {
                                                'data': @json(json_decode($jsTreeData))
                                            },
                                            "plugins": ["checkbox", "search"]
                                        }).on('changed.jstree', function (e, data) {
                                            let allSelectedNodes = data.instance.get_selected(true);
                                            let selectedNodes = [];

                                            allSelectedNodes.forEach(function (node) {
                                                // Check if node has 'fa fa-user' icon (which indicates it's a user)
                                                if (node.icon && node.icon === 'fa fa-user') {
                                                    selectedNodes.push(node.text);
                                                }

                                                // Auto expand selected nodes to show their children
                                                if (data.instance.is_selected(node.id)) {
                                                    data.instance.open_node(node.id);
                                                }
                                            });

                                            // Sort selectedNodes by position hierarchy (Direktur first, Staff last)
                                            selectedNodes.sort(function (a, b) {
                                                // Define position hierarchy order
                                                const positionOrder = {
                                                    'Direktur': 1,
                                                    'GM': 2, 'General Manager': 2,
                                                    'SM': 3, 'Senior Manager': 3,
                                                    'M': 4, 'Manager': 4,
                                                    'PJ SM': 5, 'Penanggung Jawab Senior Manager': 5,
                                                    'PJ M': 6, 'Penanggung Jawab Manager': 6,
                                                    'SPV': 7, 'Supervisor': 7,
                                                    'PJ SPV': 8, 'Penanggung Jawab Supervisor': 8,
                                                    'Staff': 9
                                                };

                                                // Extract position from text (position is at the beginning)
                                                const getPositionPriority = function (text) {
                                                    for (let pos in positionOrder) {
                                                        if (text.startsWith(pos)) {
                                                            return positionOrder[pos];
                                                        }
                                                    }
                                                    return 999; // Unknown positions go last
                                                };

                                                return getPositionPriority(a) - getPositionPriority(b);
                                            });

                                            let list = $('#selected-recipients');
                                            let section = $('#selected-section');
                                            list.empty();

                                            if (selectedNodes.length) {
                                                selectedNodes.forEach(name => {
                                                    list.append(`<li>${name}</li>`);
                                                });
                                                section.show();
                                            } else {
                                                section.hide();
                                            }
                                        });
                                        // Listen for changes and update tujuanInput as array of user IDs

                                    });
                                </script>
                            </div>
                            <!-- Added section for displaying selected recipients -->
                            <div style="display: none;" id="selected-section">
                                <label style="font-size: small;" class="form-label">
                                    Daftar Penerima:
                                </label>
                                <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                    <ul id="selected-recipients"
                                        style="font-size: small; padding-left: 15px; margin: 0; counter-reset: item; list-style-type: none;">
                                    </ul>
                                    <style>
                                        #selected-recipients li {
                                            display: block;
                                            margin-bottom: 0.2em;
                                        }

                                        #selected-recipients li:before {
                                            content: counter(item, decimal) ". ";
                                            counter-increment: item;
                                            font-weight: bold;
                                        }
                                    </style>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-6">
                                <label for="tgl_rapat" class="form-label">
                                    <img src="/img/undangan/date.png" alt="date" style="margin-right: 5px;">Tanggal
                                    Rapat<span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tgl_rapat" id="tgl_rapat" class="form-control"
                                    value="{{ old('tgl_rapat') }}" placeholder="Tanggal Rapat">
                                @error('tgl_rapat')
                                    <div class="form-control text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="waktu" class="form-label">Waktu Rapat</label> <span
                                    class="text-danger">*</span>
                                <div class="d-flex align-items-center">
                                    <input type="text" name="waktu_mulai" id="waktu_mulai" class="form-control me-2"
                                        placeholder="09.00" value="{{ old('waktu_mulai') }}">
                                    <span class="fw-bold">s/d</span>
                                    <input type="text" name="waktu_selesai" id="waktu_selesai" class="form-control ms-2"
                                        placeholder="Selesai" value="{{ old('waktu_selesai') }}">
                                </div>
                                @error('waktu_mulai')
                                    <div class="form-control text-danger">{{ $message }}</div>
                                @enderror
                                @error('waktu_selesai')
                                    <div class="form-control text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tempat">Tempat Rapat</label> <span class="text-danger">*</span>
                                <input type="text" name="tempat" id="tempat" class="form-control"
                                    placeholder="Ruang Rapat" value="{{ old('tempat') }}">
                                    @error('tempat')
                                    <div class="form-control text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <!--TTD yang bertanda tangan-->
                            <div class="col-md-6">
                                <label for="nama_bertandatangan" class="form-label">Nama yang Bertanda Tangan <span
                                        class="text-danger">*</span></label>
                                <select name="manager_user_id" required id="managerDropdown" class="form-control"
                                    disabled>
                                    <option value="">-- Pilih Penandatangan --</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ $manager->id == Auth::id() ? 'selected' : '' }}>
                                            @php
                                                if ($manager->position->id_position !== 1) {
                                                    preg_match('/\((.*?)\)/', $manager->position->nm_position, $matches);
                                                    $kode_position = $matches[1] ?? $manager->position->nm_position;
                                                } else {
                                                    $kode_position = $manager->position->nm_position;
                                                }
                                            @endphp

                                            ({{ $kode_position }}) {{ $manager->firstname }} {{ $manager->lastname }}

                                        </option>
                                    @endforeach
                                </select>

                                <input type="hidden" name="manager_user_id" id="managerUserId" class="form-control"
                                    value="{{ Auth::user()->id }}">

                                <input type="hidden" name="nama_bertandatangan" id="nama_bertandatangan"
                                    class="form-control" value="{{ $manager->id }}">
                            </div>
                            <div class="col-md-6 lampiran">
                                <label for="lampiran" class="form-label">Lampiran</label>
                                <div class="separator"></div>
                                <div class="upload-wrapper">
                                    <button type="button" class="btn btn-primary upload-button" id="openUploadModal"
                                        style="margin-left: 30px;">Pilih File</button>
                                    <input type="file" id="lampiran" name="lampiran" accept=".pdf,.jpg,.jpeg,.png"
                                        style="display: none;">
                                    <div id="filePreview" style="display: none; text-align: center">
                                        <img id="previewIcon" src="" alt="Preview"
                                            style="max-width: 18px; max-height: 18px; object-fit: contain; display: inline-block; margin-right: 10px;">
                                        <span id="fileName"></span>
                                        <button type="button" id="removeFile" class="bi bi-x remove-btn"
                                            style="border: none; color:red; background-color: white;"></button>
                                    </div>
                                </div>
                                <small id="fileError" class="text-danger" style="display:none;">File gagal diunggah. Ukuran maksimal 2MB dan harus bertipe PDF.</small>
                            </div>
                        </div>

                    </div>
                    <div class="row mb-4 isi-surat-row">
                        <div class="col-md-12">
                            <img src="\img\undangan\isi-surat.png" alt="isiSurat" style=" margin-left: 10px;">
                            <label for="isi_undangan">Agenda <span class="text-danger">*</span></label>
                        </div>
                        @error('isi_undangan')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                            <textarea id="summernote" name="isi_undangan" value="{{ old('isi_undangan') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-cancel"><a
                            href="{{route('undangan.superadmin')}}">Batal</a></button>
                    <button type="submit" class="btn btn-save">Kirim</button>
                    <div id="tujuan-container"></div> <!--Manggil script dibawah-->
                </div>
            </div>
        </form>
    </div>
    <!-- Modal Berhasil -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="modal-body">
                    <img src="/img/memo-admin/success.png" alt="Success Icon" class="my-3" style="width: 80px;">
                    <!-- Success Message -->
                    <h5 class="modal-title"><b>Sukses</b></h5>
                    <p class="mt-2">Menunggu Approval dari Manager</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><a
                            href="{{route('undangan.admin')}}" style="color: white; text-decoration: none">Kembali ke
                            Halaman Undangan</a></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload File -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">
                        <img src="/img/memo-superadmin/cloud-add.png" alt="Icon"
                            style="width: 24px; margin-right: 10px;">
                        Unggah file
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-subtitle">Pilih dan unggah file pilihan Anda</p>
                    <div class="upload-container">
                        <div class="upload-box" id="uploadBox">
                            <img src="/img/memo-superadmin/cloud-add.png" alt="Cloud Icon"
                                style="width: 40px; margin-bottom: 10px;">
                            <p class="upload-text">Pilih file atau seret & letakkan di sini</p>
                            <p class="upload-note">Ukuran file PDF tidak lebih dari 2MB</p>
                            <button class="btn btn-outline-primary" id="selectFileBtn">Pilih File</button>
                            <input type="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                            <div id="fileInfo" style="display: none; text-align: center ">
                                <div id="fileInfoWrapper"
                                    style="display: flex; align-items: center; justify-content: center">
                                    <img id="modalPreviewIcon" src="" alt="Preview"
                                        style="max-width: 18px; max-height: 18px; object-fit: contain; margin-right: 5px; display: none;">
                                    <span id="modalFileName"></span>
                                </div>
                                @error('lampiran')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
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
        $('#addUndanganForm').on('submit', function (e) {
            const tujuanContainer = $('#tujuan-container');
            tujuanContainer.html(''); // bersihkan sebelum nambah

            const selected = $('#org-tree').jstree('get_selected', true);
            const file = fileInput.files[0];
            const userIds = selected
                .filter(node => node.id.startsWith('user-'))
                .map(node => node.id.replace('user-', ''));
            const fileError = document.getElementById('fileError');

            userIds.forEach(userId => {
                tujuanContainer.append(`<input type="hidden" name="tujuan[]" value="${userId}">`);
            });

            // Validasi minimal pilih satu tujuan
            if (userIds.length === 0) {
                tujuanError.textContent = "Minimal pilih satu tujuan!";
                tujuanError.style.display = 'block';

                // Scroll otomatis ke error
                tujuanError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                e.preventDefault();
                return false;
            } else {
                tujuanError.style.display = 'none';
            }
            if (file && file.size > 2 * 1024 * 1024) {
                fileError.textContent = "File gagal diunggah. Ukuran maksimal 2MB dan harus bertipe PDF.";
                fileError.style.display = 'block';

                // Scroll otomatis ke error
                fileError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                e.preventDefault();
                return false;
            } else {
                fileError.style.display = 'none';
            }
        });
        // Modal Upload File - Menampilkan Modal
        document.getElementById('openUploadModal').addEventListener('click', function () {
            var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
            uploadModal.show();
        });

        // Membuka file input ketika tombol "Pilih File" di klik
        document.getElementById('selectFileBtn').addEventListener('click', function () {
            document.getElementById('fileInput').click();
        });

        // Menangani dragover event untuk upload box
        document.getElementById('uploadBox').addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.border = '2px dashed #007bff';
        });

        // Menangani dragleave event untuk upload box
        document.getElementById('uploadBox').addEventListener('dragleave', function () {
            this.style.border = '2px dashed #ccc';
        });

        // Menangani drop event untuk upload box
        document.getElementById('uploadBox').addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.border = '2px dashed #ccc';
            document.getElementById('fileInput').files = e.dataTransfer.files;
            updateFilePreview();
        });

        // Menangani pemilihan file melalui file input
        document.getElementById('fileInput').addEventListener('change', function () {
            const file = this.files[0];
            const uploadBtn = document.getElementById('uploadBtn');
            const fileInfo = document.getElementById('fileInfo');
            const modalFileName = document.getElementById('modalFileName');
            const modalPreviewIcon = document.getElementById('modalPreviewIcon');
            const uploadText = document.querySelector('.upload-text');
            const uploadNote = document.querySelector('.upload-note');
            const selectFileBtn = document.getElementById('selectFileBtn');

            if (file) {
                modalFileName.textContent = file.name;
                fileInfo.style.display = 'block';
                uploadBtn.disabled = false;
                uploadText.style.display = 'none';
                uploadNote.style.display = 'none';
                selectFileBtn.style.display = 'none';

                if (file.type.startsWith('image/')) {
                    modalPreviewIcon.src = '/img/image.png'; // Ikon gambar
                } else if (file.type === 'application/pdf') {
                    modalPreviewIcon.src = '/img/pdf.png'; // Ikon PDF
                }
                modalPreviewIcon.style.display = 'block';
            }
        });

        // Meng-upload file setelah tombol "Unggah" di klik di modal
        document.getElementById('uploadBtn').addEventListener('click', function () {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            const lampiran = document.getElementById('lampiran');
            const fileNameDisplay = document.getElementById('fileName');
            const filePreview = document.getElementById('filePreview');
            const previewIcon = document.getElementById('previewIcon');
            const uploadButton = document.getElementById('openUploadModal');

            // Menampilkan file info di input lampiran setelah file dipilih
            document.getElementById('fileInfoWrapper').style.display = 'flex';
            document.getElementById('fileInfoWrapper').style.alignItems = 'center';

            if (file) {
                lampiran.files = fileInput.files;
                fileNameDisplay.textContent = file.name;
                filePreview.style.display = 'block';
                uploadButton.style.display = 'none';

                if (file.type.startsWith('image/')) {
                    previewIcon.src = '/img/image.png'; // Ikon gambar
                } else if (file.type === 'application/pdf') {
                    previewIcon.src = '/img/pdf.png'; // Ikon PDF
                }
                previewIcon.style.display = 'inline-block'; // Menampilkan ikon preview
            }

            // Menyembunyikan modal setelah file diupload
            var uploadModal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
            uploadModal.hide();
        });

        // Menghapus file yang dipilih dan menyembunyikan preview
        document.getElementById('removeFile').addEventListener('click', function () {
            document.getElementById('lampiran').value = ''; // Menghapus file yang dipilih
            document.getElementById('filePreview').style.display = 'none'; // Menyembunyikan preview
            document.getElementById('openUploadModal').style.display = 'block'; // Menampilkan tombol upload lagi
        });

        // Menangani pemilihan file di input lampiran
        document.getElementById('lampiran').addEventListener('change', function () {
            const file = this.files[0];
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const previewIcon = document.getElementById('previewIcon');
            const removeFileBtn = document.getElementById('removeFile');

            if (file) {
                fileName.textContent = file.name;
                filePreview.style.display = 'block'; // Menampilkan preview

                // Menampilkan ikon preview
                if (file.type.startsWith('image/')) {
                    previewIcon.src = '/img/image.png'; // Ikon gambar
                } else if (file.type === 'application/pdf') {
                    previewIcon.src = '/img/pdf.png'; // Ikon PDF
                }

                previewIcon.style.display = 'inline-block'; // Menampilkan ikon
            }
        });

        document.getElementById('removeFile').addEventListener('click', function () {
            // Reset input field dan preview pada kolom input
            document.getElementById('lampiran').value = '';
            document.getElementById('filePreview').style.display = 'none';
            document.getElementById('openUploadModal').style.display = 'block';

            // Reset pada modal overlay
            const uploadBtn = document.getElementById('uploadBtn');
            const fileInfo = document.getElementById('fileInfo');
            const modalFileName = document.getElementById('modalFileName');
            const modalPreviewIcon = document.getElementById('modalPreviewIcon');
            const uploadText = document.querySelector('.upload-text');
            const uploadNote = document.querySelector('.upload-note');
            const selectFileBtn = document.getElementById('selectFileBtn');

            // Reset file yang tampil di overlay
            fileInfo.style.display = 'none';
            modalFileName.textContent = '';
            modalPreviewIcon.style.display = 'none';
            uploadBtn.disabled = true;
            uploadText.style.display = 'block';
            uploadNote.style.display = 'block';
            selectFileBtn.style.display = 'block';

            document.getElementById('selectFileBtn').style.display = 'flex';
            document.getElementById('selectFileBtn').style.justifyContent = 'center';
            document.getElementById('selectFileBtn').style.alignItems = 'center';
        });

        // Raroh iki opo
        $(document).ready(function () {
            $('#dropdownMenuButton').on('change', function () {
                // Saat opsi dipilih, teks akan ke kiri
                $(this).css('text-align', 'left');

                // Jika kembali ke opsi default (Pilih), teks akan kembali ke center
                if ($(this).val() === null || $(this).val() === "") {
                    $(this).css('text-align', 'center');
                }
            });
        });

        function toggleFields(show) {
            const fields = document.getElementById('additionalFields');
            if (show) {
                fields.style.display = 'block'; // Show additional fields
            } else {
                fields.style.display = 'none'; // Hide additional fields
            }
        }

    </script>
    <script>
        $(function () {
            // Summernote inisialisasi
            $('#summernote').summernote({
                height: 200,
                toolbar: [
                    ['font', ['bold', 'italic', 'underline', 'clear', 'fontname', 'color']],
                ],
                fontNames: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman'],
                fontNamesIgnoreCheck: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman']
            });
        });
    </script>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
