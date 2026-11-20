<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['storage' => $this->storageStatus()]);
    }

    private function storageStatus(): array
    {
        $disk = config('filesystems.default');
        $conf = config("filesystems.disks.$disk", []);
        $status = ['disk' => $disk, 'driver' => $conf['driver'] ?? '-', 'ok' => false, 'error' => null, 'details' => []];
        if (($conf['driver'] ?? null) === 's3') {
            foreach (['key' => 'AWS_ACCESS_KEY_ID', 'secret' => 'AWS_SECRET_ACCESS_KEY', 'region' => 'AWS_DEFAULT_REGION', 'bucket' => 'AWS_BUCKET', 'endpoint' => 'AWS_ENDPOINT'] as $k => $env) {
                $v = (string) ($conf[$k] ?? '');
                $status['details'][$env] = $v === '' ? 'KOSONG' : (in_array($k, ['key', 'secret']) ? 'terisi ('.strlen($v).' karakter)' : $v);
            }
            $status['details']['AWS_USE_PATH_STYLE_ENDPOINT'] = ! empty($conf['use_path_style_endpoint']) ? 'true' : 'false';
        }
        try {
            $test = Storage::build(array_merge($conf, ['throw' => true]));
            $file = 'uploads/.cek-penyimpanan.txt';
            $test->put($file, now()->toIso8601String());
            $test->delete($file);
            $status['ok'] = true;
        } catch (\Throwable $e) {
            $status['error'] = \Illuminate\Support\Str::limit($e->getMessage(), 400);
        }

        return $status;
    }

    public function update(Request $request)
    {
        $rules = collect(Setting::DEFAULTS)->map(fn () => 'nullable|string|max:1000')->all();
        $data = $request->validate($rules);
        foreach (array_keys(Setting::DEFAULTS) as $key) {
            Setting::put($key, $key === 'ticker_enabled' ? ($request->boolean($key) ? '1' : '0') : ($data[$key] ?? ''));
        }

        return back()->with('ok', 'Pengaturan disimpan.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ], [], ['current_password' => 'password lama', 'password' => 'password baru']);
        $request->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('ok', 'Password berhasil diganti.');
    }
}
