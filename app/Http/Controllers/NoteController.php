<?php

namespace App\Http\Controllers;

use App\DTO\CreateAppointmentNoteDTO;
use App\DTO\EditAppointmentNoteDTO;
use App\Models\Appointment;
use App\Models\AppointmentNote;
use App\Services\appointment\CreateAppointmentService;
use App\Services\appointment_note\CreateAppointmentNoteService;
use App\Services\appointment_note\EditAppointmentNoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{

    public function store(Appointment $appointment,Request $request)
    {
        $validatedNoteData = $request->validate([
           'notes' => 'required|string',
            'evolution' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file',
            'appointment_id' => 'required|exists:appointment,id'
        ]);


        $dataDTO = CreateAppointmentNoteDTO::createNewInstance($validatedNoteData);

        $service = new CreateAppointmentNoteService($dataDTO);
        $service->execute();

        return response()->json([
            'message' => 'Nota criada com sucesso!'
        ]);

    }

    public function update(Appointment $appointment, AppointmentNote $note, Request $request) {
        $validatedNoteData = $request->validate([
            'notes' => 'required|string',
            'evolution' => 'required|string',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file',
            'appointment_id' => 'required|exists:appointment,id',
            'id' => 'required|exists:appointment_note,id',
            'filesToRemove' => 'nullable|array',
            'filesToRemove.*' => 'nullable|exists:appointment_notes_files,id'
        ]);

        $dataDTO = EditAppointmentNoteDTO::createNewInstance($validatedNoteData);

        $service = new EditAppointmentNoteService($dataDTO);
        $service->execute();

        return response()->json([
            'message' => 'Nota atualizada com sucesso!'
        ]);
    }
}
