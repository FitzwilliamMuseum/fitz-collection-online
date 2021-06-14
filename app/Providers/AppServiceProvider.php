<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ImLiam\BladeHelper\Facades\BladeHelper;
use Illuminate\Support\Facades\Http;
use Storage;
use App\LookupPlace;

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
    BladeHelper::directive('fa', function(string $iconName, string $text = null, $classes = '') {
            if (is_array($classes)) {
                $classes = join(' ', $classes);
            }

            $text = $text ?? $iconName;

            return "<i class='fas fa-{$iconName} {$classes}' aria-hidden='true' title='{$text}'></i><span class='sr-only'>{$text}</span>";
    });

    BladeHelper::directive('lookup_term', function( $term){
        $json =  '[{"term": "term-14162","label": "Imperial (Roman)"},
        {"term": "term-14163","label": "Imperial (Roman)"}]';
        rtrim($json, "\0");
        $arr = json_decode($json,true);
        $filtered = array_filter($arr, function($object) use($term) {
          return ($object['term'] === $term);
        });
       return($filtered[0]['label']);
    });

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
