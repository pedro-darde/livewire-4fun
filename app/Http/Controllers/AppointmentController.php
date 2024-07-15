<?php

namespace App\Http\Controllers;

use App\Jobs\AfterAppointmentCreatedJob;
use App\Models\Appointment;
use App\Services\appointment\CreateAppointmentService;
use App\Services\appointment\EditAppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AppointmentController extends Controller
{
    private CreateAppointmentService $createAppointmentService;
    private EditAppointmentService $editAppointmentService;

    public function __construct()
    {
        $this->createAppointmentService = new CreateAppointmentService();
        $this->editAppointmentService = new EditAppointmentService();
    }
    public function index()
    {
        return Inertia::render(
            'Appointments/AppointmentsListPage',
            [
                'appointments' => Appointment::paginate(10),
            ]
        );
    }

    public function create()
    {
        return Inertia::render('Appointments/AppointmentsCreatePage');
    }

    public function loadMore(Request $request): JsonResponse
    {
        $searchString = $request->query('search');
        $perPage = $request->query('per_page') == -1 ? 0 : $request->query('per_page');
        $orderBy = $request->query("order_by") ?? "id";
        $direction = $request->query("order_direction") ?? "asc";

        $appointments = Appointment::query()
            ->when(
                isset($perPage) && $perPage > 0,
                fn ($query) => $query
                    //                    ->whereRaw("(name like '%$searchString%' OR last_name LIKE '%$searchString%' or cpf LIKE '%$searchString%' OR ('$searchString' = ''))")
                    ->orderBy($orderBy, $direction)
                    ->paginate($perPage),
                fn ($query) => $query
                    //                    ->whereRaw("(name like '%$searchString%' OR last_name LIKE '%$searchString%' or cpf LIKE '%$searchString%' OR ($searchString = $searchString))")
                    ->orderBy($orderBy, $direction)
                    ->get()
            );

        return response()->json([
            'appointments' => $appointments,
            'today' => Appointment::todayAppointments()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start' => 'required|date|before:end',
            'end'   => 'required|date|after:start',
            'about' => 'required|string',
            'patients' => 'required|array|min:1',
            'recurrence_type' => 'required',
            'recurrence_type.value' => 'required|in:0,1,2,3',
            'useCustomRecurrence' => 'nullable',
            'useCustomRecurrenceWeek' => 'nullable',
            'useDefaultRecurrence' => 'nullable',
            'useDefaultRecurrenceWeek' => 'nullable',
            'numberOfRecurrences' => 'required_if:useCustomRecurrence,true|integer|min:1',
            'id_service_supplied' => 'required|exists:service_supplied,id',
            'recurrenceWeeklyDays' => 'required_if:useCustomRecurrenceWeek,true|array|min:1',
        ]);

        $this->createAppointmentService
            ->withAppointmentData($validated)
            ->execute();

        return response()->json(['message' => "Consulta criada com sucesso!"]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'start' => 'required|date|before:end',
            'end'   => 'required|date|after:start',
            'about' => 'required|string',
            'patients' => 'required|array|min:1',
            'id' => 'required|exists:appointment,id'
        ]);

        $this->editAppointmentService
            ->withAppointmentData($validated)
            ->execute();

        return response()->json(['message' => "Consulta atualizada com sucesso!"]);
    }

    public function show(Appointment $appointment): InertiaResponse
    {
        return Inertia::render('Appointments/AppointmentsEditPage', [
            'appointment' => $appointment
        ]);
    }

    public function changeStatus(Appointment $appointment,Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,finished'
        ]);

        $appointment->status = $validated['status'];
        $appointment->save();

        return response()->json(['message' => "Status da consulta atualizado com sucesso!"]);
    }


    public function test()
    {
        $appointments = Appointment::all();
        foreach ($appointments as $appointment) {
            // AfterAppointmentCreatedJob::dispatchSync($appointment);
        }
    }
}
