<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        if (session('fillDocumentation')) {
            return view('layouts.appSimple');
        }
        return view('layouts.app');
    }
}
