<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $id              = null,
        public $name            = null,
        public $class           = '',
        public $placeholder     = null,
        public $value           = '',
        public $disabled        = false,
        public $readonly        = false,
        public $label           = "",
        public $rows           = "",
        public $required      = false,
        public $maxLength = ""
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.textarea');
    }
}
