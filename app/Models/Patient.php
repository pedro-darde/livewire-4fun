<?php

namespace App\Models;

use App\Casts\Date;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $guarded = ["id"];
    protected $table = 'patient';
    protected $casts = [
        'birth_date' => Date::class,
    ];

    public function appointments()
    {

        return $this->hasManyThrough(
            Appointment::class,
            AppointmentPatient::class,
            'patient_id',
            'id',
            'id',
            'appointment_id'
        )->orderBy('start', 'asc');
    }

    public function appointmentsPatients()
    {
        return $this->hasMany(AppointmentPatient::class);
    }
}
