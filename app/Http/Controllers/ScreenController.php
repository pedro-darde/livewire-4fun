<?php

namespace App\Http\Controllers;

use App\Models\Screen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Concerns\ValidatesAttributes;
use Inertia\Inertia;
use ReflectionClass;
use ReflectionMethod;

class ScreenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $validatorClass = new ReflectionClass(ValidatesAttributes::class);
        $defaultRules = collect($validatorClass->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(function($method){
                return Str::startsWith($method->name,'validate');
            })
            ->map(function(ReflectionMethod $method){
                return [
                    'name' => str_replace('validate_', '', Str::snake($method->name)),
                    'about' => $method->getDocComment(),
                    'class' => $method->class,
                ];
        })->values();

       return Inertia::render('ScreenGenerator', [
           'screens' => Screen::with('fields')->get(),
           'defaultRules' => $defaultRules->all(),
       ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'screen.config' => 'required',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Screen $screen)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Screen $screen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Screen $screen)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Screen $screen)
    {
        //
    }
}
