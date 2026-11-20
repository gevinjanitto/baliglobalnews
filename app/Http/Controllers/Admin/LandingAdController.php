<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\LandingAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandingAdController extends Controller
{
    public function edit()
    {
        return view('admin.landing-ads', [
            'ads' => Ad::orderByDesc('is_active')->orderBy('name')->get(),
            'selected' => LandingAd::all()->groupBy('slot')->map(fn ($g) => $g->pluck('ad_id')->all())->all(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate(['slots' => 'array', 'slots.*' => 'array', 'slots.*.*' => 'integer|exists:ads,id']);
        DB::transaction(function () use ($request) {
            LandingAd::query()->delete();
            foreach ($request->input('slots', []) as $slot => $ids) {
                if (! array_key_exists($slot, LandingAd::SLOTS)) {
                    continue;
                }
                foreach (array_values($ids) as $i => $id) {
                    LandingAd::create(['slot' => $slot, 'ad_id' => $id, 'sort_order' => $i]);
                }
            }
        });

        return back()->with('ok', 'Pengaturan iklan landing disimpan.');
    }
}
