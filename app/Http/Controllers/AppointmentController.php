<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Appointments/AppointmentsListPage',
            [
                'appointments' => Appointment::paginate(10),
//                'patients' =>
            ]
        );
    }

    public function create()
    {
        return Inertia::render('Appointments/AppointmentsCreatePage');
    }

    public function loadMore(Request $request)
    {
        $searchString = $request->query('search');
        $perPage = $request->query('per_page') == -1 ? 0 : $request->query('per_page');
        $orderBy = $request->query("order_by") ?? "id";
        $direction = $request->query("order_direction") ?? "asc";

        $appointments = Appointment::query()
            ->when(isset($perPage) && $perPage > 0,
                fn($query) => $query
//                    ->whereRaw("(name like '%$searchString%' OR last_name LIKE '%$searchString%' or cpf LIKE '%$searchString%' OR ('$searchString' = ''))")
                    ->orderBy($orderBy, $direction)
                    ->paginate($perPage),
                fn($query) => $query
//                    ->whereRaw("(name like '%$searchString%' OR last_name LIKE '%$searchString%' or cpf LIKE '%$searchString%' OR ($searchString = $searchString))")
                    ->orderBy($orderBy, $direction)
                    ->get()
            );
        return response()->json([
            'appointments' => $appointments
        ]);
    }
}
