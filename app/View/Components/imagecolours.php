<?php

namespace App\View\Components;

use Illuminate\View\Component;

use ColorThief\ColorThief;

class imagecolours extends Component
{
    public $path;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getColours($path){
      return ;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $colours = ColorThief::getPalette( $this->path, 12 );
        return view('components.imagecolours', compact('colours'));
    }
}
