<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Memo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/supervisor/view-memoTerkirim.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('memo.terkirim')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Memo Terkirim</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="{{route ('memo.terkirim')}}">Memo Terkirim</a>/<a href="#" style="color: #565656;">Detail Memo</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="info-surat" class="form-label">
                            <img src="/img/memo-supervisor/info.png" alt="info surat">Informasi Detail Surat
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor" value="{{ $memo->memo->nomor_memo }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{ $memo->memo->divisi->nm_divisi }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $memo->memo->judul }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl" value="{{ $memo->memo->tgl_dibuat }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="lampiran">Lampiran</label>
                        <div class="separator"></div>
                        <input type="text" id="kepada" value="{{ $memo->memo->tujuan }}" readonly>
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="btn-file" onclick="window.location.href='{{ route('view-memoPDF', $memo->memo->id_memo) }}'"><img src="/img/mata.png" alt="view">Lihat</button>
                        
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                       
                        @if ($memo->status == 'reject')
                            <span class="badge bg-danger">Ditolak</span>
                        @elseif ($memo->status == 'pending')
                            <span class="badge bg-warning">Diproses</span>
                        @elseif ($memo->status == 'correction')
                            <span class="badge bg-danger">Dikoreksi</span>
                        @else
                            <span class="badge bg-success">Diterima</span>
                        @endif
                        
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="card-blue">
                    <label for="penerima" class="form-label">
                        <img src="/img/memo-supervisor/info.png" alt="info penerima">Informasi Penerima
                    </label>
                </div>
                <div class="col">
                    <div class="card-white">
                        <label for="file">Penerima</label>
                        <div class="separator"></div>
                        <input type="text" id="penerima">                  
                    </div>                        
                    <div class="card-white">
                        <label for="file">Catatan</label>
                        <div class="separator"></div>
                        <input type="text-area" id="catatan" value="{{ $memo->memo->catatan }}" readonly>                   
                    </div>
                </div>             
            </div>
        </div>
    </div>
    
    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.approval-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    checkboxes.forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                });
            });

            // Ketika tombol konfirmasi di modal ditekan, submit form
            document.getElementById('confirmSubmit').addEventListener('click', function () {
                document.getElementById('approvalForm').submit();
            });
        });
    </script>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>