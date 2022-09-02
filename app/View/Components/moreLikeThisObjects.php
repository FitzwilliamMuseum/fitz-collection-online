<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\MltObjects;

class moreLikeThisObjects extends Component
{
    public array $mlt;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public array $data)
    {
        $this->mlt = MltObjects::findMoreLikeThis($data, 'object');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.more-like-this-objects');
    }
}
