<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait StoresImages
{
    protected function storeImage(?UploadedFile $file, string $folder, ?string $old = null): ?string
    {
        if (! $file) {
            return $old;
        }
        $path = $file->storeAs('uploads/'.$folder.'/'.date('Y/m'), Str::random(24).'.'.$file->extension(), config('filesystems.default'));
        if (! $path) {
            throw ValidationException::withMessages(['image' => 'Gambar gagal disimpan ke penyimpanan "'.config('filesystems.default').'". Cek Pengaturan → Status Penyimpanan.']);
        }
        $this->deleteImage($old);

        return $path;
    }

    protected function deleteImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/')) {
            Storage::disk(config('filesystems.default'))->delete($path);
        }
    }
}
