<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    public function index()
    {
        return view('admin.regions.index', ['regions' => Region::flatOptions()->load('children')->loadCount('posts')]);
    }

    public function create()
    {
        return view('admin.regions.form', ['region' => new Region(['is_active' => true, 'show_in_menu' => true]), 'parents' => Region::flatOptions()]);
    }

    public function edit(Region $region)
    {
        $exclude = $region->descendantIds();

        return view('admin.regions.form', ['region' => $region, 'parents' => Region::flatOptions()->whereNotIn('id', $exclude)]);
    }

    public function store(Request $request)
    {
        Region::create($this->validated($request));

        return redirect()->route('admin.regions.index')->with('ok', 'Wilayah ditambahkan.');
    }

    public function update(Request $request, Region $region)
    {
        $region->update($this->validated($request, $region));

        return redirect()->route('admin.regions.index')->with('ok', 'Wilayah diperbarui.');
    }

    public function destroy(Region $region)
    {
        $region->delete();

        return back()->with('ok', 'Wilayah dihapus.');
    }

    private function validated(Request $request, ?Region $region = null): array
    {
        $request->merge(['slug' => Str::slug($request->slug ?: $request->name)]);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => ['required', 'max:150', Rule::unique('regions', 'slug')->ignore($region?->id)],
            'parent_id' => ['nullable', 'exists:regions,id', Rule::notIn($region ? $region->descendantIds() : [])],
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        $data['show_in_menu'] = $request->boolean('show_in_menu');

        return $data;
    }
}
