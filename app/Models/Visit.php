<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Visit extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['visitor_id', 'ip', 'country', 'country_code', 'region', 'city', 'geo_done', 'device', 'browser', 'os', 'path', 'post_id', 'referrer'];

    protected $casts = ['geo_done' => 'boolean'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getLocationAttribute(): string
    {
        if (! $this->geo_done) {
            return 'Mendeteksi…';
        }

        return collect([$this->city, $this->country])->filter()->implode(', ') ?: 'Tidak diketahui';
    }

    public static function isBot(string $ua): bool
    {
        return $ua === '' || (bool) preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|curl|wget|python|headless|lighthouse/i', $ua);
    }

    public static function parseAgent(string $ua): array
    {
        $device = preg_match('/iPad|Tablet/i', $ua) ? 'Tablet' : (preg_match('/Mobi|Android|iPhone/i', $ua) ? 'Mobile' : 'Desktop');
        $browser = match (true) {
            str_contains($ua, 'Edg') => 'Edge',
            str_contains($ua, 'OPR') || str_contains($ua, 'Opera') => 'Opera',
            str_contains($ua, 'SamsungBrowser') => 'Samsung Internet',
            str_contains($ua, 'FBAN') || str_contains($ua, 'FBAV') => 'Facebook App',
            str_contains($ua, 'Instagram') => 'Instagram App',
            str_contains($ua, 'Chrome') || str_contains($ua, 'CriOS') => 'Chrome',
            str_contains($ua, 'Firefox') || str_contains($ua, 'FxiOS') => 'Firefox',
            str_contains($ua, 'Safari') => 'Safari',
            default => 'Lainnya',
        };
        $os = match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Android') => 'Android',
            (bool) preg_match('/iPhone|iPad|iPod/', $ua) => 'iOS',
            str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'CrOS') => 'ChromeOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Lainnya',
        };

        return compact('device', 'browser', 'os');
    }

    public static function resolveGeo(): void
    {
        $ips = static::where('geo_done', false)->distinct()->limit(100)->pluck('ip');
        if ($ips->isEmpty()) {
            return;
        }
        $public = $ips->filter(fn ($ip) => filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
        $ips->diff($public)->each(fn ($ip) => static::where('ip', $ip)->update(['geo_done' => true, 'city' => 'Jaringan lokal']));
        if ($public->isEmpty()) {
            return;
        }
        try {
            $res = Http::timeout(5)->post('http://ip-api.com/batch?fields=status,country,countryCode,regionName,city,query', $public->values()->all());
            foreach ((array) $res->json() as $row) {
                $ok = ($row['status'] ?? '') === 'success';
                static::where('ip', $row['query'] ?? '')->update([
                    'geo_done' => true,
                    'country' => $ok ? $row['country'] : null,
                    'country_code' => $ok ? $row['countryCode'] : null,
                    'region' => $ok ? $row['regionName'] : null,
                    'city' => $ok ? $row['city'] : null,
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
