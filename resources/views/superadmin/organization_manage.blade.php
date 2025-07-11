@extends('layouts.superadmin')

@section('title', 'Manajemen Struktur Organisasi')

@section('content')
<div class="container">
    <div class="header">
        <!-- Back Button -->
        <div class="back-button">
            <a href="{{route('superadmin.dashboard')}}"><img src="/img/user-manage/Vector_back.png" alt=""></a>
        </div>
        <h1>Manajemen Struktur Organisasi</h1>
    </div>        
    <div class="row">
        <div class="breadcrumb-wrapper" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div class="breadcrumb" style="gap: 5px; width: 82%;">
                <a href="{{route('superadmin.dashboard')}}">Beranda</a> / <a href="#">Pengaturan</a> / <a href="#" style="color: #565656;">Manajemen Struktur Organisasi</a>
            </div>
            <form method="GET" action="{{ route('organization.manageOrganization') }}" class="search-filter d-flex gap-2">
            <label style="margin: 0; padding-bottom: 25px; padding-right: 12px; color: #565656;">
                Show
                <select name="per_page" onchange="this.form.submit()" style="color: #565656; padding: 2px 5px;">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                entries
            </label>
            </form>
        </div>
    </div>

    <!-- Wrapper untuk elemen di luar card -->
    <div class="user-manage">
        <div class="header-tools">
            <h2 class="title">Struktur Organisasi</h2>
            <div class="search-filter">
                <div class="d-flex gap-2">
                    <form action="{{ route('organization.manageOrganization') }}" method="GET" class="d-flex align-items-center btn btn-search" style="gap: 5px;">
                        <button type="submit" class="border-0 bg-transparent p-0" style="outline: none; box-shadow: none;">
                            <img src="/img/user-manage/search.png" alt="search" style="width: 20px; height: 20px; cursor: pointer;">
                        </button>                            
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 bg-transparent" placeholder="Cari berdasarkan nama ..." style="outline: none; box-shadow: none;">
                    </form>
                </div>

                <div class="dropdown m-3">
                    <button class="btn btn-dropdown dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2">Filter</span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('organization.manageOrganization', ['sort' => 'asc']) }}" style="justify-content: center; text-align: center;">
                                Urutkan abjad A-Z
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('organization.manageOrganization', ['sort' => 'desc']) }}" style="justify-content: center; text-align: center;">
                                Urutkan abjad Z-A
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Add User Button to Open Mod    al -->
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Tambah Struktur Organisasi</button>
            </div>
        </div>
        <!-- Card untuk tabel -->
        <div class="accordion mt-4" id="orgStructure">
            @php
            function renderOrgRecursive($node) {
                if(isset($node->name_director)) {
                    $label = "Direktur: ".htmlspecialchars($node->name_director);
                    $margin = 0; $border = 'primary'; $bg = 'primary';
                    $type = 'director'; $id = $node->id_director; $name = $node->name_director;
                } elseif(isset($node->nm_divisi)) {
                    $label = "Divisi: ".htmlspecialchars($node->nm_divisi);
                    $margin = 20; $border = 'secondary'; $bg = 'secondary';
                    $type = 'divisi'; $id = $node->id_divisi; $name = $node->nm_divisi;
                } elseif(isset($node->name_department)) {
                    $label = "Departemen: ".htmlspecialchars($node->name_department);
                    $margin = 40; $border = 'info'; $bg = 'info';
                    $type = 'department'; $id = $node->id_department; $name = $node->name_department;
                } elseif(isset($node->name_section)) {
                    $label = "Bagian: ".htmlspecialchars($node->name_section);
                    $margin = 60; $border = 'success'; $bg = 'success';
                    $type = 'section'; $id = $node->id_section; $name = $node->name_section;
                } elseif(isset($node->name_unit)) {
                    $label = "Unit: ".htmlspecialchars($node->name_unit);
                    $margin = 80; $border = 'warning'; $bg = 'warning';
                    $type = 'unit'; $id = $node->id_unit; $name = $node->name_unit;
                } else {
                    return;
                }

                $idUnique = uniqid('accordion_');
                $deleteUrl = route('organization.delete', ['type' => $type, 'id' => $id]);

                $hasChildren = 
                    (!empty($node->subDirectors)) || 
                    (!empty($node->divisi)) || 
                    (!empty($node->department)) || 
                    (!empty($node->section)) || 
                    (!empty($node->unit));

                if ($hasChildren) {
                    echo "<div class='accordion-item mb-2 border border-{$border} rounded' style='margin-left: {$margin}px'>
                        <h4 class='accordion-header' id='heading{$idUnique}'>
                            <div class='d-flex align-items-center justify-content-between bg-{$bg} text-white rounded p-2'
                                style='cursor: pointer;'
                                data-bs-toggle='collapse' data-bs-target='#collapse{$idUnique}'
                                aria-expanded='false' aria-controls='collapse{$idUnique}'>
                                
                                <span>{$label}</span>
                                
                                <span>
                                    <button class='btn btn-edit' data-bs-toggle='modal' data-bs-target='#editModal'
                                        data-type='{$type}' data-id='{$id}' data-name=\"".htmlspecialchars($name, ENT_QUOTES)."\">
                                        <img src='/img/user-manage/Edit1.png' alt='edit'>
                                    </button>
                                    <button type='button' class='btn btn-delete' onclick=\"confirmDelete('{$deleteUrl}')\">
                                        <img src='/img/user-manage/Trash1.png' alt='delete'>
                                    </button>
                                </span>
                            </div>
                        </h4>
                        <div id='collapse{$idUnique}' class='accordion-collapse collapse' aria-labelledby='heading{$idUnique}'>
                            <div class='accordion-body'>";
                    if(isset($node->subDirectors)) 
                        foreach ($node->subDirectors as $subDir) 
                            renderOrgRecursive($subDir);

                    if(isset($node->divisi)) 
                        foreach ($node->divisi as $div) 
                            renderOrgRecursive($div);

                    if(isset($node->department)) {
                        if(isset($node->name_director)) {
                            foreach ($node->department->whereNull('divisi_id_divisi') as $dept)
                                renderOrgRecursive($dept);
                        }
                        if(isset($node->nm_divisi)) {
                            foreach ($node->department as $dept)
                                renderOrgRecursive($dept);
                        }
                    }

                    if(isset($node->section)) {
                        if(isset($node->name_department)) {
                            foreach ($node->section as $sec)
                                renderOrgRecursive($sec);
                        }
                    }

                    if(isset($node->unit)) {
                        if(isset($node->name_department)) {
                            foreach ($node->unit->whereNull('section_id_section') as $unit)
                                renderOrgRecursive($unit);
                        }
                        if(isset($node->name_section)) {
                            foreach ($node->unit as $unit)
                                renderOrgRecursive($unit);
                        }
                    }

                    echo "    </div>
                            </div>
                        </div>";
                } else {
                    echo "<div class='d-flex justify-content-between align-items-center mb-2' style='margin-left: {$margin}px'>
                            <span>{$label}</span>
                            <span>
                                <button class='btn btn-sm btn-light me-1' data-bs-toggle='modal' data-bs-target='#editModal'
                                    data-type='{$type}' data-id='{$id}' data-name=\"".htmlspecialchars($name, ENT_QUOTES)."\">
                                    Edit
                                </button>
                                <button type='button' class='btn btn-sm btn-danger' onclick=\"confirmDelete('{$deleteUrl}')\">
                                    Hapus
                                </button>
                            </span>
                        </div>";
                }
            }
            @endphp

            @if($mainDirector)
                @php renderOrgRecursive($mainDirector); @endphp
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Struktur Organisasi -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('organization-manage/add') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Tambah Struktur Organisasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          
          <div class="mb-3">
            <label for="type" class="form-label">Jenis Struktur</label>
            <select class="form-select" id="type" name="type" required>
              <option value="">-- Pilih --</option>
              <option value="Director">Direktur</option>
              <option value="Divisi">Divisi</option>
              <option value="Department">Departemen</option>
              <option value="Section">Bagian</option>
              <option value="Unit">Unit</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="parent_id" class="form-label">Parent Struktur</label>
            <select class="form-select" id="parent_id" name="parent_id">
              <option value="">-- Pilih induk struktur --</option>
              @php
              function renderOrgOptions($node, $level = 0) {
                  $indent = str_repeat('&nbsp;', $level * 4);
                  if(isset($node->name_director))
                      echo "<option value='director-{$node->id_director}'>{$indent}Direktur: {$node->name_director}</option>";
                  elseif(isset($node->nm_divisi))
                      echo "<option value='divisi-{$node->id_divisi}'>{$indent}--> Divisi: {$node->nm_divisi}</option>";
                  elseif(isset($node->name_department))
                      echo "<option value='department-{$node->id_department}'>{$indent}-----> Departemen: {$node->name_department}</option>";
                  elseif(isset($node->name_section))
                      echo "<option value='section-{$node->id_section}'>{$indent}--------> Bagian: {$node->name_section}</option>";
                  elseif(isset($node->name_unit))
                      echo "<option value='unit-{$node->id_unit}'>{$indent}-----------> Unit: {$node->name_unit}</option>";

                  if(isset($node->subDirectors))
                      foreach ($node->subDirectors as $subDir)
                          renderOrgOptions($subDir, $level+1);
                  if(isset($node->divisi))
                      foreach ($node->divisi as $div)
                          renderOrgOptions($div, $level+1);
                  if(isset($node->department)) {
                      if(isset($node->name_director))
                          foreach ($node->department->whereNull('divisi_id_divisi') as $dept)
                              renderOrgOptions($dept, $level+1);
                      if(isset($node->nm_divisi))
                          foreach ($node->department as $dept)
                              renderOrgOptions($dept, $level+1);
                  }
                  if(isset($node->section))
                      foreach ($node->section as $sec)
                          renderOrgOptions($sec, $level+1);
                  if(isset($node->unit)) {
                      if(isset($node->name_department) && $node->unit->whereNull('section_id_section'))
                          foreach ($node->unit->whereNull('section_id_section') as $unit)
                              renderOrgOptions($unit, $level+1);
                      if(isset($node->name_section))
                          foreach ($node->unit as $unit)
                              renderOrgOptions($unit, $level+1);
                  }
              }
              if($mainDirector) renderOrgOptions($mainDirector);
              @endphp
            </select>
          </div>

          <div class="mb-3">
            <label for="name" class="form-label">Nama Struktur</label>
            <input type="text" class="form-control" id="name" name="name" required placeholder="Masukkan nama struktur...">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Struktur Organisasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" id="editType">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const type = button.getAttribute('data-type');
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');

        editModal.querySelector('#editType').value = type;
        editModal.querySelector('#editId').value = id;
        editModal.querySelector('#editName').value = name;

        editModal.querySelector('#editForm').action = `/organization/${type}/${id}`;
    });
});

function confirmDelete(url) {
    Swal.fire({
        title: 'Anda yakin?',
        text: "Semua data di bawahnya juga akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(res => {
                if (res.ok) {
                    location.reload();
                } else {
                    Swal.fire('Gagal!', 'Tidak dapat menghapus data.', 'error');
                }
            });
        }
    });
}
</script>


@endsection
    
