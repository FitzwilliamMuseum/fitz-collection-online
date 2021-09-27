<?php

namespace App\View\Components;

use Illuminate\View\Component;

class fmeProducts extends Component
{
    public $shopify;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($shopify)
    {
      $this->shopify  = $shopify;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.fme-products');
    }
}
