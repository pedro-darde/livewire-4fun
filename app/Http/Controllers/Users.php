<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class Users extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
       return Inertia::render('Crud', [
           'table' => User::TABLE,
           'columnDefinitions' => User::getDtoColumnDefinitions(),
           'registers' => User::filterBySearchString('')->paginate(20),
           'modelProps' => [
               'crudActions' => [
                     'edit' => true,
                     'delete' => true
                ]
           ]
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
      $validator =  Validator::make($request->all(),User::getOnlyRules());
      $data = $validator->validate();
      $user = new User;
      $user->fill($data);
      $user->save();
      response()->json(['message' => 'Usuário criado com sucesso!'], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
       $user->fill($request->all());
       $user->save();
       response()->json(['message' => 'Usuário atualizado com sucesso!'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        response()->json(['message' => 'Usuário deletado com sucesso!'], Response::HTTP_OK);
    }
}
