<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AxiellTerm
{
    /**
     * @param string $priref
     * @return object
     */
    public static function find(string $priref): object
    {
        $key = $priref . '-axiellTerminology';
        $expiresAt = now()->addDays(10);
        $data = [];
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {

            $params = array(
                '?&database' => 'thesaurus.uf',
                'search' => 'priref=' . $priref,
                'limit' => 1,
                'output' => 'json'
            );
            $response = Http::get(env('ADLIBURI') . urldecode(http_build_query($params)));
            $axiell = $response->object();
            if(property_exists($axiell->adlibJSON,'recordList')){
                if(is_array($axiell->adlibJSON->recordList->record)){
                    $data = $axiell->adlibJSON->recordList->record[0];
                }
            } else {
                $data = NULL;
            }
            Cache::put($key, $data, $expiresAt);
        }
        return $data;
    }
}
