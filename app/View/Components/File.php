<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class File extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $accept = 'image/*',
        public bool $multiple = false,
        public ?string $value = "",
        public string $width = "150px",
        public string $height = "200px",
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.file');
    }
}
