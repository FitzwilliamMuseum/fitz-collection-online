<?php

namespace App\View\Components;

use EasyRdf\Resource;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use EasyRdf\Graph;
use App\Models\AxiellAgent;
use Illuminate\Support\Facades\Cache;

class UlanBiography extends Component
{
    /**
     * @var Resource|mixed|string|null
     */
    public mixed $scopeNote;
    /**
     * @var array
     */
    public array $altNames;
    /**
     * @var object
     */
    public object $axiell;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $ulanID, public string $agentID)
    {
        $keyScope = $ulanID . '-axiellULAN-scope';
        $keyAltNames = $ulanID . '-axiellULAN-names';
        $keyAxiell = $agentID . '-axiellIDULAN';
        $expiresAt = now()->addDays(10);
        if (Cache::has($keyScope) && Cache::has($keyAltNames)) {
            $scopeNote = Cache::get($keyScope);
            $altNames = Cache::get($keyAltNames);
            $axiell = Cache::get($keyAxiell);
        } else {
            $ulan = Graph::newAndLoad("http://vocab.getty.edu/ulan/$ulanID", 'rdfxml');

            $scope = $ulan->resource("http://vocab.getty.edu/ulan/$ulanID");
            if (!is_null($scope->get('skos:scopeNote'))) {
                $scopeNote = $ulan->get($scope->get('skos:scopeNote')->getUri(), 'rdf:value');
            } else {
                $scopeNote = NULL;
            }
            $altNames = $ulan->allLiterals("http://vocab.getty.edu/ulan/$ulanID", 'skos:altLabel');
            $axiell = AxiellAgent::find($agentID);
            Cache::put($keyScope, $scopeNote, $expiresAt);
            Cache::put($keyAltNames, $altNames, $expiresAt);
            Cache::put($keyAxiell, $axiell, $expiresAt);
        }
        $this->axiell = $axiell;
        $this->scopeNote = $scopeNote;
        $this->altNames = $altNames;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.ulan-biography');
    }
}
