<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\Divisi;
use App\Models\Department;
use App\Models\Section;
use App\Models\Unit;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        // Ambil direktur utama beserta relasi
        $mainDirector = Director::with([
            'subDirectors.divisi.department.section.unit',
            'subDirectors.divisi.department.unit',
            'subDirectors.department.section.unit',
            'subDirectors.department.unit',
            'divisi.department.section.unit',
            'divisi.department.unit',
            'department.section.unit',
            'department.unit'
        ])->where('is_main', 1)->first();

        return view('superadmin.organization_manage', compact('mainDirector'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());

        $type = $request->type;
        $name = $request->name;
        if(!empty($request->kode)){
            $kode = $request->kode;
        } else {
            $kode = NULL;
        }
        $parent = $request->parent_id;

        // Siapkan parent type & id
        $parentType = null;
        $parentId = null;
        if ($parent) {
            [$parentType, $parentId] = explode('-', $parent);
        }

        switch ($type) {
            case 'Director':
                $director = new Director();
                $director->name_director = $name;
                $director->kode_director = $kode;
                $director->is_main = 0;
                if ($parentType == 'director') {
                    $director->parent_director_id = $parentId;
                }
                $director->save();
                break;

            case 'Divisi':
                $divisi = new Divisi();
                $divisi->nm_divisi = $name;
                $divisi->kode_divisi = $kode;
                if ($parentType == 'director') {
                    $divisi->director_id_director = $parentId;
                }
                $divisi->save();
                break;

            case 'Department':
                $department = new Department();
                $department->name_department = $name;
                $department->kode_department = $kode;
                if ($parentType == 'divisi') {
                    $department->divisi_id_divisi = $parentId;
                    $divisi = Divisi::find($parentId);
                    $department->director_id_director = $divisi?->director_id_director;
                } elseif ($parentType == 'director') {
                    $department->director_id_director = $parentId;
                }
                $department->save();
                break;

            case 'Section':
                $section = new Section();
                $section->name_section = $name;
                if ($parentType == 'department') {
                    $section->department_id_department = $parentId;
                }
                $section->save();
                break;

            case 'Unit':
                $unit = new Unit();
                $unit->name_unit = $name;
                if ($parentType == 'section') {
                    $unit->section_id_section = $parentId;
                } elseif ($parentType == 'department') {
                    $unit->department_id_department = $parentId;
                }
                $unit->save();
                break;
        }

        return redirect()->route('organization.manageOrganization')->with('success', 'User added successfully.');
    }

    private function rules()
    {
        return [
            'type' => 'required|in:Director,Divisi,Department,Section,Unit',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|string'
        ];
    }

    public function update(Request $request, $type, $id)
    {
        $name = $request->input('name');
        if(!empty($request->input('kode'))){
            $kode = $request->input('kode');
        } else {
            $kode = NULL;
        }
        switch ($type) {
            case 'director':
                $model = Director::findOrFail($id);
                $model->name_director = $name;
                $model->kode_director = $kode;
                break;
            case 'divisi':
                $model = Divisi::findOrFail($id);
                $model->nm_divisi = $name;
                $model->kode_divisi = $kode;
                break;
            case 'department':
                $model = Department::findOrFail($id);
                $model->name_department = $name;
                $model->kode_department = $kode;
                break;
            case 'section':
                $model = Section::findOrFail($id);
                $model->name_section = $name;
                break;
            case 'unit':
                $model = Unit::findOrFail($id);
                $model->name_unit = $name;
                break;
        }
        $model->save();
        return back()->with('success', ucfirst($type).' berhasil diupdate');
    }

    public function delete($type, $id)
    {
        switch ($type) {
            case 'director':
                $node = Director::findOrFail($id);
                $node->subDirectors()->delete();
                $node->divisi()->delete();
                $node->department()->delete();
                break;
            case 'divisi':
                $node = Divisi::findOrFail($id);
                $node->department()->delete();
                break;
            case 'department':
                $node = Department::findOrFail($id);
                $node->section()->delete();
                $node->unit()->delete();
                break;
            case 'section':
                $node = Section::findOrFail($id);
                $node->unit()->delete();
                break;
            case 'unit':
                $node = Unit::findOrFail($id);
                break;
        }
        $node->delete();
        return response()->json(['success' => true]);
    }

}
