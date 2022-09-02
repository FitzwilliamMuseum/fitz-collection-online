<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class closeMatchIdentifiers extends Component
{
    public array $uris = [
        'nomisma_id' => 'http://nomisma.org/id/',
        'aat_id' => 'http://vocab.getty.edu/aat/',
        'tgn_id' => 'http://vocab.getty.edu/tgn/',
        'ulan_id' => 'http://vocab.getty.edu/ulan/',
        'wikidata_id' => 'http://www.wikidata.org/entity/',
        'dbpedia_id' => 'http://dbpedia.org/resource/',
        'geonames_id' => 'http://sws.geonames.org/',
        'pleiades_id' => 'http://pleiades.stoa.org/places/',
    ];

    public array $combined = [];


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public array $identifiers)
    {
        $urisCombined = $this->combineValues($this->uris, $identifiers);
        unset($urisCombined['axiell_id']);
        $this->combined = array_filter($urisCombined);
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public function combineValues(array $array1, array $array2): array
    {
        $array3 = array();
        foreach($array1 as $key => $value)
        {
            if(array_key_exists($key, $array2) && $array2[$key] != '')
            {
                $array3[$key] = $value . $array2[$key];
            }

        }

        return $array3;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.close-match-identifiers');
    }
}
