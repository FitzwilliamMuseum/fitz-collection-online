<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class searchResult extends Component
{
    /** @var array */
    public array $record;

    /** @var array */
    public array $pris;

    /**
     * @param array $record
     */
    public function __construct(array $record)
    {
        $this->record = $record;
        $pris = Arr::pluck($record['_source']['identifier'], 'priref');
        $pris = array_filter($pris);
        $this->pris = Arr::flatten($pris);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.search-result');
    }
}
