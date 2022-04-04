<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class researchMLT extends Component
{

    /**
     * @param array $research
     */
    public function __construct(public array $research)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.research-m-l-t');
    }
}
