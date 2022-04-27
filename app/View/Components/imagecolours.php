<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class imagecolours extends Component
{
    public array $palette;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(array $palette)
    {
        $this->palette = $palette;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.image-colours');
    }
}
