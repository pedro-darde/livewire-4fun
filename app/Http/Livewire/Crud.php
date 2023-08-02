<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;

class Crud extends Component
{
    public string $modelClass;
    public array $actionRoutes;
    public string $controllerName;

    public function render() {
        $this->extractRoutes();
        return view("livewire.crud.crud");
    }

    private function extractRoutes()
    {
        $appRoutes = Route::getRoutes()->getRoutes();

        $onlyModelName = explode("\\", $this->modelClass);
        $onlyModelName = end($onlyModelName);

        $controllerName = $this->controllerName ?? strtolower($onlyModelName) . "s";

        $this->actionRoutes = array_filter($appRoutes, function ($item) use($controllerName) {
            return Str::startsWith($item->uri(), $controllerName);
        });
    }
}
