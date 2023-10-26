<?php

namespace App\Http\Controllers;

use App\Jobs\TestJob;
use App\Models\Patient;
use App\Rules\CPF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PatientController extends Controller
{
    public function index(): InertiaResponse
    {
        $patients = Patient::paginate(10);
        return Inertia::render('Patients/PatientsListPage', [
            'patients' => $patients
        ]);
    }

    public function loadMore(Request $request): JsonResponse
    {
        $searchString = $request->query('search');
        $perPage = $request->query('per_page') == -1 ? 0 : $request->query('per_page');
        $orderBy = $request->query("order_by") ?? "id";
        $direction = $request->query("order_direction") ?? "asc";

        $patients = Patient::query()
            ->when(
                isset($perPage) && $perPage > 0,
                fn ($query) => $query
                    ->whereRaw("(name like '%$searchString%' OR last_name LIKE '%$searchString%' or cpf LIKE '%$searchString%' OR ('$searchString' = ''))")
                    ->orderBy($orderBy, $direction)
                    ->paginate($perPage),
                fn ($query) => $query
                    ->whereRaw("(name like '%$searchString%' OR last_name LIKE '%$searchString%' or cpf LIKE '%$searchString%' OR ($searchString = $searchString))")
                    ->orderBy($orderBy, $direction)
                    ->get()
            );
        return response()->json([
            'patients' => $patients,
        ]);
    }

    public function destroy(Patient $patient): JsonResponse
    {
        $patient->appointmentsPatients()->delete();
        $patient->delete();
        return response()->json([
            'message' => 'Paciente excluído com sucesso!',
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Patients/PatientsCreatePage');
    }

    public function show(Patient $patient): InertiaResponse
    {
        $patient->load(['appointments','appointments.note']);
        return Inertia::render('Patients/PatientsEditPage', [
            'patient' => $patient
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rg' => 'nullable|string',
            'cpf' => ['string', new CPF],
            'name' => 'string|required',
            'last_name' => 'string|required',
            'birth_date' => 'date|required',
            'phone' => 'string|required',
            'nickname' => 'nullable|string',
            'email' => 'string|email',
        ]);

        $patient = new Patient;
        $patient->fill($validated);

        $patient->save();

        return response()->json([
            'message' => 'Paciente cadastrado com sucesso!',
        ]);
    }

    public function update(Patient $patient, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rg' => 'nullable|string',
            'cpf' => ['string', new CPF],
            'name' => 'string|required',
            'last_name' => 'string|required',
            'birth_date' => 'date|required',
            'phone' => 'string|required',
            'nickname' => 'nullable|string',
            'email' => 'string|email',
        ]);

        $patient->fill($validated);

        $patient->save();

        return response()->json([
            'message' => 'Paciente editado com sucesso!',
        ]);
    }
}
