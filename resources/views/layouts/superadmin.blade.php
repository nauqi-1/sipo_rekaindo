<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <head>
    <title>@yield('title')</title>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
      name="description"
      content="Berry is trending dashboard template made using Bootstrap 5 design framework. Berry is available in Bootstrap, React, CodeIgniter, Angular,  and .net Technologies."
    />
    <meta
      name="keywords"
      content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard"
    />
    <meta name="author" content="codedthemes" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- [Favicon] icon -->
    <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon" />
    <!-- [Google Font] Family -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" id="main-font-link" />
    <!-- [phosphor Icons] https://phosphoricons.com/ -->

    <link rel="stylesheet" href="../assets/fonts/phosphor/duotone/style.css" />
    <!-- Bootstrap Icons-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Editor Teks Summernote -->
    <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css" />
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../assets/fonts/feather.css" />
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css" />
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/material.css" />
    <!-- [Template CSS Files] -->
<link rel="stylesheet" href="../assets/css/style.css" id="main-style-link" />
<link rel="stylesheet" href="../assets/css/style-preset.css" />
<link rel="stylesheet" href="../assets/css/style-app.css" />
<link rel="stylesheet" href="../css/superadmin/dashboard.css" />
<link rel="stylesheet" href="../assets/css/user-manage.css" />
<link rel="stylesheet" href="../assets/css/data-perusahaan.css" />
<link rel="stylesheet" href="../css/superadmin.edit-profile.css" />
<link rel="stylesheet" href="../css/superadmin/arsip.css"/>
<link rel="stylesheet" href="../css/superadmin/edit-memo.css"/>
<link rel="stylesheet" href="../css/surat.css"/>
<link rel="stylesheet" href="../css/superadmin/laporan.css"/>
<link rel="stylesheet" href="../css/superadmin/cetak-laporan.css"/>
<link rel="stylesheet" href="../css/info.css"/>
<link rel="stylesheet" href="../css/add.css"/>
<link rel="stylesheet" href="{{ asset('css/add.css') }}" />

  </head>
  <body>
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
      <div class="loader-track">
        <div class="loader-fill"></div>
      </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- Include Sidebar -->
    @include('includes.superadmin.sidebar')

    <!-- Include Navbar -->
    @include('includes.superadmin.navbar')
 
    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        <!-- [ Main Content ] start -->
              @yield('content')
        <!-- [ Main Content ] end -->
      </div>
    </div>
    <!-- [ Main Content ] end -->

    <footer class="pc-footer">
      <div class="footer-wrapper container-fluid">
        <div class="row">
          <div class="col-sm-6 my-1">
          </div>
        </div>
      </div>
    </footer>

 <!-- Required Js -->
<script src="../assets/js/plugins/popper.min.js"></script>
<script src="../assets/js/plugins/simplebar.min.js"></script>
<script src="../assets/js/plugins/bootstrap.min.js"></script>
<script src="../assets/js/icon/custom-font.js"></script>
<script src="../assets/js/script.js"></script>
<script src="../assets/js/theme.js"></script>
<script src="../assets/js/plugins/feather.min.js"></script>
<script>
  layout_change('light');
</script>
   
<script>
  font_change('Roboto');
</script>
 
<script>
  change_box_container('false');
</script>
 
<script>
  layout_caption_change('true');
</script>
   
<script>
  layout_rtl_change('false');
</script>
 
<script>
  preset_change('preset-1');
