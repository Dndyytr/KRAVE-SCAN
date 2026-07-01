<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CustomerLayout extends Component
{
    /**
     * The branch name.
     */
    public ?string $branch;

    /**
     * The table number.
     */
    public ?string $table;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $branch = null,
        ?string $table = null,
        public bool $immersive = false,
    ) {
        $this->branch = $branch;
        $this->table = $table;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.customer');
    }
}
