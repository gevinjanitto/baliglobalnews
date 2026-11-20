<?php

use App\Models\Setting;
use Illuminate\Support\Str;

if (! function_exists('media_url')) {
    function media_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return $path;
        }
        if (Str::startsWith($path, ['seed/', 'brand/'])) {
            return asset($path);
        }

        return url('media/'.ltrim($path, '/'));
    }
}

if (! function_exists('clean_html')) {
    // strips unsafe tags and turns non-breaking spaces (old Quill output) into normal spaces so text wraps
    function clean_html(?string $html): string
    {
        $html = preg_replace('#<(script|iframe|object|embed)[^>]*>.*?</\1>#is', '', $html ?? '');

        return preg_replace('/(?<!&nbsp;|&#160;|\x{00A0}| )(?:&nbsp;|&#160;|\x{00A0})(?!&nbsp;|&#160;|\x{00A0}| )/u', ' ', $html);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('tgl')) {
    function tgl($date, string $format = 'd M Y'): string
    {
        return $date ? $date->locale('id')->translatedFormat($format) : '';
    }
}
