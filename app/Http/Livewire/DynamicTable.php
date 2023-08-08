<?php

namespace App\Http\Livewire;
use App\Constants\Order;
use App\DTO\DynamicTableDTO;
use App\Traits\HasDynamicTable;
use App\Traits\WithDynamicTable;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\WithPagination;

class DynamicTable extends Component {

    use WithPagination;

    public array $orderDirectionOptions = [];

    protected $listeners = ['changeSearch' => 'updateSearch'];

    /** @var DynamicTableDTO[] $tableColumns */
    public $tableColumns;

    /** @var WithDynamicTable  $modelClass  */
    public $modelClass;
    public string $search = '';

    public array $orderBy = [];
    public array $orderByOptions = [];
    public ?string $orderByDirection = 'asc';
    public $instanceModel;
    public function getRegistersProperty() {
        return $this->modelClass::filterBySearchString($this->search, $this->orderBy, $this->orderByDirection ?? 'asc')->paginate(8);
    }

    public function mount()
    {
        $this->tableColumns = $this->modelClass::getDtoColumnDefinitions();
        $this->orderByOptions = Arr::map($this->tableColumns, fn ($item) => ['name' => $item->getColumnDescription(), 'value' => $item->getColumnName()]);
        $this->orderBy = [Arr::first($this->orderByOptions)['value']];
        $this->search = '';
        $this->orderDirectionOptions =  Order::getAsList();
    }

    public function hydrate() {
        $this->tableColumns = $this->modelClass::getDtoColumnDefinitions();
        $this->orderByOptions = Arr::map($this->tableColumns, fn ($item) => ['name' => $item->getColumnDescription(), 'value' => $item->getColumnName()]);
    }
    public function render()
    {
        if (!in_array(HasDynamicTable::class, class_uses_recursive($this->modelClass))) {
            throw new \Exception("The specified model class: {$this->modelClass}, needs to be using the HasDynamicTable trait");
        }

        return view("livewire.crud.components.dynamic-table");
    }

    public function delete(int $id)
    {
        $register = $this->modelClass::find($id);
        $register->delete();
    }

    public function emitEdit(int $id) {
        $this->emitTo('crud', 'onEditClick', $this->modelClass::find($id));
    }

    public function updateSearch(string $value) {
        $this->search = $value;
    }

    public function shouldShowColumnOnList(string $property) {
       return !in_array($property,(new $this->modelClass)->getHidden());
    }
}
