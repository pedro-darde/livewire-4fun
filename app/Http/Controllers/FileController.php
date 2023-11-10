<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function download(Request $request)
    {
        $path = $request->get("path");
        if (file_exists($path)) {
            return response()->download($path);
        }
        return response()->json(['message' => 'File not found.'], 404);
    }
}
