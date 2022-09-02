<?php

namespace App\Models;

use App\DirectUs;

class AxiellPlaceNames extends Model
{

    /**
     * @param string $place
     * @return array|null
     */
    public static function find(string $place): ?array
    {
        $api = new DirectUs(
            'place_name_aliases',
            array(
            'fields' => '*',
            'filter[axiell_name][eq]' => $place),
            '*.*.*'
        );
        return Collect($api->getData()['data'])->first();
    }
}
