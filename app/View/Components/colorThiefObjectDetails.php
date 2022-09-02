<?php

namespace App\View\Components;

use ColorThief\ColorThief;
use Illuminate\View\Component;

class colorThiefObjectDetails extends Component
{
    public array $palette;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public array $data)
    {
        if (array_key_exists('multimedia', $data)) {
            if (array_key_exists('large', $data['multimedia'][0]['processed'])) {
                $image = $data['multimedia'][0]['processed']['large']['location'];
                $path = env('CIIM_IMAGE_URL') . $image;
                $this->palette = ColorThief::getPalette($path, 12);
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.color-thief-object-details');
    }
}
