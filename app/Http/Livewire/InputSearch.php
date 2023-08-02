<?php

namespace App\Http\Livewire;

use Livewire\Component;

class InputSearch extends Component
{
    public string $value;

    public function render()
    {
        return view("livewire.input-search");
    }

    public function search() {
        $this->emit("search", $this->value);
    }
}
