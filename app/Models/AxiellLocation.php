<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Cache;

class AxiellLocation
{
    /**
     * @param string $priref
     * @return object
     */
    public static function find(string $priref): object
    {
        $key = $priref . '-axiellLocation';
        $expiresAt = now()->addDays(10);
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            $fieldsToQuery = array(
                'current_location',
                'current_location.type',
                'current_location.description'
            );
            $fields = '&fields=' . implode(',', $fieldsToQuery);
            $response = Http::get(env('ADLIBURI') . '?&database=objects.uf&search=priref=' . $priref . $fields . '&limit=1&output=json');
            $data = $response->object();
            Cache::put($key, $data, $expiresAt);
        }
        return $data;
    }
}
