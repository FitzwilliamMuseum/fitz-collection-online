<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeadersMiddleware
{

  private array $unwantedHeaderList = [
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
      $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
//      $response->headers->set('X-Content-Type-Options', 'nosniff');
      $response->headers->set('X-XSS-Protection', '1; mode=block');
      $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
      return $response;
  }

    /**
     * @param array $headerList
     * @return void
     */
  private function removeUnwantedHeaders(array $headerList)
  {
      foreach ($headerList as $header)
          header_remove($header);
  }
}
