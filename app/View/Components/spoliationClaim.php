<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class spoliationClaim extends Component
{
    public array $spoliation;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $spoliation)
    {
        $this->spoliation = $spoliation;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.spoliation-claim');
    }
}
