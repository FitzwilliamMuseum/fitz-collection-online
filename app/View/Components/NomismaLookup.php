<?php

namespace App\View\Components;
use EasyRdf\Graph;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class NomismaLookup extends Component
{
    public ?array $labels;

    public ?string $lat;

    public ?string $long;

    public ?string $type;

    public ?array $definitions;

    /**
     * @param string $nomismaID
     */
    public function __construct(public string $nomismaID)
    {
        $key = $nomismaID . '-nomismaLookup-rdf';
        $expiresAt = now()->addDays(10);
        if (Cache::has($key)) {
            $numismatics = Cache::get($key);
        } else {
            $nomisma = Graph::newAndLoad('http://nomisma.org/id/' . $nomismaID . '.rdf');
            $labels = $nomisma->allLiterals("http://nomisma.org/id/$nomismaID", 'skos:prefLabel');
            $definitions = $nomisma->allLiterals("http://nomisma.org/id/$nomismaID", 'skos:definition');
            $type = $nomisma->get("http://nomisma.org/id/$nomismaID", 'rdf:type');
            $lat = $nomisma->get("http://nomisma.org/id/$nomismaID#this", 'geo:lat');
            $lon = $nomisma->get("http://nomisma.org/id/$nomismaID#this", 'geo:long');
            $numismatics = array(
                'labels' => $labels,
                'definitions' => $definitions,
                'type' => $type,
                'lat' => $lat,
                'long' => $lon
            );
            Cache::put($key, $numismatics, $expiresAt);
        }
        $this->definitions = $numismatics['definitions'];
        $this->labels = $numismatics['labels'];
        $this->lat = $numismatics['lat'];
        $this->long = $numismatics['long'];
        $this->type = $numismatics['type'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.nomisma-lookup');
    }
}
