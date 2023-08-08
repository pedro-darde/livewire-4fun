<?php

namespace App\Http\Livewire;

use App\Traits\WithDynamicTable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;

class Crud extends Component
{
    protected $listeners = ['onEditClick' => 'toggleModalAddEdit'];

    /** @var WithDynamicTable $modelClass */
    public  $modelClass;
    public array $actionRoutes;
    public string $controllerName;
    public bool $modalAddEdit = false;
    public string $routineTitle;
    public  $onlyModelName;

    public $modalAddEditTitle = '';

    public string $modelLower;

    public function mount()
    {
        $this->createProps();
        $this->extractRoutes();
    }

    public function render() {
        return view("livewire.crud.crud");
    }

    public function toggleModalAddEdit($register = null)
    {
        // already editing, so return
        if ($this->modalAddEdit) return;
        $this->{$this->modelLower} = $this->modelClass::find($register) ?? new $this->modelClass;

        if (!$register)  {
            $this->modalAddEditTitle = 'Add ' . $this->routineTitle;
        } else {
            $this->modalAddEditTitle = 'Edit ' . $this->routineTitle . ' #'  . $this->{$this->modelLower}->id;
        }
        $this->modalAddEdit = true;

    }

    public function closeModalAddEdit()
    {
        $this->modalAddEdit = false;
        $this->{$this->modelLower} = null;
        $this->resetValidation();
    }

    public function saveEdit() {
        $this->validate();
        if (!$this->{$this->modelLower}->id) {
            $this->modelClass::create(
                $this->{$this->modelLower}->getAttributes()
            );
        } else {
            $this->{$this->modelLower}->save();
        }
        $this->closeModalAddEdit();
        $this->dispatchBrowserEvent('registerSaved');
    }

    private function extractRoutes()
    {
        $appRoutes = Route::getRoutes()->getRoutes();

        $this->actionRoutes = array_filter($appRoutes, function ($item)  {
            return Str::startsWith($item->uri(), $this->controllerName);
        });
    }

    protected function rules()
    {
       $rulesArr = [];
       foreach($this->modelClass::getDtoColumnDefinitions() as $dtoDefinition) {
           $ruleKey = "$this->modelLower." . $dtoDefinition->getColumnName();
           $rulesArr[$ruleKey] = '';

           // if the rule is callable so pass the current item to it.
           if (is_callable($dtoDefinition->getColumnRules())) {
               $ruleValue = $dtoDefinition->getColumnRules()($this->{$this->modelLower});
           } else {
               $ruleValue = $dtoDefinition->getColumnRules();
           }
           $rulesArr[$ruleKey] = $ruleValue;
       }
       return $rulesArr;
    }

    private function createProps()
    {
        $onlyModelName = explode("\\", $this->modelClass);
        $this->onlyModelName = end($onlyModelName);
        $this->controllerName ??= Str::lower($this->onlyModelName) . "s";
        $this->modelLower = Str::lower($this->onlyModelName);
        $this->{$this->modelLower} = null;
    }
}
