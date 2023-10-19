<?php

namespace App\Http\Controllers;

use App\Services\screen\DynamicSaver;
use Illuminate\Http\Request;

class DynamicScreenController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->all();
        $table = $data['table'];
        $register = $data['register'];
        DynamicSaver::save($table, $register);

        response()->json([
            'message' => 'Registro salvo com sucesso!'
        ]);
    }
}
