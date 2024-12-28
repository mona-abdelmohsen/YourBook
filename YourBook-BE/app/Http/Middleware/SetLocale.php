<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // App::setLocale('ar');
        // return $next($request);

        $locale = substr($request->header('Accept-Language', 'en'), 0, 2);
        $supportedLocales = ['en', 'ar'];
    
        App::setLocale(in_array($locale, $supportedLocales) ? $locale : 'en');
    
        return $next($request);
    }
}
