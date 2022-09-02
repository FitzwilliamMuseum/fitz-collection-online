<?php

namespace App\Models;

use App\DirectUs;

class LinkedArtIdentifiers extends Model
{

    public static function find(string $axiell_id): ?array
    {
        $api = new DirectUs(
            'linked_art_identifiers',
            array(
                'fields' => 'axiell_id,ulan_id,geonames_id,pleiades_id,aat_id,wikidata_id,tgn_id,dbpedia_id,nomisma_id',
                'filter[axiell_id][eq]' => $axiell_id
            )
        );
        return Collect($api->getData()['data'])->first();
    }
}
