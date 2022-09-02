<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AxiellTermDisplay extends Component
{
    /**
     * @var array
     */
    public array $broaderTerms = [];

    /**
     * @var array
     */
    public array $usedFor = [];

    /**
     * @var string
     */
    public string $termType = '';

    /**
     * @var string
     */
    public string $nameType = '';

    /**
     * @var string
     */
    public array $termTypes = [];

    /**
     * @var string
     */
    public string $created = '';

    /**
     * @var array
     */
    public array $narrowerTerms = [];

    /**
     * @var array
     */
    public array $equivalentTerms = [];

    /**
     * @var string
     */
    public string $termNumber = '';

    /**
     * @var array
     */
    public array $biographicalNote = [];

    /**
     * @var array
     */
    public array $nameTypes = [];

    /**
     * @param object $axiell
     * @param array|null $identifiers
     */
    public function __construct(public object $axiell, public ?array $identifiers)
    {
        if (property_exists($axiell, 'ulan.biography')) {
            $this->biographicalNote = $axiell->{"ulan.biography"};
        }

        if (property_exists($axiell, 'broader_term') && property_exists($axiell, 'broader_term.lref')) {
            $this->broaderTerms = array_combine($axiell->broader_term, $axiell->{"broader_term.lref"});
        }
        if (property_exists($axiell, 'used_for') && property_exists($axiell, 'used_for.lref')) {
            $this->usedFor = array_combine($axiell->used_for, $axiell->{"used_for.lref"});
        }
        if (property_exists($axiell, 'narrower_term') && property_exists($axiell, 'narrower_term.lref')) {
            $this->narrowerTerms = array_combine($axiell->narrower_term, $axiell->{"narrower_term.lref"});
        }
        if (property_exists($axiell, 'equivalent_term') && property_exists($axiell, 'equivalent_term.lref')) {
            $this->equivalentTerms = array_combine($axiell->equivalent_term, $axiell->{"equivalent_term.lref"});
        }
        if(property_exists($axiell, 'name.type')) {
            if (is_array($axiell->{"name.type"})) {
                if (count($axiell->{"name.type"}[0]->value) > 1) {
                    $this->nameType = ucfirst($axiell->{"name.type"}[0]->value[1]);
                    foreach ($axiell->{"name.type"} as $type) {
                        if (count($type->value) > 1) {
                            $this->nameTypes[] = ucfirst($type->value[1]);
                        }
                    }
                }
            }
        }
        if (property_exists($axiell, 'term.type')) {
            if (is_array($axiell->{"term.type"})) {
                if (count($axiell->{"term.type"}[0]->value) > 1) {
                    $this->termType = ucfirst($axiell->{"term.type"}[0]->value[1]);
                    foreach ($axiell->{"term.type"} as $type) {
                        if (count($type->value) > 1) {
                            $this->termTypes[] = ucfirst($type->value[1]);
                        }
                    }
                }
            }
        }

        if (property_exists($axiell->{"@attributes"}, 'created')) {
            $this->created = $axiell->{"@attributes"}->created;
        }
        if (property_exists($axiell, 'term.number')) {
            $this->termNumber = '3000' . $axiell->{"term.number"}[0];
        }
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('components.axiell-term-display');
    }
}
