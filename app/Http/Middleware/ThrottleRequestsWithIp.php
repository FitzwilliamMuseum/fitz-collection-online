<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Api\IpAddress;

use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;

class ThrottleRequestsWithIp extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @param string $prefix
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 300, $decayMinutes = 1, $prefix = ''): mixed
    {
        if(in_array($request->ip(), IpAddress::whitelist()))
            return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);

        return parent::handle($request, $next, 60, 1, $prefix);
    }
}
