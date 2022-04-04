<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class publicationDetails extends Component
{
    public array $publication;

    public array $count;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $publication, array $count)
    {
       $this->publication = $publication;
       $this->count = $count;
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
