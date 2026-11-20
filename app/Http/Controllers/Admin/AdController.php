<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    use StoresImages;

    public function index()
    {
        return view('admin.ads.index', ['ads' => Ad::withCount('posts')->latest()->get()]);
    }

    public function create()
    {
        return view('admin.ads.form', ['ad' => new Ad(['is_active' => true, 'format' => 'banner'])]);
    }

    public function edit(Ad $ad)
    {
        return view('admin.ads.form', compact('ad'));
    }

    public function store(Request $request)
    {
        $ad = new Ad;
        $this->save($request, $ad);

        return redirect()->route('admin.ads.index')->with('ok', 'Iklan ditambahkan.');
    }

    public function update(Request $request, Ad $ad)
    {
        $this->save($request, $ad);

        return redirect()->route('admin.ads.index')->with('ok', 'Iklan diperbarui.');
    }

    public function destroy(Ad $ad)
    {
        $this->deleteImage($ad->image);
        $ad->delete();

        return back()->with('ok', 'Iklan dihapus.');
    }

    private function save(Request $request, Ad $ad): void
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'image' => ($ad->image ? 'nullable' : 'required').'|image|max:5120',
            'link_url' => 'nullable|url|max:500',
            'format' => 'required|in:'.implode(',', array_keys(Ad::FORMATS)),
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ], [], ['image' => 'gambar iklan', 'name' => 'nama iklan']);
        $data['image'] = $this->storeImage($request->file('image'), 'ads', $ad->image);
        $data['is_active'] = $request->boolean('is_active');
        $ad->fill($data)->save();
    }
}
