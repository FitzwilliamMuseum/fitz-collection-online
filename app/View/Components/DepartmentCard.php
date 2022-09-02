<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DepartmentCard extends Component
{
    public string $image;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public array $department)
    {
        $this->image = match ($department['key']) {
            'Coins and Medals' => 'https://content.fitz.ms/fitz-website/assets/Tray_in_coins_and_medals_storage.jpg?key=exhibition',
            'Antiquities' => 'https://content.fitz.ms/fitz-website/assets/GR_2_1891_1_201603_mfj22_dc1.jpeg?key=exhibition',
            'Paintings, Drawings and Prints' => 'https://content.fitz.ms/fitz-website/assets/arton1505-9d09e.jpeg?key=exhibition',
            'Applied Arts' => 'https://content.fitz.ms/fitz-website/assets/T.37-1938_car1.jpg?key=exhibition',
            'Manuscripts and Printed Books' => 'https://content.fitz.ms/fitz-website/assets/Hours, manusript MS_20152_20_2813v_2014r_29.jpg?key=exhibition',
            default => 'https://content.fitz.ms/fitz-website/assets/arton1534-1854b.jpeg?key=exhibition',
        };
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.department-card');
    }
}
