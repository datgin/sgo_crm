<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Date extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public bool $required = false,
        public ?string $label = null,
        public ?string $value = '',
        public ?string $placeholder = null,
        public bool $single = true, // true = chọn 1 ngày, false = khoảng
        public ?string $id = "",
    ) {
        $this->id = $id ?: $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.date');
    }
}
