<?php

namespace App\Models;

use App\DirectUs;

class SpoliationClaims extends Model
{
    public static string $table = 'spoliation_claims';

    public static function find(string $priref): array
    {
        $api = new DirectUs();
        $api->setEndpoint(self::$table);
        $api->setArguments(
            array(
                'fields' => '*',
                'filter[priref][eq]' => $priref
            )
        );
        return $api->getData();
    }
}
