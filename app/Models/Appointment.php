<?php

namespace App\Models;

use App\Casts\Date;
use App\Casts\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = ["id"];
    protected $table = 'appointment';
    protected $appends = ['creatorName'];

    protected $casts = [
        'start' => DateTime::class,
        'end' => DateTime::class,
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
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (Appointment $appointment) {
            // todo create appointment notifications
        });
    }

    public function getCreatorNameAttribute()
    {
        if (isset($this->creator)) {
            return $this->creator->name;
        }
        return $this->creator()->name;
    }
}
