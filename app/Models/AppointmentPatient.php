<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @method static create(array $array)
 */

class AppointmentPatient extends Model
{
    use HasFactory;

    protected $table = 'appointment_patient';
}
