<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\URL;
use Closure;

class ForceSSL
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $domain = env('APP_DOMAIN');

        if (request()->getHttpHost() === $domain) {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
