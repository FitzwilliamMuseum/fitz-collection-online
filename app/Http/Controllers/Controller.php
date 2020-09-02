<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Artisan;
use App\FitzElastic\Elastic;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getElastic()
    {
      return new Elastic();
    }

    public function clearCache()
    {
      Artisan::call('cache:clear');
      return "Cache is cleared";
    }
}
