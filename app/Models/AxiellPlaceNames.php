<?php

namespace App\Models;

use App\DirectUs;

class AxiellPlaceNames extends Model
{
    public static string $table = 'place_name_aliases';

    /**
     * @param string $place
     * @return array|null
     */
    public static function find(string $place): ?array
    {
        $api = new DirectUs();
        $api->setEndpoint(self::$table);
        $api->setArguments(
            array(
                'fields' => '*',
                'filter[axiell_name][eq]' => $place
            )
        );
        return Collect($api->getData()['data'])->first();
    }
}
