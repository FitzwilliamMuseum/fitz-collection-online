<?php

namespace App\View\Components;

use EasyRdf\Graph;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use function PHPUnit\Framework\throwException;
use Illuminate\Support\Facades\Cache;

class nomismaEntityLookup extends Component
{
    public ?array $labels;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $nomismaID)
    {
        try {
            $nomisma = Graph::newAndLoad('http://nomisma.org/id/' . $nomismaID . '.rdf');
            $labels = $nomisma->allLiterals("http://nomisma.org/id/$nomismaID", 'skos:prefLabel');
            $this->labels = $labels;
        } catch (Exception $e) {
            throwException(new Exception('No nomisma entity found for ' . $nomismaID . ' ' . $e->getMessage()));
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.nomisma-entity-lookup');
    }
}
