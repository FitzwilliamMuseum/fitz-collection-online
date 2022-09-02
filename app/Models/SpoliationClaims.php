<?php

namespace App\Models;

use App\DirectUs;

class SpoliationClaims extends Model
{
    public static function find(string $priref): array
    {
        $api = new DirectUs(
            'spoliation_claims',
            array(
                'fields' => '*',
                'filter[priref][eq]' => $priref),
        );
        return $api->getData();
    }
}
