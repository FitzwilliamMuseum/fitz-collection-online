<?php

namespace App\View\Components;

use Illuminate\View\Component;

class researchMLT extends Component
{
    public $research;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($research)
    {
        $this->research = $research;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.research-m-l-t');
    }
}
