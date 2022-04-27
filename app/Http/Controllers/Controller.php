<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Artisan;
use App\FitzElastic\Elastic;
use JetBrains\PhpStorm\Pure;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return Elastic
     */
    #[Pure] public function getElastic(): Elastic
    {
      return new Elastic();
    }

    /**
     * @return string
     */
    public function clearCache(): String
    {
      Artisan::call('cache:clear');
      return "Cache is cleared";
    }
}
