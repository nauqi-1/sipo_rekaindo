/**
=========================================================================
=========================================================================
Template Name: Berry - Admin Template
Author: CodedThemes
Support: https://codedthemes.com/
File: script.js
Description:  this file will contains behavior, properties, 
              functionality and interactions of a small module of ui element 
              which used to build a theme layout.
=========================================================================
=========================================================================
*/

'use strict';
var flg = '0';
document.addEventListener('DOMContentLoaded', function () {
  // feather icon start
  feather.replace();
  // feather icon end

  // remove pre-loader start
  setTimeout(function () {
    var loaderBg = document.querySelector('.loader-bg');
    if (loaderBg) {
      loaderBg.remove();
    }
  }, 400);

  // remove pre-loader end
  if (document.body.hasAttribute('data-pc-layout') && document.body.getAttribute('data-pc-layout') === 'horizontal') {
    if (window.innerWidth <= 1024) {
      add_scroller();
    }
  } else {
    add_scroller();
  }

  var hamburger = document.querySelector('.hamburger');
  if (hamburger && !hamburger.classList.contains('is-active')) {
    hamburger.addEventListener('click', function () {
      // Toggle the 'is-active' class
      hamburger.classList.toggle('is-active');
    });
  }

  // Menu overlay layout start
  var temp_overlay_menu = document.querySelector('#overlay-menu');
  if (temp_overlay_menu) {
    temp_overlay_menu.addEventListener('click', function () {
      var pcSidebar = document.querySelector('.pc-sidebar');
      menu_click(); // Assuming this initializes any menu interactions needed

      if (pcSidebar.classList.contains('pc-over-menu-active')) {
        remove_overlay_menu();
      } else {
        pcSidebar.classList.add('pc-over-menu-active');

        // Check if overlay already exists before adding
        if (!document.querySelector('.pc-menu-overlay')) {
          pcSidebar.insertAdjacentHTML('beforeend', '<div class="pc-menu-overlay"></div>');

          // Add event listener to the overlay for removing menu and overlay on click
          document.querySelector('.pc-menu-overlay').addEventListener('click', function () {
            remove_overlay_menu();
            document.querySelector('.hamburger').classList.remove('is-active'); // Ensuring hamburger is deactivated
          });
        }
      }
    });
  }
  // Menu overlay layout end

  // Menu collapse click start
  var mobile_collapse_over = document.querySelector('#mobile-collapse');
  if (mobile_collapse_over) {
    mobile_collapse_over.addEventListener('click', function () {
      var temp_sidebar = document.querySelector('.pc-sidebar');
      if (temp_sidebar) {
        if (temp_sidebar.classList.contains('mob-sidebar-active')) {
          rm_menu(); // Close menu if already active
        } else {
          temp_sidebar.classList.add('mob-sidebar-active');

          // Only add the overlay if it doesn't already exist
          if (!document.querySelector('.pc-menu-overlay')) {
            temp_sidebar.insertAdjacentHTML('beforeend', '<div class="pc-menu-overlay"></div>');

            // Add event listener to remove the menu when overlay is clicked
            document.querySelector('.pc-menu-overlay').addEventListener('click', function () {
              rm_menu();
            });
          }
        }
      }
    });
  }
  // Menu collapse click end

  // Menu collapse click start
  var topbar_link_list = document.querySelectorAll('.pc-horizontal .topbar .pc-navbar > li > a');
  if (topbar_link_list.length) {
    topbar_link_list.forEach((link) => {
      link.addEventListener('click', function (e) {
        var targetElement = e.target;
        setTimeout(function () {
          var secondChild = targetElement.parentNode.children[1];
          if (secondChild) {
            secondChild.removeAttribute('style');
          }
        }, 1000);
      });
    });
  }
  // Menu collapse click end
  // Horizontal menu click js start
  var topbar_link_list = document.querySelectorAll('.pc-horizontal .topbar .pc-navbar > li > a');
  if (topbar_link_list) {
    topbar_link_list.forEach((link) => {
      link.addEventListener('click', function (e) {
        var targetElement = e.target;
        setTimeout(function () {
          targetElement.parentNode.children[1].removeAttribute('style');
        }, 1000);
      });
    });
  }
  // Horizontal menu click js end

  // header dropdown scrollbar start
  function initializeSimpleBar(selector) {
    const element = document.querySelector(selector);
    if (element) {
      new SimpleBar(element);
    }
  }
  // Initialize SimpleBar for Profile notification scroll
  initializeSimpleBar('.profile-notification-scroll');
  // Initialize SimpleBar for header notification scroll
  initializeSimpleBar('.header-notification-scroll');
  // header dropdown scrollbar end

  // component scrollbar start
  const cardBody = document.querySelector('.component-list-card .card-body');
  if (cardBody) {
    new SimpleBar(cardBody);
  }
  // component- dropdown scrollbar end

  // sidebar toggle event
  const sidebarHideBtn = document.querySelector('#sidebar-hide');
  const sidebar = document.querySelector('.pc-sidebar');

  if (sidebarHideBtn && sidebar) {
    sidebarHideBtn.addEventListener('click', () => {
      sidebar.classList.toggle('pc-sidebar-hide');
    });
  }

  // search dropdown trigger event
  const searchDrp = document.querySelector('.trig-drp-search');
  if (searchDrp) {
    searchDrp.addEventListener('shown.bs.dropdown', () => {
      const searchInput = document.querySelector('.drp-search input');
      if (searchInput) {
        searchInput.focus();
      }
    });
  }
});
// Menu click start
function add_scroller() {
  // Initialize menu click behavior
  menu_click();

  // Cache the navbar content element
  var navbarContent = document.querySelector('.navbar-content');

  // Check if the navbar content exists and SimpleBar is not already initialized
  if (navbarContent && !navbarContent.SimpleBar) {
    new SimpleBar(navbarContent);
  }
}

