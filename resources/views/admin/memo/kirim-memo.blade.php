<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Memo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/kirim-memo.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('admin.memo.memo-admin')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Add Memo</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb">
                    <a href="#">Home</a>/<a href="#">Memo</a>/<a href="#">Add Memo</a>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Informasi Detail Surat</div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>No Surat</label>
                        <input type="text" value="123" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Lampiran</label>
                        <input type="text" value="-" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Perihal</label>
                        <input type="text" value="Memo Rapat Kajian" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" value="8 Januari 2025" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Kepada</label>
                        <input type="text" value="Manager Divisi Keuangan" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Detail</div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Pembuat</label>
                        <input type="text" value="Staff HR & GA" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Status</label>
                        <span class="status">Approve</span>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Dibuat Tanggal</label>
                        <input type="text" value="8 Januari 2025" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>File</label>
                        <div class="btn-group">
                            <button class="btn btn-secondary">Preview</button>
                            <button class="btn btn-primary">Download</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Tindakan Selanjutnya</div>
            <div class="form-group">
                <label>Pengesahan</label>
                <div>
                    <label><input type="radio" name="approve" value="Approve"> Approve</label>
                    <label><input type="radio" name="approve" value="Reject"> Reject</label>
                </div>
            </div>
            <div class="form-group">
                <label>Tindakan Selanjutnya</label>
                <select>
                    <option>--Pilih Tindakan--</option>
                </select>
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea placeholder="Berikan Catatan"></textarea>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-secondary">Cancel</button>
            <button class="btn btn-primary">Save</button>
        </div>
    </div>
</body>
</html>