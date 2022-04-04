<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class exhibitionDetails extends Component
{
    public array $exhibition;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $exhibition)
    {
        $this->exhibition = $exhibition;
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
