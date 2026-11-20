<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    private const SKIP = ['admin', 'admin/*', 'media/*', 'iklan/*', 'up', 'setup.sh'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $ua = (string) $request->userAgent();
        if (! $request->isMethod('GET') || $request->is(...self::SKIP) || $response->getStatusCode() !== 200 || Visit::isBot($ua)) {
            return $response;
        }

        try {
            $vid = $request->cookie('bgn_vid');
            if (! $vid || strlen($vid) > 40) {
                $vid = Str::random(32);
                $response->headers->setCookie(cookie('bgn_vid', $vid, 60 * 24 * 730));
            }
            $ref = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);
            Visit::create(Visit::parseAgent($ua) + [
                'visitor_id' => $vid,
                'ip' => (string) $request->ip(),
                'path' => Str::limit('/'.ltrim($request->path(), '/').($request->getQueryString() ? '?'.$request->getQueryString() : ''), 490, ''),
                'post_id' => $request->attributes->get('visit_post_id'),
                'referrer' => $ref && $ref !== $request->getHost() ? Str::limit($ref, 250, '') : null,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        return $response;
    }
}
