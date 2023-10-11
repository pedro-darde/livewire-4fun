<?php

namespace App\Http\Controllers;

use App\Errors\TableNotFound;
use App\Models\DynamicModel;

class SelectController extends Controller
{
    public function dynamicItems(\Illuminate\Http\Request $request, string $tableSearch)
    {
        $data = $request->get('data');
        $items = DynamicModel::getItemsToList($tableSearch, $data['optionText'], $data['optionValue']);

        return response()->json([
            'items' => $items,
        ], 200);
    }
}
