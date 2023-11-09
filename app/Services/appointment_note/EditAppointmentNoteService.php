<?php

namespace App\Services\appointment_note;

use App\DTO\editAppointmentNoteDTO;
use App\Models\AppointmentNote;
use App\Models\AppointmentNotesFiles;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EditAppointmentNoteService
{
    public function __construct(
        private readonly EditAppointmentNoteDTO $editAppointmentNoteDTO
    )
    {
    }

    public function execute() {
        try {
            DB::beginTransaction();
            $note = AppointmentNote::find($this->editAppointmentNoteDTO->id);
            $note->fill([
                'notes' => $this->editAppointmentNoteDTO->notes,
                'evolution' => $this->editAppointmentNoteDTO->evolution,
                'appointment_id' => $this->editAppointmentNoteDTO->appointment_id,
                'updated_at' => now()->setTimeZone('America/Sao_Paulo')->format('Y-m-d H:i:s')
            ]);
            $note->save();

            if (count($this->editAppointmentNoteDTO->filesToRemove ?? [])) {
                $files = AppointmentNotesFiles::whereIn('id', $this->editAppointmentNoteDTO->filesToRemove)->get();
                $files->each->delete();
            }

            if (count($this->editAppointmentNoteDTO->files ?? [])) {
                /** @var UploadedFile $file */
                foreach ($this->editAppointmentNoteDTO->files as $file) {
                    $pathFile = AppointmentNote::DIR_PATH . "/{$note->id}";
                    $file->storeAs($pathFile, $file->getClientOriginalName(), 'local');
                    $note->files()->create([
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => "{$pathFile}/{$file->getClientOriginalName()}",
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                        'note_id' => $note->id,
                    ]);
                }
            }

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
