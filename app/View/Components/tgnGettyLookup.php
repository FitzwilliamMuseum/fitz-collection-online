<?php

namespace App\View\Components;

use EasyRdf\Graph;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class tgnGettyLookup extends Component
{
    /**
     * @var Resource|mixed|string|null
     */
    public mixed $scopeNote;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $tgnID)
    {
        $keyScope = $tgnID . '-axiellAgentTGNID-scopenoteLookup';
        $expiresAt = now()->addDays(10);
        if (Cache::has($keyScope) ) {
            $scopeNote = Cache::get($keyScope);
        } else {
            $tgn = Graph::newAndLoad("http://vocab.getty.edu/tgn/$tgnID", 'rdfxml');
            $scope = $tgn->resource("http://vocab.getty.edu/tgn/$tgnID");
            if($scope->hasProperty('skos:scopeNote')) {
                $uri = $scope->get('skos:scopeNote')->getUri();
                $scope = Graph::newAndLoad($uri,'rdfxml');
                $scopeNote = $scope->getLiteral($uri, 'rdf:value');
            } else {
                $scopeNote = null;
            }
            Cache::put($keyScope, $scopeNote, $expiresAt);
        }
        $this->scopeNote = $scopeNote;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.tgn-getty-lookup');
    }
}
