<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SwitchCheckbox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public bool $checked = false, public ?string $id = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.switch-checkbox');
    }
}
