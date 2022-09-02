<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class exhibitionDetails extends Component
{
    /**
     * @param array $exhibition
     */
    public function __construct(public array $exhibition)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.exhibition-details');
    }
}
