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
                <a href="{{route ('memo.superadmin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Edit Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="{{route('superadmin.dashboard')}}">Beranda</a>/<a href="{{route ('memo.superadmin')}}">Memo</a>/<a href="#" style="color: #565656;">Edit Memo</a>
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
                   <div class="d-flex justify-content-center">
                    <div style="width: 95%;">
                    <label style="font-size: small;" for="kepada" class="form-label">
                        <img src="/img/undangan/kepada.png" alt="kepada" style="margin-right: 5px;">Kepada <span class="text-danger">*</span>
                         <span class="text-danger" style="font-size: x-small;">Cukup pilih tujuan tanpa memilih tingkat dibawahnya.</span>
                    </label>
                    
                    <div id="orgTreeError" class="form-control text-danger" style="display:none;"></div>
              
                    <div class="col-md-12">
                                    <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                        <div style=" font-size: small;" id="org-tree"></div>
                                    </div>
                        <script>   

                            const tujuanNameArray = @json($tujuanArray);
                            $(function() {
                                $('#org-tree').jstree({
                                    'core' : {
                                        'data' : @json(json_decode($jsTreeData))
                                    },
                                    "plugins" : [ "checkbox", "search" ],
                                    "checkbox" : {
                                        "keep_selected_style" : false,
                                        "three_state" : false,
                                        "cascade" : 'none',
                                    },
                                });
                                
                                 $('#org-tree').on('ready.jstree', function () {
                                    const treeInstance = $('#org-tree').jstree(true); // ✅ get jsTree instance
                                    const allNodes = treeInstance.get_json('#', { flat: true }); 

                                    tujuanNameArray.forEach(name => {
                                        const foundNode = allNodes.find(node => node.text === name);
                                        if (foundNode) {
                                            treeInstance.check_node(foundNode.id);
                                        }

                                    });
                                });
                                
                            });
                        </script>
                           
                    </div>
                    </div>
                </div>

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
                    <div class="col-md-6"></div>
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
</div>
            <div class="card-footer">
                <button type="button" class="btn btn-cancel"><a href="{{route ('memo.admin')}}">Batal</a></button>
                <button type="submit" class="btn btn-save">Simpan</button>
            </div>
            <div id="tujuan-container"></div>
        </div>
        </form>
    </div>
    <script>
        $('#editMemoForm').on('submit', function (e) {

            // Get selected nodes
            const selectedNodes = $('#org-tree').jstree('get_selected', true);
            const tujuan = selectedNodes;
          console.log(tujuan);
            // Append hidden inputs
            tujuan.forEach(node => {
            const nodeId = node.id
            const nodeText = node.text;
            $('#tujuan-container').append(
                `<input type="hidden" name="tujuan[]" value="${node.id}">` +
                `<input type="hidden" name="tujuanString[]" value="${node.text}">`
            );
        });
             if (userIds.length === 0) {
                alert("Minimal pilih satu tujuan!");
                e.preventDefault();
                return false;
            }
            console.log("Submitting form with tujuan:", tujuan); // <--- debug
        });
        $(document).ready(function() {
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
