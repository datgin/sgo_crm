<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Operation extends Component
{
    public bool $edit;
    public bool $delete;
    public bool $view;
    public object $row;

    public function __construct(object $row, bool $edit = true, bool $delete = true, bool $view = false)
    {
        $this->edit = $edit;
        $this->delete = $delete;
        $this->view = $view;
        $this->row = $row;
    }

    public function render(): View|Closure|string
    {
        return view('components.operation');
    }
}
