<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DisplayStatus extends Component
{

    /**
     * @param object $location
     */
    public function __construct(public object $location)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.display-status');
    }
}
