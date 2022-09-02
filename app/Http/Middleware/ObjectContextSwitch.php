<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ObjectContextSwitch
{

    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', ['text/html','application/xhtml+xml','application/xml','application/ld+json','application/ld+json;profile=\"https://linked.art/ns/v1/linked-art.json\"']);
        return $next($request);
    }
}
