<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ImLiam\BladeHelper\Facades\BladeHelper;
use Storage;
use App\LookupPlace;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
  /**
  * Register any application services.
  *
  * @return void
  */
  public function register()
  {
    //
  }

  /**
  * Bootstrap any application services.
  *
  * @return void
  */
  public function boot()
  {
    Paginator::useBootstrapFive();

    BladeHelper::directive('humansize', function ($bytes, $precision = 2) {
      $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
      $factor = floor((strlen($bytes) - 1) / 3);
      return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    });

    BladeHelper::directive('geo', function ($place) {
      $geo  = new LookupPlace;
      $geo->setPlace($place);
      $results = $geo->lookup();

      if(!empty($results)){
        $coords = $results->first()->getCoordinates();
        return $coords->getLatitude().',' .$coords->getLongitude();
      }

    });
  }
}
