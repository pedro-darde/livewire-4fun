<?php

namespace App\DTO;

use App\Traits\DTOGenerator;
use Illuminate\Http\UploadedFile;

class CreateAppointmentNoteDTO
{
    use DTOGenerator;
    public function __construct(
        public readonly string $notes,
        public readonly string $evolution,
        public readonly int $appointment_id,
        public readonly mixed $files,
    )
    {

    }
}
