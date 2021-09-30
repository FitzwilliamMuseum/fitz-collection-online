<?php

namespace App\View\Components;

use Illuminate\View\Component;

use ColorThief\ColorThief;

class imagecolours extends Component
{
    public $path;

    public $palette;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($path, $palette = NULL)
    {
        $this->path = $path;
        $this->palette = $this->getColours($path);
    }

    public function getColours($path){
      return ColorThief::getPalette($path, 12);
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