// Menu click start
function menu_click() {
  var vw = window.innerWidth;
  var menuItems = document.querySelectorAll('.pc-navbar li');

  // Remove previous click events
  menuItems.forEach((item) => {
    item.removeEventListener('click', function () {});
  });

  // Hide all submenus initially
  var subMenus = document.querySelectorAll('.pc-navbar li:not(.pc-trigger) .pc-submenu');
  subMenus.forEach((subMenu) => {
    subMenu.style.display = 'none';
  });

  // Event delegation for main menu items
  var navbar = document.querySelector('.pc-navbar');
  if (navbar) {
    navbar.addEventListener('click', function (event) {
      var target = event.target.closest('li.pc-hasmenu');

      if (target) {
        event.stopPropagation();
        toggleMenu(target);
      }
    });
  }

  // Helper function to toggle menus
  function toggleMenu(targetElement) {
    // Handle the current menu item
    if (targetElement.classList.contains('pc-trigger')) {
      targetElement.classList.remove('pc-trigger');
      slideUp(targetElement.children[1], 200);
      setTimeout(() => {
        targetElement.children[1].removeAttribute('style');
        targetElement.children[1].style.display = 'none';
      }, 200);
    } else {
      closeAllMenus(); // Close other open menus
      targetElement.classList.add('pc-trigger');
      slideDown(targetElement.children[1], 200);
    }
  }

  // Close all open menus
  function closeAllMenus() {
    var openMenus = document.querySelectorAll('li.pc-trigger');
    openMenus.forEach((menu) => {
      menu.classList.remove('pc-trigger');
      slideUp(menu.children[1], 200);
      setTimeout(() => {
        menu.children[1].removeAttribute('style');
        menu.children[1].style.display = 'none';
      }, 200);
    });
  }

  // Submenu click handling with event delegation
  var subMenuItems = document.querySelectorAll('.pc-navbar > li:not(.pc-caption) li');
  subMenuItems.forEach((subMenuItem) => {
    subMenuItem.addEventListener('click', function (event) {
      var target = event.target.closest('li');
      if (target) {
        event.stopPropagation();
        toggleSubMenu(target);
      }
    });
  });

  // Helper function to toggle submenus
  function toggleSubMenu(targetElement) {
    if (targetElement.classList.contains('pc-hasmenu')) {
      if (targetElement.classList.contains('pc-trigger')) {
        targetElement.classList.remove('pc-trigger');
        slideUp(targetElement.children[1], 200);
      } else {
        closeSiblingMenus(targetElement.parentNode.children);
        targetElement.classList.add('pc-trigger');
        slideDown(targetElement.children[1], 200);
      }
    }
  }

  // Close sibling menus
  function closeSiblingMenus(siblings) {
    Array.from(siblings).forEach((sibling) => {
      if (sibling.classList.contains('pc-trigger')) {
        sibling.classList.remove('pc-trigger');
        slideUp(sibling.children[1], 200);
      }
    });
  }
}

