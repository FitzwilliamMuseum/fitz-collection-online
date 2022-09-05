<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\FindMoreLikeThis;
use Psr\SimpleCache\InvalidArgumentException;

class MoreLikeThisShopify extends Component
{
    public array $shopify;

    /**
     * Create a new component instance.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function __construct(public array $data)
    {
        if (array_key_exists('title', $data)) {
            $query = $data['title'][0]['value'];
        } else {
            $query = $data['summary_title'];
        }
        $this->shopify = FindMoreLikeThis::find($query, 'shopify');

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.more-like-this-shopify');
    }
}
