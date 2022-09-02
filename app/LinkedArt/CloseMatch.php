<?php

namespace App\LinkedArt;

use App\Models\LinkedArtIdentifiers;

class CloseMatch
{
    const ULAN_URI = 'http://vocab.getty.edu/page/ulan/';
    const GEONAMES_URI = 'https://sws.geonames.org/';
    const PLEIADES_URI = 'https://pleiades.stoa.org/places/';
    const AAT_URI = 'http://vocab.getty.edu/page/aat/';
    const WIKIDATA_URI = 'https://www.wikidata.org/entity/';
    const DBPEDIA_URI = 'https://dbpedia.org/resource/';
    const TGN_URI = 'http://vocab.getty.edu/page/tgn/';
    const NOMISMA_URI = 'https://nomisma.org/id/';

    /**
     * @param string $axiell_id
     * @return array
     */
    public static function buildCloseMatch(string $axiell_id): array
    {
        $identifiers = LinkedArtIdentifiers::find($axiell_id);
        if ($identifiers) {
            $identifiers = array_filter($identifiers);
            $close_match = [];
            foreach ($identifiers as $key => $value) {
                switch ($key) {
                    case 'ulan_id':
                        $close_match[] = self::ULAN_URI . $value;
                        break;
                    case 'geonames_id':
                        $close_match[] = self::GEONAMES_URI . $value;
                        break;
                    case 'pleiades_id':
                        $close_match[] = self::PLEIADES_URI . $value;
                        break;
                    case 'aat_id':
                        $close_match[] = self::AAT_URI . $value;
                        break;
                    case 'wikidata_id':
                        $close_match[] = self::WIKIDATA_URI . $value;
                        break;
                    case 'tgn_id':
                        $close_match[] = self::TGN_URI . $value;
                        break;
                    case 'dbpedia_id':
                        $close_match[] = self::DBPEDIA_URI . $value;
                        break;
                    case 'nomisma_id':
                        $close_match[] = self::NOMISMA_URI . $value;
                        break;
                }
            }
            return array_filter($close_match);
        } else {
            return (array) null;
        }
    }
}