</script>

    <!-- [Page Specific JS] start -->
    <!-- Apex Chart -->
    <script src="../assets/js/plugins/apexcharts.min.js"></script>
    <script src="../assets/js/pages/dashboard-default.js"></script>
    <!-- [Page Specific JS] end -->
    <script src="../assets/js/data.js"></script>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

    // $(document).ready(function() {
    //     $('#summernote').summernote({
    //         height: 300,
    //         toolbar: [
    //         ['style', ['style']],
    //         ['font', ['bold', 'italic', 'underline', 'clear', 'fontname', 'fontsize', 'color']],
    //         ['para', ['ul', 'ol', 'paragraph']],
    //         ['insert', ['link', 'picture', 'video']],
    //         ['view', ['fullscreen', 'codeview', 'help']],
    //         ],
    //         fontNames: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman'], 
    //         fontNamesIgnoreCheck: ['Arial', 'Courier Prime', 'Georgia', 'Tahoma', 'Times New Roman']
    //     });
    // });

    document.getElementById('confirmDelete').addEventListener('click', function () {
            // Ambil referensi modal
            const deleteModalEl = document.getElementById('deleteModal');
            const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
            
            // Tutup modal Hapus terlebih dahulu
            deleteModal.hide();
            
            // Pastikan modal benar-benar tertutup sebelum membuka modal berikutnya
            deleteModalEl.addEventListener('hidden.bs.modal', function () {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            }, { once: true }); // Tambahkan event listener hanya sekali
        });

        document.getElementById('confirmArsip').addEventListener('click', function () {
            // Ambil referensi modal
            const deleteModalEl = document.getElementById('arsipModal');
            const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);
            
            // Tutup modal Hapus terlebih dahulu
            deleteModal.hide();
            
            // Pastikan modal benar-benar tertutup sebelum membuka modal berikutnya
            deleteModalEl.addEventListener('hidden.bs.modal', function () {
                const successModal = new bootstrap.Modal(document.getElementById('successArsipModal'));
                successModal.show();
            }, { once: true }); // Tambahkan event listener hanya sekali
        });
    </script>
    <script>
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
    </script>
    <script>
        document.getElementById("tgl_dibuat").addEventListener("change", function() {
            let inputTanggal = this.value; // Ambil nilai dari input date
            if (!inputTanggal) return;

            let tanggalObj = new Date(inputTanggal);

            // let hari = tanggalObj.toLocaleDateString('id-ID', { weekday: 'long' });
            let tanggal = String(tanggalObj.getDate()).padStart(2, '0'); // Tambahkan 0 di depan jika <10
            let bulan = tanggalObj.toLocaleDateString('id-ID', { month: 'long' });
            let tahun = tanggalObj.getFullYear();

            // let formattedTanggal = ${hari}, ${tanggal} ${bulan} ${tahun};
            let formattedTanggal = `${tanggal} ${bulan} ${tahun}`;

            // Mengubah input date menjadi text dan menampilkan format tanggal yang dipilih
            this.type = "text";
            this.value = formattedTanggal;
        });
    </script>
    <!-- <script>
      document.getElementById('tambahIsiRisalahBtn').addEventListener('click', function() {
        var newRow = document.createElement('div');
        newRow.classList.add('isi-surat-row', 'row');  
        newRow.style.gap = '0';  

        newRow.innerHTML = `
            <div class="col-md-1">
                <input type="text" class="form-control" name="no[]">
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
        `;
        document.getElementById('risalahContainer').appendChild(newRow);
      });
    </script> -->
    <script>
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
            const tandaIdentitas = document.getElementById('tanda_identitas');
            const fileNameDisplay = document.getElementById('fileName');
            const filePreview = document.getElementById('filePreview');
            const previewIcon = document.getElementById('previewIcon');
            const uploadButton = document.getElementById('openUploadModal');

            // Menampilkan file info di input lampiran setelah file dipilih
            document.getElementById('fileInfoWrapper').style.display = 'flex';
            document.getElementById('fileInfoWrapper').style.alignItems = 'center';
            
            if (file) {
                tandaIdentitas.files = fileInput.files;
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
            document.getElementById('tanda_identitas').value = ''; // Menghapus file yang dipilih
            document.getElementById('filePreview').style.display = 'none'; // Menyembunyikan preview
            document.getElementById('openUploadModal').style.display = 'block'; // Menampilkan tombol upload lagi
        });

        // Menangani pemilihan file di input lampiran
        document.getElementById('tanda_identitas').addEventListener('change', function () {
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
            document.getElementById('tanda_identitas').value = '';
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
    
        function toggleFields(show) {
            const fields = document.getElementById('additionalFields');
            if (show) {
                fields.style.display = 'block'; // Show additional fields
            } else {
                fields.style.display = 'none'; // Hide additional fields
            }
        }

        function toggleKategoriBarang() {
            var yaRadio = document.getElementById("ya");
            var jumlahKategoriDiv = document.getElementById("jumlahKategoriDiv");
            var jumlahKategoriInput = document.getElementById("jumlahKategori");
            var barangTable = document.getElementById("barangTable");
            
            if (yaRadio.checked) {
                jumlahKategoriDiv.style.display = "block";
            } else {
                jumlahKategoriDiv.style.display = "none";
                jumlahKategoriInput.value = "";
                barangTable.innerHTML = "";
            }
        }
        
        function generateBarangFields() {
            const jumlahKategori = document.getElementById("jumlahKategori").value;
            const barangTable = document.getElementById("barangTable");
            barangTable.innerHTML = ""; // Hapus isi sebelumnya
            
            if (jumlahKategori > 0) {
                for (let i = 0; i < jumlahKategori; i++) {
                    // Buat row baru untuk setiap kolom
                    const row = document.createElement('div');
                    row.classList.add('row', 'mb-3');
                    row.style.display = 'flex';
                    row.style.gap = '10px';
                    row.style.margin = '10px 47px';

                    // Template untuk input field
                    row.innerHTML = `
                        <div class="col-md-6">
                            <label for="nomor_${i}">Nomor</label>
                            <input type="text" id="nomor_${i}" name="nomor[]" class="form-control" value="${i + 1}" readonly>
                            <input type="hidden" name="memo_divisi_id_divisi" value="{{ auth()->user()->divisi_id_divisi }}">
                        </div>
                        <div class="col-md-6">
                            <label for="barang_${i}">Barang</label>
                            <input type="text" id="barang_${i}" name="barang[]" class="form-control" placeholder="Masukkan barang">
                        </div>
                        <div class="col-md-6">
                            <label for="qty_${i}">Qty</label>
                            <input type="number" id="qty_${i}" name="qty[]" class="form-control" placeholder="Masukkan jumlah">
                        </div>
                        <div class="col-md-6">
                            <label for="satuan_${i}">Satuan</label>
                            <input type="text" id="satuan_${i}" name="satuan[]" class="form-control" placeholder="Masukkan satuan">
                        </div>
                    `;

                    // Tambahkan row ke dalam barangTable
                    barangTable.appendChild(row);
                }
            }
        }
    </script>
  </body>
  <!-- [Body] end -->
</html>
