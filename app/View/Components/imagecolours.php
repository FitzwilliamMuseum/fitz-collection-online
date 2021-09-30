<?php

namespace App\View\Components;

use Illuminate\View\Component;

use ColorThief\ColorThief;

class imagecolours extends Component
{
    public $palette;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($palette)
    {
        $this->palette = $palette;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.imagecolours');
    }
}
