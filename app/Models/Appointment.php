<?php

namespace App\Models;

use App\Enums\RecurrenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
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
 * @method static \Illuminate\Database\Eloquent\Builder|Appointment orderByDesc(string $field)
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
        'onlyHourEnd',
        'title',
        'recurrence_type'
    ];

    const MAX_WEEKLY_APPOINTMENTS = 8;
    const MAX_MONTHLY_APPOINTMENTS = 6;
    const MAX_BIWEEKLY_APPOINTMENTS = 28;

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

    public function note(): HasOne
    {
        return $this->hasOne(AppointmentNote::class, 'appointment_id');
    }

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

    public function scopeTodayAppointments(Builder $query): Builder
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

    public function getTitleAttribute()
    {
        return 'Consulta com ' . $this->patientsNames . ' as ' . $this->onlyHourStart;
    }
//  {
//    text: "Não",
//    value: RecurrenceType.NONE
//  },
//  {
//    text: "Sim, semanalmente.",
//    value: RecurrenceType.WEEKLY,
//    shortDesc: "Semanalmente"
//  },
//  {
//    text: "Sim, quinzenalmente.",
//    value: RecurrenceType.BIWEEKLY,
//    shortDesc: "Quinzenalmente"
//  },
//  {
//    text: "Sim, mensalmente.",
//    value: RecurrenceType.MONTHLY,
//    shortDesc: "Mensalmente"
//  }
    public function getRecurrenceTypeAttribute() {
        return [
            'text' => RecurrenceType::getText($this->attributes['recurrence_type']),
            'value' => $this->attributes['recurrence_type'],
            'shortDesc' => RecurrenceType::getAbreviation($this->attributes['recurrence_type'])
        ];
    }
}
