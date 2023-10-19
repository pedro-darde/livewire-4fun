<?php

namespace App\Models;

use App\Casts\Date;
use App\Casts\DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $start
 * @property string $end
 * @property string $about
 * @property string $status
 * @property bool $is_recurrence
 * @property int $recurrence_type
 * @property int $id_service_supplied
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment find(int $id)
 */

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = ["id"];
    protected $table = 'appointment';
    protected $appends = [
        'creatorName',
        'startParsed',
        'endParsed',
        'patientsNames',
        'onlyHourStart',
        'onlyHourEnd'
    ];

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function patients()
    {
        return $this->hasManyThrough(
            Patient::class,
            AppointmentPatient::class,
            'appointment_id',
            'id',
            'id',
            'patient_id'
        );
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::created(function (Appointment $appointment) {
    //         // todo create appointment notifications
    //     });
    // }

    public function getStartParsedAttribute()
    {
        return date('d/m/Y H:i', strtotime($this->start));
    }

    public function getEndParsedAttribute()
    {
        return date('d/m/Y H:i', strtotime($this->end));
    }

    public function getCreatorNameAttribute()
    {
        if (isset($this->creator)) {
            return $this->creator->name;
        }
        return $this->creator()->name;
    }

    public function getOnlyHourStartAttribute()
    {
        return date('H:i', strtotime($this->start));
    }

    public function getOnlyHourEndAttribute()
    {
        return date('H:i', strtotime($this->end));
    }

    public function scopeTodayAppointments($query)
    {
        return $query->whereRaw("date(start) = date(now())");
    }

    public function getPatientsNamesAttribute()
    {
        if (isset($this->patients)) {
            return $this->patients->map(fn ($patient) => $patient->name . ' ' . $patient->last_name)->join(', ');
        }
        return $this->patients()->get()->map(fn ($patient) => $patient->name . ' ' . $patient->last_name)->join(', ');
    }
}
