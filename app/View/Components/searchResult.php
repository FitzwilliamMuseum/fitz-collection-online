<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class searchResult extends Component
{

    /*
     * @var array
     */
    public array $priref;
    /**
     * @var array
     */
    public array $accession;
    /**
     * @var array|mixed
     */
    public array $makers;

    public function __construct(public array $record)
    {
        $this->priref = Arr::flatten(array_filter(Arr::pluck($record['_source']['identifier'], 'priref')));
        $this->accession = Arr::flatten(array_filter(Arr::pluck($record['_source']['identifier'], 'accession_number')));
        $this->makers = $record['_source']['lifecycle']['creation'][0]['maker'] ?? [];
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
