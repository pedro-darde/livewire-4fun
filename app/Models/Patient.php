<?php

namespace App\Models;

use App\Casts\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Patient extends Model
{
    use HasFactory;

    protected $guarded = ["id"];
    protected $table = 'patient';
    protected $casts = [
        'birth_date' => Date::class,
    ];

    public function appointments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Appointment::class,
            AppointmentPatient::class,
            'patient_id',
            'id',
            'id',
            'appointment_id'
        );
    }

    public function appointmentsPatients(): HasMany
    {
        return $this->hasMany(AppointmentPatient::class);
    }
}
