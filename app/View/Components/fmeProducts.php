<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class fmeProducts extends Component
{
    /**
     * @var array
     */
    public array $shopify;

    /**
     * @param array $shopify
     */
    public function __construct(array $shopify)
    {
      $this->shopify  = $shopify;
    }

    /**
     * Get the view / contents that represent the component.
     * @return View
     */
    public function render(): View
    {
        return view('components.fme-products');
    }
}
