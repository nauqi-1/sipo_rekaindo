<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Memo</title>
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/supervisor/view-memoDiterima.css') }}">
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Back Button -->
            <div class="back-button">
                <a href="{{route ('memo.diterima')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
            </div>
            <h1>Memo Diterima</h1>
        </div>        
        <div class="row">
            <div class="breadcrumb-wrapper">
                <div class="breadcrumb" style="gap: 5px;">
                    <a href="#">Beranda</a>/<a href="{{route ('memo.diterima')}}">Memo Diterima</a>/<a href="#" style="color: #565656;">Detail Memo</a>
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
                        <input type="text" id="seri" value="{{ $memo->memo->seri_surat }}">
                    </div>
                    <div class="card-white">
                        <label for="diterima">Diterima</label>
                        <div class="separator"></div>

                        <input type="text" id="diterima" value="{{ $memo->memo->tujuan }}">
                    </div>
                </div>
                <div class="col">
                <div class="card-blue">
                        <label for="tgl_surat" class="form-label">Status Surat</label>
                    </div>
                    <div class="card-white">
                        <label for="status">Status</label>
                        <div class="separator"></div>
                        <button class="status">
                        @if ($memo->memo->status == 'reject')
                            <!-- <span class="badge bg-danger">Ditolak</span> -->
                            <span>Ditolak</span>
                        @elseif ($memo->memo->status == 'pending')
                            <!-- <span class="badge bg-warning">Diproses</span> -->
                            <span>Diproses</span>
                        @else
                            <!-- <span class="badge bg-success">Diterima</span> -->
                            <span>Diterima</span>
                        @endif
                        </button>
                    </div>
                    <div class="card-white">
                        <label for="tanggal">Tanggal</label>
                        <div class="separator"></div>
                        <input type="text" id="tanggal" value="{{ $memo->tgl_disahkan ? : '-' }}">
                    </div>
                </div>
            </div>
            <div class="row mb-4" style="gap: 20px;">
                <div class="col">
                    <div class="card-blue">
                        <label for="tgl_surat" class="form-label">
                            <img src="/img/risalah/info.png" alt="info surat">Informasi Detail Surat
                        </label>
                    </div>
                    <div class="card-white">
                        <label for="nomor">No Dokumen</label>
                        <div class="separator"></div>
                        <input type="text" id="nomor" value="{{ $memo->memo->nomor_memo }}">
                    </div>
                    <div class="card-white">
                        <label for="divisi">Divisi</label>
                        <div class="separator"></div>
                        <input type="text" id="seri" value="{{ $memo->memo->divisi->nm_divisi }}">
                    </div>
                    <div class="card-white">
                        <label for="perihal">Perihal</label>
                        <div class="separator"></div>
                        <input type="text" id="perihal" value="{{ $memo->memo->judul }}">
                    </div>
                    <div class="card-white">
                        <label for="tgl">Tanggal Surat</label>
                        <div class="separator"></div>
                        <input type="text" id="tgl"value="{{  \Carbon\Carbon::parse($memo->memo->tgl_dibuat)->format('d-m-Y')  }}">
                    </div>
                    <div class="card-white">
                        <label for="file">File</label>
                        <div class="separator"></div>
                        <button class="btn-file"  onclick="window.location.href='{{ route('view-memoPDF', $memo->memo->id_memo) }}'"><img src="/img/mata.png" alt="view">Lihat</button>
                    </div>
                </div>
            </div>

            <form id="approvalForm" method="POST" action="{{ route('memo.updateStatus', $memo->memo->id_memo) }}">
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
                            <input type="radio" class="form-check-input approval-checkbox" id="correction" name="status" value="pending">
                        </div>
                    </div>

                    <div class="card-blue1">Tindakan Selanjutnya</div>
                    <div class="card-white">
                        <select class="btn btn-dropdown dropdown-toggle d-flex justify-content-between align-items-center w-100" id="nextAction" name="next_action">
                            <option disabled selected style="text-align: left;">--Pilih Tindakan--</option>
                            <option value="koreksi">Koreksi kembali</option>
                            <option value="dilanjutkan">Dilanjutkan</option>
                        </select>                    
                    </div>
                </div>

                <div class="col">
                    <div class="card-blue1">Catatan</div>
                    <textarea type="text" id="catatan" name="catatan" placeholder="Berikan Catatan"></textarea>        
                </div>             
            </div>

            <div class="footer">
                <button type="button" class="btn back" id="backBtn" onclick="window.location.href='{{ route('memo.diterima') }}'">Kembali</button>
                <button type="button" class="btn submit" id="submitBtn" data-bs-toggle="modal" data-bs-target="#submit">Kirim</button>
            </div>
        </form>

        <!-- Modal kirim -->
        <div class="modal fade" id="submit" tabindex="-1" aria-labelledby="submitLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <!-- Close Button -->
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                        <img src="/img/memo-superadmin/konfirmasi.png" alt="Question Mark Icon" class="mb-3" style="width: 80px;">
                        <h5 class="modal-title mb-4"><b>Kirim Memo Diterima?</b></h5>
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
                        <img src="/img/memo-admin/success.png" alt="Success Icon" class="my-3" style="width: 80px;">
                        <!-- Success Message -->
                        <h5 class="modal-title"><b>Sukses</b></h5>
                        <p class="mt-2">Berhasil Mengirimkan Memo</p>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><a href="{{route ('memo.diterima')}}" style="color: white; text-decoration: none">Kembali ke Halaman Memo</a></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
    // Overlay kirim
    document.addEventListener('DOMContentLoaded', function () {
        // Mengatur agar hanya satu checkbox approval yang bisa dipilih
        const checkboxes = document.querySelectorAll('.approval-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            });
        });

        const approvalForm = document.getElementById('approvalForm');
        const confirmSubmitButton = document.getElementById('confirmSubmit');

        confirmSubmitButton.addEventListener('click', function (event) {
            event.preventDefault(); // Mencegah submit default
            
            // Kirim form secara normal
            approvalForm.submit();
        });

        // Jika ada notifikasi sukses dari server, tampilkan modal sukses
        const successMessage = "{{ session('success') }}";
        if (successMessage) {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        }
    });
    </script>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>