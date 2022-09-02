<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class termsOfUse extends Component
{
    /**
     * @param string $path
     */
    public function __construct(public string $path)
    {
    }
    /**
     * @return View
     */
    public function render(): View
    {
        return view('components.terms-of-use');
    }
}
