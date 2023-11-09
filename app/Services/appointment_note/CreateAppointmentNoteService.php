<?php

namespace App\Services\appointment_note;

use App\DTO\CreateAppointmentNoteDTO;
use App\Models\AppointmentNote;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateAppointmentNoteService
{
    public function __construct(
        private readonly CreateAppointmentNoteDTO $createAppointmentNoteDTO
    )
    {
    }

    public function execute() {
        try {
            DB::beginTransaction();

            $note = new AppointmentNote([
                'notes' => $this->createAppointmentNoteDTO->notes,
                'evolution' => $this->createAppointmentNoteDTO->evolution,
                'appointment_id' => $this->createAppointmentNoteDTO->appointment_id,
            ]);

            $note->save();

            if (isset($this->createAppointmentNoteDTO->files)) {
                /** @var UploadedFile $file */
                foreach ($this->createAppointmentNoteDTO->files as $file) {
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
