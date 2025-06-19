<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public array $options = [],
        public mixed $value = null,
        public bool $multiple = false,
        public bool $required = false,
        public string $label = "",
        public ?string $id = null,
        public ?string $placeholder = null,

    ) {
        // Nếu $id không truyền vào thì tự gán = $name
        $this->id = $id ?: $name;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select');
    }
}
