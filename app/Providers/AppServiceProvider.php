<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ImLiam\BladeHelper\Facades\BladeHelper;
use Illuminate\Support\Facades\Storage;
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

        # https://gist.github.com/brunogaspar/154fb2f99a7f83003ef35fd4b5655935?permalink_comment_id=3749071#gistcomment-3749071

        \Illuminate\Support\Collection::macro('recursive', function () {
            return $this->whenNotEmpty($recursive = function ($item) use (&$recursive) {
                if (is_array($item)) {
                    return $recursive(new static($item));
                } elseif ($item instanceof Collection) {
                    $item->transform(static function ($collection, $key) use ($recursive, $item) {
                        return $item->{$key} = $recursive($collection);
                    });
                } elseif (is_object($item)) {
                    foreach ($item as $key => &$val) {
                        $item->{$key} = $recursive($val);
                    }
                }
                return $item;
            });
        });

        BladeHelper::directive('humansize', function ($bytes, $precision = 2) {
            $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$precision}f", $bytes / pow(1024, $factor)) . @$size[$factor];
        });

        BladeHelper::directive('geo', function ($place) {
            $geo = new LookupPlace;
            $geo->setPlace($place);
            $results = $geo->lookup();

            if (!empty($results)) {
                $coords = $results->first()->getCoordinates();
                return $coords->getLatitude() . ',' . $coords->getLongitude();
            } else {
                return false;
            }
        });
    }
}
