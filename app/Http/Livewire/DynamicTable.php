<?php

namespace App\Http\Livewire;
use App\DTO\DynamicTableDTO;
use App\Models\User;
use App\Traits\HasDynamicTable;
use Livewire\Component;

class DynamicTable extends Component {
    protected $listeners = ['search' => 'filterRegisters'];

    /** @var DynamicTableDTO[] $tableColumns */
    public $tableColumns;

    public string $modelClass;
    public string $search;
    public $registers;
    public function render()
    {
        $this->tableColumns = $this->modelClass::getDtoColumnDefinitions();
        if (!in_array(HasDynamicTable::class, class_uses_recursive($this->modelClass))) {
            throw new \Exception("The specified model class: {$this->modelClass}, needs to be using the HasDynamicTable trait");
        }
        $this->registers = $this->modelClass::all();
        return view("livewire.crud.components.dynamic-table");
    }

    public function delete(int $id)
    {
        $register = $this->modelClass::find($id);
        $register->delete();
        $this->registers = $this->modelClass::all();
    }

    public function filterRegisters(string $searchString) {
        $this->modelClass::filterBySearchString($searchString);
        dd($searchString);
    }
}
