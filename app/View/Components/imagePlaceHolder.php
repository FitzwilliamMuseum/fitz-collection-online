<?php

namespace App\View\Components;

use Illuminate\View\Component;

class imagePlaceHolder extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $path, public string $altText, public string $classes)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.image-place-holder');
    }
}
