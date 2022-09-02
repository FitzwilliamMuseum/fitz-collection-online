<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class publicationDetails extends Component
{

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public array $publication, public int $count)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.publication-details');
    }
}
