<?php

namespace App\DTO;

use App\Traits\DTOGenerator;
use Illuminate\Http\UploadedFile;

/**
 * @property UploadedFile[] $files
 */
readonly class EditAppointmentNoteDTO
{
    use DTOGenerator;

    public function __construct
    (
        public int $id,
        public string $notes,
        public string $evolution,
        public int $appointment_id,
        public mixed $files,
        public mixed $filesToRemove
    )
    {
    }
}
