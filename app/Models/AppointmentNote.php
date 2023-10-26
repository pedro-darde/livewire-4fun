<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentNote extends Model
{
    use HasFactory;

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
