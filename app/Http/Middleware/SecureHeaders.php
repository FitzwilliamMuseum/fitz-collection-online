<?php

namespace App\Http\Middleware;

use Closure;
use http\Env\Request;

/*
@see https://danieldusek.com/enabling-security-headers-for-your-website-with-php-and-laravel.html
*/

class SecureHeaders
{
    /**
     * @var array|string[]
     */
    private array $unwantedHeaderList = [
        'X-Powered-By',
        'Server',
    ];

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $this->removeUnwantedHeaders($this->unwantedHeaderList);
        $response = $next($request);
        $response->headers->set('X-Powered-By', 'Dan\'s magic army of elves');
        return $response;
    }

    /**
     * @param array $headerList
     * @return void
     */
    private function removeUnwantedHeaders(array $headerList)
    {
        foreach ($headerList as $header) {
            header_remove($header);
        }
    }

}
