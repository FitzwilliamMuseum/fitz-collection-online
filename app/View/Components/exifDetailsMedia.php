<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use PHPExif\Exif;
use PHPExif\Reader\Reader;

class exifDetailsMedia extends Component
{
    public Exif $exif;
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
                $reader = Reader::factory(Reader::TYPE_NATIVE);
                $this->exif = $reader->read($path);
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.exif-details-media');
    }
}
