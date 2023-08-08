<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class InputSearch extends Component
{
    public string $value;

    public function render()
    {
        return view("livewire.input-search");
    }

    public function search() {
        $this->emitTo("dynamic-table", "changeSearch", $this->value);
    }
}