// hide menu in mobile menu
function rm_menu() {
  // Cache the necessary elements
  var sidebar = document.querySelector('.pc-sidebar');
  var topbar = document.querySelector('.topbar');
  var sidebarOverlay = document.querySelector('.pc-sidebar .pc-menu-overlay');
  var topbarOverlay = document.querySelector('.topbar .pc-menu-overlay');

  // Remove active class from sidebar if it exists
  if (sidebar) {
    sidebar.classList.remove('mob-sidebar-active');
  }

  // Remove active class from topbar if it exists
  if (topbar) {
    topbar.classList.remove('mob-sidebar-active');
  }

  // Remove sidebar overlay if it exists
  if (sidebarOverlay) {
    sidebarOverlay.remove();
  }

  // Remove topbar overlay if it exists
  if (topbarOverlay) {
    topbarOverlay.remove();
  }
}

// remove overlay
function remove_overlay_menu() {
  var sidebar = document.querySelector('.pc-sidebar');
  var topbar = document.querySelector('.topbar');
  var sidebarOverlay = document.querySelector('.pc-sidebar .pc-menu-overlay');
  var topbarOverlay = document.querySelector('.topbar .pc-menu-overlay');

  // Remove active class from sidebar
  if (sidebar) {
    sidebar.classList.remove('pc-over-menu-active');
  }

  // Remove active class from topbar if exists
  if (topbar) {
    topbar.classList.remove('mob-sidebar-active');
  }

  // Remove the overlay elements if they exist
  if (sidebarOverlay) {
    sidebarOverlay.remove();
  }

  if (topbarOverlay) {
    topbarOverlay.remove();
  }
}

window.addEventListener('load', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });
  var toastElList = [].slice.call(document.querySelectorAll('.toast'));
  var toastList = toastElList.map(function (toastEl) {
    return new bootstrap.Toast(toastEl);
  });
});

