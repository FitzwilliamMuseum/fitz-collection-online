<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DisplayStatus extends Component
{
    public object $location;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(object $location)
    {
        $this->location = $location;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.display-status');
    }
}
