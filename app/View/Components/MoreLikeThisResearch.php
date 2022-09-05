<?php

namespace App\View\Components;

use App\Models\FindMoreLikeThis;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Psr\SimpleCache\InvalidArgumentException;
use Solarium\Core\Query\DocumentInterface;

class MoreLikeThisResearch extends Component
{
    /**
     * @var array|DocumentInterface[]
     */
    public array $research;

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(public array $data)
    {
        if (array_key_exists('title', $data)) {
            $query = $data['title'][0]['value'];
        } else {
            $query = $data['summary_title'];
        }
        $this->research = FindMoreLikeThis::find($query, '*');
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.more-like-this-research');
    }
}
