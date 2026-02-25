<?php

// resources/views/components/select-input.blade.php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectInput extends Component
{
    public $options;

    public $model;

    public $style_css;

    public function __construct($model, $options, $style_css = 'line-height: 1.5 !important; margin-top: 10px !important; width: 300px !important;')
    {
        $this->model = $model;
        $this->options = $options;
        $this->style_css = $style_css;
    }

    public function render()
    {
        return view('components.select-input');
    }
}
