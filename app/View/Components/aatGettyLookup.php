<?php

namespace App\View\Components;

use EasyRdf\Resource;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use EasyRdf\Graph;
use Illuminate\Support\Facades\Cache;

class aatGettyLookup extends Component
{
    /**
     * @var Resource|mixed|string|null
     */
    public mixed $scopeNote = NULL;
    /**
     * @var array
     */
    public array $altNames = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $aatID)
    {
        $keyScope = $aatID . '-axiellAgentBiographyAATID-scope';
        $keyAltNames = $aatID . '-axiellAgentBiographyAATID-names';
        $expiresAt = now()->addDays(10);
        if (Cache::has($keyScope) && Cache::has($keyAltNames)) {
            $scopeNote = Cache::get($keyScope);
            $altNames = Cache::get($keyAltNames);
        } else {
            try {
                $aat = Graph::newAndLoad("http://vocab.getty.edu/aat/$aatID.rdf");
                $scope = $aat->resource("http://vocab.getty.edu/aat/$aatID");
                if (!is_null($scope->get('skos:scopeNote'))) {
                    $scopeNote = $aat->get($scope->get('skos:scopeNote')->getUri(), 'rdf:value');
                } else {
                    $scopeNote = NULL;
                }
                if (!is_null($scope->get('skos:altLabel'))) {
                    $altNames = $aat->allLiterals("http://vocab.getty.edu/aat/$aatID", 'skos:altLabel');
                } else {
                    $altNames = [];
                }
                Cache::put($keyScope, $scopeNote, $expiresAt);
                Cache::put($keyAltNames, $altNames, $expiresAt);
            } catch (Exception $e) {
            }
        }
        $this->scopeNote = $scopeNote ?? NULL;
        $this->altNames = $altNames ?? [];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.aat-getty-lookup');
    }
}
