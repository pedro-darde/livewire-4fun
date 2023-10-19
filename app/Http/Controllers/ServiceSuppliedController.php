<?php

namespace App\Http\Controllers;

use App\Models\ServiceSupplied;
use Illuminate\Http\Request;

class ServiceSuppliedController extends Controller
{
    public function getAll()
    {
        $serviceSupplied = ServiceSupplied::all();
        return response()->json($serviceSupplied);
    }
}
