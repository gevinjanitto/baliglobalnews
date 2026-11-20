<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page(['show_in_footer' => true])]);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function store(Request $request)
    {
        Page::create($this->validated($request));

        return redirect()->route('admin.pages.index')->with('ok', 'Halaman ditambahkan.');
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->validated($request, $page));

        return redirect()->route('admin.pages.index')->with('ok', 'Halaman diperbarui.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return back()->with('ok', 'Halaman dihapus.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $request->merge(['slug' => Str::slug($request->slug ?: $request->title)]);
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'slug' => ['required', 'max:150', Rule::unique('pages', 'slug')->ignore($page?->id)],
            'body' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['body'] = clean_html($data['body'] ?? '');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['show_in_footer'] = $request->boolean('show_in_footer');

        return $data;
    }
}
