<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IconController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $perPage = 100;
        $filter = $request->query('filter', '');

        $registers =  Icon::getAll()
            ->when(!empty($filter), function ($query) use ($filter) {
                return $query->filter(function ($item) use ($filter) {
                    return Str::contains($item, $filter);
                });
            })
            ->skip(($page - 1) * $perPage)
            ->take($perPage);

        return response()->json([
            'data' => $registers,
            'page' => $page,
            'perPage' => $perPage,
            'total' => Icon::getAll()->count(),
            'hasMore' => $registers->count() === $perPage,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
