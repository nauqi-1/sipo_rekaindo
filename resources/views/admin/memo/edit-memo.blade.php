<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Memo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/edit-memo.css') }}">
</head>
<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('memo.admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{ route('admin.dashboard') }}">Home</a>/<a href="{{ route('memo.admin') }}">Memo</a>/<a href="#" style="color: #565656;">Edit Memo</a>
                </div>
            </div>
        </div>

        <!-- form add memo -->
        <form method="POST" action="{{ route('memo/update', $memo->id_memo) }}">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="font-size: 18px;"><b>Form Edit Memo</b></h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="tgl_dibuat" class="form-label">
                            <img src="/img/memo-superadmin/date.png" alt="date" style="margin-right: 5px;">Tanggal Surat <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tgl_dibuat" id="tgl_dibuat" class="form-control" value="{{ $memo->tgl_dibuat->format('Y-m-d') }}" required>
                        <input type="hidden" name="tgl_disahkan" >
                        <input type="hidden" name="divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">
                    </div>
                    <div class="col-md-6">
                        <label for="seri_surat" class="form-label">Seri Surat</label>
                        <input type="text" name="seri_surat" id="seri_surat" class="form-control" value="{{ $memo->seri_surat }}" readonly>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="nomor_memo" class="form-label">Nomor Surat</label>
                        <input type="text" name="nomor_memo" id="nomor_memo" class="form-control" value="{{ $memo->nomor_memo }}" readonly>
                    </div>
                    <div class="col-md-6" >
                        <label for="judul" class="form-label">Perihal <span class="text-danger">*</span></label>
                        <input type="text" name="judul" id="judul" class="form-control" value="{{ $memo->judul }}" required>
                    </div>

                </div>
                <div class="row mb-4">
                   <div class="col-md-6">
    <label for="kepada" class="form-label">
        <img src="/img/undangan/kepada.png" alt="kepada" style="margin-right: 5px;">
        Kepada <span class="text-danger">*</span>
        <label for="tujuan" class="label-kepada">Centang lebih dari satu jika diperlukan</label>
    </label>

    @php
        $selectedTujuan = is_array(old('tujuan')) ? old('tujuan') : explode(';', $memo->tujuan ?? '');
    @endphp

    <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
        @foreach($divisi as $d)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" 
                    name="tujuan[]" 
                    value="{{ $d->id_divisi }}" 
                    id="divisi_{{ $d->id_divisi }}"
                    {{ in_array($d->id_divisi, $selectedTujuan) ? 'checked' : '' }}>
                <label class="form-check-label" for="divisi_{{ $d->id_divisi }}">
                    {{ $d->nm_divisi }}
                </label>
            </div>
        @endforeach
    </div>

    @error('tujuan')
        <div class="form-control text-danger">{{ $message }}</div>
    @enderror
</div>

                    <!-- <div class="col-md-6 lampiran">
                        <label for="upload_file" class="form-label">Lampiran <span class="text-danger">*</span></label>
                        <div class="upload-wrapper">
                            <button type="button" class="btn btn-primary upload-button" data-bs-toggle="modal" data-bs-target="#uploadModal">Pilih File</button>
                            <input type="file" id="upload_file" name="upload_file" class="form-control-file" value="{{ $memo->tanda_identitas }}" hidden>
                        </div>
                    </div> -->
                    <div class="col-md-6">
                        <label for="nama_bertandatangan" class="form-label">Nama yang Bertanda Tangan <span class="text-danger"></span></label>
                        <input type="hidden" name="nama_bertandatangan" id="nama_bertandatangan" class="form-control" value="{{ $memo->nama_bertandatangan }}" required>
                        <select name="nama_bertandatangan" id="nama_bertandatangan" class="form-control" disabled>
                        @foreach($managers as $manager)
                            <option value="{{  $manager->firstname . ' ' . $manager->lastname  }}" 
                                {{ $memo->nama_bertandatangan == ($manager->firstname . ' ' . $manager->lastname) ? 'selected' : '' }}>
                                {{ $manager->firstname . ' ' . $manager->lastname }}
                            </option>
                        @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-4 isi-surat-row">
                    <div class="col-md-12">
                        <img src="\img\memo-superadmin\isi-surat.png" alt="isiSurat"style=" margin-left: 10px;">
                        <label for="isi-memo">Isi Surat <span class="text-danger">*</span></label>
                    </div>
                    <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                            <textarea id="summernote" name="isi_memo" >{{ $memo->isi_memo }}</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb-4 need-row">
                <div class="col-md-12">
                    <label for="need" class="need">Keperluan Barang</label>
                    <label for="isi" class="fill">*Isi keperluan barang jika dibutuhkan</label>
                </div>
            </div>
            @foreach ($memo->kategoriBarang as $index => $barang)
            <div class="row mb-4 isi">
                <input type="hidden" name="kategori_barang[{{ $index }}][id_kategori_barang]" value="{{ $barang->id_kategori_barang }}">
                <div class="col-md-6">
                    <label for="kategori_barang_{{ $index }}_nomor">Nomor</label>
                    <input type="text" id="kategori_barang_{{ $index }}_nomor" name="kategori_barang[{{ $index }}][nomor]" class="form-control" value="{{ $barang->nomor }}">
                </div>
                <div class="col-md-6">
                    <label for="kategori_barang_{{ $index }}_nama_barang">Barang</label>
                    <input type="text" id="kategori_barang_{{ $index }}_nama_barang" name="kategori_barang[{{ $index }}][barang]" class="form-control" value="{{ $barang->barang }}">
                </div>
                <div class="col-md-6">
                    <label for="kategori_barang_{{ $index }}_qty">Qty</label>
                    <input type="number" id="kategori_barang_{{ $index }}_qty" name="kategori_barang[{{ $index }}][qty]" class="form-control" value="{{ $barang->qty }}">
                </div>
                <div class="col-md-6">
                    <label for="kategori_barang_{{ $index }}_satuan">Satuan</label>
                    <input type="text" id="kategori_barang_{{ $index }}_satuan" name="kategori_barang[{{ $index }}][satuan]" class="form-control" value="{{ $barang->satuan }}">
                </div>
            </div>
        @endforeach

            <div class="card-footer">
                <button type="button" class="btn btn-cancel"><a href="{{route ('memo.admin')}}">Batal</a></button>
                <button type="submit" class="btn btn-save">Simpan</button>
            </div>
        </div>
        </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Upload File -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">
                        <img src="/img/memo-admin/cloud-add.png" alt="Icon" style="width: 24px; margin-right: 10px;">
                        Unggah file
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="modal-subtitle">Pilih dan unggah file pilihan Anda</p>
                    <div class="upload-container">
                        <div class="upload-box" id="uploadBox">
                            <img src="/img/memo-admin/cloud-add.png" alt="Cloud Icon" style="width: 40px; margin-bottom: 10px;">
                            <p class="upload-text">Pilih file atau seret & letakkan di sini</p>
                            <p class="upload-note">Ukuran file PDF tidak lebih dari 20MB</p>
                            <button class="btn btn-outline-primary" id="selectFileBtn">Pilih File</button>
                            <input type="file" id="fileInput" accept=".pdf" style="display: none;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="uploadBtn" >Unggah</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#dropdownMenuButton').on('change', function() {
                // Saat opsi dipilih, teks akan ke kiri
                $(this).css('text-align', 'left');

                // Jika kembali ke opsi default (Pilih), teks akan kembali ke center
                if($(this).val() === null || $(this).val() === "") {
                    $(this).css('text-align', 'center');
                }
            });
        });

        // Hubungkan tombol "Select File" dengan input file
        document.getElementById('selectFileBtn').addEventListener('click', function () {
            document.getElementById('fileInput').click();
        });

        // Deteksi perubahan file dan aktifkan tombol Upload
        document.getElementById('fileInput').addEventListener('change', function () {
            const uploadBtn = document.getElementById('uploadBtn');
            if (this.files.length > 0) {
                uploadBtn.disabled = false;
            } else {
                uploadBtn.disabled = true;
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

        function toggleFields(show) {
            const fields = document.getElementById('additionalFields');
            if (show) {
                fields.style.display = 'block'; // Show additional fields
            } else {
                fields.style.display = 'none'; // Hide additional fields
            }
        }

        // Fungsi untuk menampilkan dan menyembunyikan fields tambahan
        function toggleFields(show) {
            const fields = document.getElementById('additionalFields');
            if (show) {
                fields.style.display = 'block'; // Menampilkan fields tambahan
            } else {
                fields.style.display = 'none'; // Menyembunyikan fields tambahan
            }
        }

        // Fungsi untuk memperbarui dan menampilkan fields berdasarkan jumlah kolom yang dimasukkan
        function updateFields() {
            const jumlahKolom = document.getElementById('jumlah_kolom').value;
            const dynamicFieldsContainer = document.getElementById('dynamicFields');
            dynamicFieldsContainer.innerHTML = ''; // Kosongkan container sebelum menambahkan field baru

            for (let i = 0; i < jumlahKolom; i++) {
                // Buat row baru untuk setiap kolom
                const row = document.createElement('div');
                row.classList.add('row', 'mb-3');
                row.style.display = 'flex';
                row.style.gap = '2px';
                row.style.marginLeft = '47px';
                row.style.marginRight = '47px';
                row.style.marginTop = '10px';
                
                // Buat kolom untuk Nomor
                const colNomor = document.createElement('div');
                colNomor.classList.add('col-md-6');
                const labelNomor = document.createElement('label');
                labelNomor.setAttribute('for', 'nomor_' + i);
                labelNomor.textContent = 'Nomor';
                const inputNomor = document.createElement('input');
                inputNomor.setAttribute('type', 'text');
                inputNomor.setAttribute('id', 'nomor_' + i);
                inputNomor.setAttribute('name', 'nomor_' + i);
                inputNomor.classList.add('form-control');
                inputNomor.setAttribute('placeholder', 'Masukkan nomor');
                colNomor.appendChild(labelNomor);
                colNomor.appendChild(inputNomor);

                // Buat kolom untuk Barang
                const colBarang = document.createElement('div');
                colBarang.classList.add('col-md-6');
                const labelBarang = document.createElement('label');
                labelBarang.setAttribute('for', 'barang_' + i);
                labelBarang.textContent = 'Barang';
                const inputBarang = document.createElement('input');
                inputBarang.setAttribute('type', 'text');
                inputBarang.setAttribute('id', 'barang_' + i);
                inputBarang.setAttribute('name', 'barang_' + i);
                inputBarang.classList.add('form-control');
                inputBarang.setAttribute('placeholder', 'Masukkan barang');
                colBarang.appendChild(labelBarang);
                colBarang.appendChild(inputBarang);

                // Tambahkan kolom Nomor dan Barang ke dalam row
                row.appendChild(colNomor);
                row.appendChild(colBarang);

                // Buat kolom untuk Qty
                const colQty = document.createElement('div');
                colQty.classList.add('col-md-6');
                const labelQty = document.createElement('label');
                labelQty.setAttribute('for', 'qty_' + i);
                labelQty.textContent = 'Qty';
                const inputQty = document.createElement('input');
                inputQty.setAttribute('type', 'number');
                inputQty.setAttribute('id', 'qty_' + i);
                inputQty.setAttribute('name', 'qty_' + i);
                inputQty.classList.add('form-control');
                inputQty.setAttribute('placeholder', 'Masukkan jumlah');
                colQty.appendChild(labelQty);
                colQty.appendChild(inputQty);

                // Buat kolom untuk Satuan
                const colSatuan = document.createElement('div');
                colSatuan.classList.add('col-md-6');
                const labelSatuan = document.createElement('label');
                labelSatuan.setAttribute('for', 'satuan_' + i);
                labelSatuan.textContent = 'Satuan';
                const inputSatuan = document.createElement('input');
                inputSatuan.setAttribute('type', 'text');
                inputSatuan.setAttribute('id', 'satuan_' + i);
                inputSatuan.setAttribute('name', 'satuan_' + i);
                inputSatuan.classList.add('form-control');
                inputSatuan.setAttribute('placeholder', 'Masukkan satuan');
                colSatuan.appendChild(labelSatuan);
                colSatuan.appendChild(inputSatuan);

                // Tambahkan kolom Qty dan Satuan ke dalam row
                row.appendChild(colQty);
                row.appendChild(colSatuan);

                // Tambahkan row ke dalam container dynamicFields
                dynamicFieldsContainer.appendChild(row);
            }
        }

    </script>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>