<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $id              = null,
        public $name            = null,
        public $type            = 'text',
        public $class           = '',
        public $placeholder     = null,
        public $value           = '',
        public $disabled        = false,
        public $readonly        = false,
        public $label           = "",
        public $required      = false
    ) {
        $this->id = $id ?: $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input');
    }
}
