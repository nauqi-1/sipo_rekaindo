<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Memo Superadmin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/edit-memo.css') }}">

    <!--DEPENDENCY UNTUK JSTREE-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route('memo.superadmin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Memo</h1>
        </div>
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{route('superadmin.dashboard')}}">Beranda</a>/<a
                        href="{{route('memo.superadmin')}}">Memo</a>/<a href="#" style="color: #565656;">Edit Memo</a>
                </div>
            </div>
        </div>

        <!-- form add memo -->
        <form id="editMemoForm" method="POST" action="{{ route('memo/update', $memo->id_memo) }}">
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
                                <img src="/img/memo-superadmin/date.png" alt="date" style="margin-right: 5px;">Tanggal
                                Surat <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tgl_dibuat" id="tgl_dibuat" class="form-control"
                                value="{{ $memo->tgl_dibuat->format('Y-m-d') }}" required>
                            <input type="hidden" name="tgl_disahkan">
                            <input type="hidden" name="divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">
                        </div>
                        <div class="col-md-6">
                            <label for="seri_surat" class="form-label">Seri Surat</label>
                            <input type="text" name="seri_surat" id="seri_surat" class="form-control"
                                value="{{ $memo->seri_surat }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="nomor_memo" class="form-label">Nomor Surat</label>
                            <input type="text" name="nomor_memo" id="nomor_memo" class="form-control"
                                value="{{ $memo->nomor_memo }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="judul" class="form-label">Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="judul" id="judul" class="form-control" value="{{ $memo->judul }}">
                            @error('judul')
                                <div class="form-control text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row mb-4">
                        <div class="d-flex justify-content-center">
                            <div style="width: 95%;">
                                <label style="font-size: small;" for="kepada" class="form-label">
                                    <img src="/img/undangan/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada
                                    <span class="text-danger">*</span>
                                    <span class="text-danger" style="font-size: x-small;">Cukup pilih Divisi /
                                        Departemen / Bagian / Unit / Karyawan yang dituju.</span>
                                </label>

                                <div id="orgTreeError" class="form-control text-danger" style="display:none;"></div>

                                <div class="col-md-12">
                                    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                        <div style=" font-size: small;" id="org-tree"></div>
                                    </div>
                                    <script>
                                        const tujuanNameArray = @json($tujuanArray);
                                        $(function () {
                                            $('#org-tree').jstree({
                                                'core': {
                                                    'data': @json(json_decode($jsTreeData))
                                                },
                                                "plugins": ["checkbox", "search"],
                                                "checkbox": {
                                                    "keep_selected_style": false,
                                                    "three_state": false,
                                                    "cascade": 'none',
                                                },
                                            }).on('ready.jstree', function (e, data) {
                                                // hide checkboxes for top-level nodes
                                                $('#org-tree li').each(function () {
                                                    var node = data.instance.get_node(this.id);
                                                    if (node && node.parent === "#") {
                                                        // hide checkbox using CSS
                                                        $(this).find('.jstree-checkbox').css('display', 'none');
                                                    }
                                                });
                                            }).on('changed.jstree', function (e, data) {
                                                document.getElementById('errorTujuan').style.display = 'none';
                                                let sortOrder = ['div', 'dept', 'section', 'unit', 'user'];
                                                let selectedNodes = data.instance.get_selected(true)
                                                    .sort((a, b) => {
                                                        let aType = a.id.split('-')[0]; // prefix before "-"
                                                        let bType = b.id.split('-')[0];
                                                        return sortOrder.indexOf(aType) - sortOrder.indexOf(bType);
                                                    });

                                                let list = $('#selected-recipients');
                                                let section = $('#selected-section');
                                                list.empty();

                                                if (selectedNodes.length) {
                                                    selectedNodes.forEach(node => {
                                                        list.append(`<li>${node.text}</li>`);
                                                    });
                                                    section.show();
                                                } else {
                                                    section.hide();
                                                }
                                            });

                                            $('#org-tree').on('ready.jstree', function () {
                                                const treeInstance = $('#org-tree').jstree(true); // ✅ get jsTree instance
                                                const allNodes = treeInstance.get_json('#', {
                                                    flat: true
                                                });

                                                tujuanNameArray.forEach(name => {
                                                    const foundNode = allNodes.find(node => node.text === name);
                                                    if (foundNode) {
                                                        treeInstance.check_node(foundNode.id);
                                                    }

                                                });
                                            });

                                        });
                                    </script>
                                    <div style="display: none;" id="selected-section">
                                        <label style="font-size: small;" class="form-label">
                                            Daftar Penerima:
                                        </label>
                                        <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                            <ul id="selected-recipients"
                                                style="font-size: small; padding-left: 15px; margin: 0;"></ul>
                                        </div>
                                    </div>
                                    <div style="display: none;" id="errorTujuan" class="form-control text-danger">
                                        <div style="font-size: small;">
                                            Minimal pilih satu tujuan!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="nama_bertandatangan" class="form-label">Nama yang Bertanda Tangan <span
                                    class="text-danger"></span></label>
                            <input type="hidden" name="nama_bertandatangan" id="nama_bertandatangan"
                                class="form-control" value="{{ $memo->nama_bertandatangan }}" required>
                            <select name="nama_bertandatangan" id="nama_bertandatangan" class="form-control" disabled>
                                @foreach($managers as $manager)
                                    <option value="{{  $manager->firstname . ' ' . $manager->lastname  }}" {{ $memo->nama_bertandatangan == ($manager->firstname . ' ' . $manager->lastname) ? 'selected' : '' }}>
                                        {{ $manager->firstname . ' ' . $manager->lastname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6"></div>
                    </div>

                    <div class="row mb-4 isi-surat-row">
                        <div class="col-md-12">
                            <img src="\img\memo-superadmin\isi-surat.png" alt="isiSurat" style=" margin-left: 10px;">
                            <label for="isi-memo">Isi Surat <span class="text-danger">*</span></label>
                        </div>
                        <div class="row editor-container col-12 mb-4" style="font-size: 12px;">
                            <textarea id="summernote" name="isi_memo">{{ $memo->isi_memo }}</textarea>
                            @error('isi_memo')
                                <div class=" text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row mb-4 need-row">
                    <div class="col-md-12">
                        <label for="need" class="need">Keperluan Barang</label>
                        <label for="isi" class="fill">*Isi keperluan barang jika dibutuhkan</label>

                    </div>
                    @foreach ($memo->kategoriBarang as $index => $barang)
                        <div class="row mb-4 isi">
                            <input type="hidden" name="kategori_barang[{{ $index }}][id_kategori_barang]"
                                value="{{ $barang->id_kategori_barang }}">
                            <div class="col-md-6">
                                <label for="kategori_barang_{{ $index }}_nomor">Nomor</label>
                                <input type="text" id="kategori_barang_{{ $index }}_nomor"
                                    name="kategori_barang[{{ $index }}][nomor]" class="form-control"
                                    value="{{ $barang->nomor }}">
                            </div>
                            <div class="col-md-6">
                                <label for="kategori_barang_{{ $index }}_nama_barang">Barang</label>
                                <input type="text" id="kategori_barang_{{ $index }}_nama_barang"
                                    name="kategori_barang[{{ $index }}][barang]" class="form-control"
                                    value="{{ $barang->barang }}"
                                    oninvalid="this.setCustomValidity('Kolom ini wajib diisi.');"
                                    oninput="this.setCustomValidity('');">
                            </div>
                            <div class="col-md-6">
                                <label for="kategori_barang_{{ $index }}_qty">Qty</label>
                                <input type="number" id="kategori_barang_{{ $index }}_qty"
                                    name="kategori_barang[{{ $index }}][qty]" class="form-control"
                                    value="{{ $barang->qty }}" oninvalid="this.setCustomValidity('Kolom ini wajib diisi.');"
                                    oninput="this.setCustomValidity('');">
                            </div>
                            <div class="col-md-6">
                                <label for="kategori_barang_{{ $index }}_satuan">Satuan</label>
                                <input type="text" id="kategori_barang_{{ $index }}_satuan"
                                    name="kategori_barang[{{ $index }}][satuan]" class="form-control"
                                    value="{{ $barang->satuan }}"
                                    oninvalid="this.setCustomValidity('Kolom ini wajib diisi.');"
                                    oninput="this.setCustomValidity('');">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    <a class="btn btn-cancel" href="{{route('memo.superadmin')}}">Batal</a>
                    <button type="submit" class="btn btn-save">Simpan</button>
                </div>
                <div id="tujuan-container"></div>
            </div>
        </form>
    </div>

    <script>

        $('#editMemoForm').on('submit', function (e) {
            // Clear existing tujuan[] inputs
            $('#tujuan-container').empty();
            // Get selected nodes
            const selectedNodes = $('#org-tree').jstree('get_selected', true);
            if (selectedNodes.length === 0) {
                document.getElementById('errorTujuan').style.display = 'block';
                document.getElementById('errorTujuan').scrollIntoView({ behavior: 'smooth', block: 'center' });
                e.preventDefault();
                return false;
            } else {
                document.getElementById('errorTujuan').style.display = 'none';
            }
            let sortOrder = ['div', 'dept', 'section', 'unit', 'user'];
            selectedNodes.sort((a, b) => {
                let aType = a.id.split('-')[0];
                let bType = b.id.split('-')[0];
                return sortOrder.indexOf(aType) - sortOrder.indexOf(bType);
            });
            selectedNodes.forEach(node => {
                $('#tujuan-container').append(
                    `<input type="hidden" name="tujuan[]" value="${node.id}">` +
                    `<input type="hidden" name="tujuanString[]" value="${node.text}">`
                );
            });
        });
        $(document).ready(function () {
            $('#summernote').summernote({
                height: 300,
                toolbar: [
                    //['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    //['insert', ['link', 'picture', 'video']],
                    //['view', ['fullscreen', 'codeview', 'help']],
                ],
                //fontNames: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman'], 
                //fontNamesIgnoreCheck: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman']
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>