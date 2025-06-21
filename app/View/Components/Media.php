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
    public function __construct(
        public string $name,
        public bool $multiple = false,
        public array|string|null $selected = null
    ) {
        // Đảm bảo $selected luôn là mảng sau khi khởi tạo
        if (is_string($this->selected)) {
            $this->selected = [$this->selected];
        } elseif (!is_array($this->selected)) {
            $this->selected = [];
        }
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.media');
    }
}
