<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Media extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $name, public bool $multiple = true, public array $selected = [])
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.media');
    }
}
