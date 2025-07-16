<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Manajemen Pengguna</title>
        <link href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/superadmin/user-manage.css') }}">
    </head>
    <body>
        <div class="modal fade show" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true" style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('user-manage/update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <img src="/img/user-manage/editUser.png" alt="editUser" style="margin-right: 10px;">
                            <h5 class="modal-title" id="editUserModalLabel"><b>Edit Pengguna</b></h5>
                            <button type="button" class="btn-close" onclick="window.location='{{ route('user.manage') }}'" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="id" class="form-label">ID Pengguna : <span class="text-danger">*</span></label>
                                    <input type="text" name="id" id="id" class="form-control" value="{{ $user->id }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email : <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required autofocus autocomplete="email">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">Nama Depan : <span class="text-danger">*</span></label>
                                    <input type="text" name="firstname" id="firstname" class="form-control" value="{{ old('firstname', $user->firstname) }}" required autofocus autocomplete="firstname">
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Nama Belakang : <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" id="lastname" class="form-control" value="{{ old('lastname', $user->lastname) }}" required autofocus autocomplete="lastname">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Nama Pengguna : <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $user->username) }}" required autofocus autocomplete="username">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">No. Telpon : <span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" required autofocus autocomplete="phone_number">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Kata Sandi : <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi : <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
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
                                    }
                                    
                                    @endphp

                                    @if($mainDirector)
                                        @php renderOrgRecursive($mainDirector); @endphp
                                    @endif
                                    <label for="divisi_id_divisi" class="form-label">Pilih Posisi<span style="color : red;"> *</span></label>
                                    <select class="form-select" id="parent_id" name="parent_id">
                                        <option value="">-- Pilih Posisi --</option>
                                        @php
                                        function renderOrgOptions($node, $level = 0) {
                                            $indent = str_repeat('&nbsp;', $level * 4);
                                            if(isset($node->name_director))
                                                echo "<option value='{$node->id_director}' data-type='director'>{$indent}Direktur: {$node->name_director}</option>";
                                            elseif(isset($node->nm_divisi))
                                                echo "<option value='{$node->id_divisi}' data-type='divisi'>{$indent}--> Divisi: {$node->nm_divisi}</option>";
                                            elseif(isset($node->name_department))
                                                echo "<option value='{$node->id_department}' data-type='department'>{$indent}-----> Departemen: {$node->name_department}</option>";
                                            elseif(isset($node->name_section))
                                                echo "<option value='{$node->id_section}' data-type='section'>{$indent}--------> Bagian: {$node->name_section}</option>";
                                            elseif(isset($node->name_unit))
                                                echo "<option value='{$node->id_unit}' data-type='unit'>{$indent}-----------> Unit: {$node->name_unit}</option>";

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
                                        <input type="hidden" name="parent_type" id="parent_type">
                                </div>
                                <div class="col-md-6">
                                    <label for="position_id_position" class="form-label">Pilih Jabatan<span style="color : red;"> *</span></label>
                                    <select name="position_id_position" id="position_id_position" class="form-control" required autofocus autocomplete="position_id_position">
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id_position }}">{{ $position->nm_position }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="izin">
                                    <label for="role_id_role" class="form-izin">Izin Akses <span class="text-danger">*</span></label>
                                    @foreach ($roles as $role)
                                        <label for="role_{{ $role->id_role }}">{{ $role->nm_role }}</label>
                                        <input type="radio" name="role_id_role" value="{{ $role->id_role }}" id="role_{{ $role->id_role }}" {{ old('role_id_role', $user->role_id_role) == $role->id_role ? 'checked' : '' }} required>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-cancel" onclick="window.location='#'">Batal</button>
                            <button type="submit" class="btn btn-save">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const parentSelect = document.getElementById('parent_id');
        const parentTypeInput = document.getElementById('parent_type');

        parentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const type = selectedOption.getAttribute('data-type') || '';
            parentTypeInput.value = type;
        });
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const parentSelect = document.getElementById('parent_id');
        const positionSelect = document.getElementById('position_id_position');
        const allPositions = @json($positions);

        // Mapping tipe induk -> array id_position
        const positionMap = {
            'director': [1], // Direktur
            'divisi': [2, 3, 4], // GM, SM, PJ SM
            'department': [3, 4, 5, 6, 7, 8], // SM, PJ SM, Manajer, SPV, PJ M, PJ SPV
            'section': [5, 6, 7, 8, 9], // Manajer, SPV, PJ M, PJ SPV, Staff
            'unit': [9] // Staff
        };

        function updatePositions() {
            const selectedOption = parentSelect.options[parentSelect.selectedIndex];
            const type = selectedOption ? selectedOption.getAttribute('data-type') : null;

            // Kosongkan posisi
            positionSelect.innerHTML = '';

            if (type && positionMap[type]) {
                // Enable select
                positionSelect.disabled = false;

                // Tampilkan hanya posisi yang sesuai mapping
                let filtered = allPositions.filter(pos => positionMap[type].includes(pos.id_position));
                filtered.forEach(pos => {
                    let opt = document.createElement('option');
                    opt.value = pos.id_position;
                    opt.textContent = pos.nm_position;
                    positionSelect.appendChild(opt);
                });
            } else {
                // Kalau belum pilih parent, disable
                positionSelect.disabled = true;
                // Tampilkan placeholder
                let opt = document.createElement('option');
                opt.textContent = '-- Pilih posisi setelah pilih induk --';
                positionSelect.appendChild(opt);
            }
        }

        // Saat load halaman langsung disable dulu
        updatePositions();

        // Saat induk diubah
        parentSelect.addEventListener('change', updatePositions);
    });
</script>