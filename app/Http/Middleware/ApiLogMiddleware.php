<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiLog;
use Illuminate\Http\Response;

class ApiLogMiddleware
{
    private mixed $enabled;

    public function __construct()
    {
        $this->enabled = config('api-log.enabled', false);
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        return $next($request);
    }


    /**
     * Handle tasks after the response has been sent to the browser.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        if ($request->route()->getName() != "api.log" && $this->enabled && $request->wantsJson()) {

            ApiLog::create([
                'request_full_url' => method_exists($request, 'fullUrl') ? $request->fullUrl() : null,
                'request_method' => method_exists($request, 'method') ? $request->method() : null,
                'request_body' => method_exists($request, 'all') ? $request->all() : null,
                'request_ip' => method_exists($request, 'ip') ? $request->ip() : null,
                'request_header' => method_exists($request, 'header') ? $request->header() : null,
                'request_agent' => method_exists($request, 'header') ? $request->header('User-Agent') : null,
                'response_content' => method_exists($response, 'content') ? $response->content() : null,
                'response_status_code' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
                'user_id' =>$request->user()->id ?? null,
                'user_timezone' => $request->user()->timezone ?? null,
            ]);

        }
    }
}
