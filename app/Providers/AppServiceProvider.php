<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ImLiam\BladeHelper\Facades\BladeHelper;
use Geocoder\Query\GeocodeQuery;
use Illuminate\Support\Facades\Http;
use Storage;
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
  }
}
