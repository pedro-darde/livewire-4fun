<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @method static AppointmentNote find(int $id)
 */

class AppointmentNote extends Model
{
    use HasFactory;

    const DIR_PATH = 'public/uploads/notes_files';
    protected $guarded = ['id'];

    protected $table = 'appointment_note';

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(AppointmentNotesFiles::class, 'appointment_note_id');
    }
}