// active menu item list start
var elem = document.querySelectorAll('.pc-sidebar .pc-navbar a');
for (var l = 0; l < elem.length; l++) {
  var pageUrl = window.location.href.split(/[?#]/)[0];
  if (elem[l].href == pageUrl && elem[l].getAttribute('href') != '') {
    elem[l].parentNode.classList.add('active');

    elem[l].parentNode.parentNode.parentNode.classList.add('pc-trigger');
    elem[l].parentNode.parentNode.parentNode.classList.add('active');
    elem[l].parentNode.parentNode.style.display = 'block';

    elem[l].parentNode.parentNode.parentNode.parentNode.parentNode.classList.add('pc-trigger');
    elem[l].parentNode.parentNode.parentNode.parentNode.style.display = 'block';
  }
}

// like event
var likeInputs = document.querySelectorAll('.prod-likes .form-check-input');
likeInputs.forEach(function (likeInput) {
  likeInput.addEventListener('change', function (event) {
    var parentElement = event.target.parentNode;

    if (event.target.checked) {
      // Append like animation HTML
      parentElement.insertAdjacentHTML(
        'beforeend',
        `<div class="pc-like">
          <div class="like-wrapper">
            <span>
              <span class="pc-group">
                <span class="pc-dots"></span>
                <span class="pc-dots"></span>
                <span class="pc-dots"></span>
                <span class="pc-dots"></span>
              </span>
            </span>
          </div>
        </div>`
      );

      // Add animation class
      parentElement.querySelector('.pc-like').classList.add('pc-like-animate');

      // Remove the like animation after 3 seconds
      setTimeout(function () {
        var likeElement = parentElement.querySelector('.pc-like');
        if (likeElement) {
          likeElement.remove();
        }
      }, 3000);
    } else {
      // Remove the like animation if it exists
      var likeElement = parentElement.querySelector('.pc-like');
      if (likeElement) {
        likeElement.remove();
      }
    }
  });
});

// authentication logo
// Select all elements with class 'img-brand' under '.auth-main.v2'
var imgBrands = document.querySelectorAll('.auth-main.v2 .img-brand');
// Check if any elements exist before proceeding
if (imgBrands.length) {
  // Iterate through the NodeList and update the 'src' attribute
  imgBrands.forEach(function (img) {
    img.setAttribute('src', '../assets/images/logo-white.svg');
  });
}
// =======================================================
// =======================================================

function removeClassByPrefix(node, prefix) {
  for (let i = 0; i < node.classList.length; i++) {
    let value = node.classList[i];
    if (value.startsWith(prefix)) {
      node.classList.remove(value);
    }
  }
}

let slideUp = (target, duration = 0) => {
  target.style.transitionProperty = 'height, margin, padding';
  target.style.transitionDuration = duration + 'ms';
  target.style.boxSizing = 'border-box';
  target.style.height = target.offsetHeight + 'px';
  target.offsetHeight;
  target.style.overflow = 'hidden';
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
};

let slideDown = (target, duration = 0) => {
  target.style.removeProperty('display');
  let display = window.getComputedStyle(target).display;

  if (display === 'none') display = 'block';

  target.style.display = display;
  let height = target.offsetHeight;
  target.style.overflow = 'hidden';
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  target.offsetHeight;
  target.style.boxSizing = 'border-box';
  target.style.transitionProperty = 'height, margin, padding';
  target.style.transitionDuration = duration + 'ms';
  target.style.height = height + 'px';
  target.style.removeProperty('padding-top');
  target.style.removeProperty('padding-bottom');
  target.style.removeProperty('margin-top');
  target.style.removeProperty('margin-bottom');
  window.setTimeout(() => {
    target.style.removeProperty('height');
    target.style.removeProperty('overflow');
    target.style.removeProperty('transition-duration');
    target.style.removeProperty('transition-property');
  }, duration);
};

var slideToggle = (target, duration = 0) => {
  if (window.getComputedStyle(target).display === 'none') {
    return slideDown(target, duration);
  } else {
    return slideUp(target, duration);
  }
};


// Event Listener Overlay delete
document.addEventListener("DOMContentLoaded", function () {
  let deleteModal = document.getElementById("deleteModal");
  let deleteForm = document.getElementById("deleteUserForm");
  let deleteSuccessModal = new bootstrap.Modal(document.getElementById("deleteSuccessModal"));

  deleteModal.addEventListener("show.bs.modal", function (event) {
      let button = event.relatedTarget;
      let userId = button.getAttribute("data-user-id");
      let route = button.getAttribute("data-route");
      deleteForm.setAttribute("action", route);
  });

  deleteForm.addEventListener("submit", function (event) {
      event.preventDefault(); // Mencegah form langsung dikirim

      let formAction = deleteForm.getAttribute("action");
      let formData = new FormData(deleteForm);

      fetch(formAction, {
          method: "DELETE", // HARUS DELETE, BUKAN POST
          body: formData,
          headers: {
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          },
      }).then(response => {
          if (response.ok) {
              let modalInstance = bootstrap.Modal.getInstance(deleteModal);
              modalInstance.hide();

              setTimeout(() => {
                  deleteSuccessModal.show();
                  setTimeout(() => {
                      location.reload(); // Refresh halaman setelah 2 detik
                  }, 1500);
              }, 500);
          }
      }).catch(error => console.error("Error:", error));
  });
});


// Script Overlay Delete
    document.addEventListener("DOMContentLoaded", function () {
        // Pastikan tombol confirmDelete ada sebelum menambahkan event listener
        let confirmDelete = document.getElementById('confirmDelete');
        if (confirmDelete) {
            confirmDelete.addEventListener('click', function () {
                const deleteModalEl = document.getElementById('deleteModal');
                const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);

                deleteModal.hide();

                deleteModalEl.addEventListener('hidden.bs.modal', function () {
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                }, { once: true });
            });
        }

        // Pastikan tombol confirmArsip ada sebelum menambahkan event listener
        let confirmArsip = document.getElementById('confirmArsip');
        if (confirmArsip) {
            confirmArsip.addEventListener('click', function () {
                const deleteModalEl = document.getElementById('arsipModal');
                const deleteModal = bootstrap.Modal.getInstance(deleteModalEl);

                deleteModal.hide();

                deleteModalEl.addEventListener('hidden.bs.modal', function () {
                    const successModal = new bootstrap.Modal(document.getElementById('successArsipModal'));
                    successModal.show();
                }, { once: true });
            });
        }
    });


    // Modal Overlay Upload File - Menampilkan Modal
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

  // Raroh iki opo
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
                      <input type="text" id="nomor_${i}" name="nomor[]" class="form-control" placeholder="Masukkan nomor">
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

  document.getElementById('tgl_dibuat').addEventListener('focus', function() {
    this.type = 'date'; 
  });

  document.getElementById('tgl_dibuat').addEventListener('blur', function() {
      if (this.value) { 
          const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
          let inputTanggal = new Date(this.value);
          
          let namaHari = hari[inputTanggal.getDay()];
          let tanggal = inputTanggal.getDate().toString().padStart(2, '0');
          let bulan = (inputTanggal.getMonth() + 1).toString().padStart(2, '0');
          let tahun = inputTanggal.getFullYear();
          
          this.type = 'text'; 
          this.value = `${namaHari}, ${tanggal}-${bulan}-${tahun}`; 
      } else {
          this.type = 'text';
          this.placeholder = "mm/dd/yyyy"; 
      }
  });  

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


// =======================================================
// =======================================================
