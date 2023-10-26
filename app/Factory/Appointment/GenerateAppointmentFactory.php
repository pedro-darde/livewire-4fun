<?php

namespace App\Factory\Appointment;

use App\Enums\RecurrenceType;
use App\Factory\Generators\Appointment\BiweeklyMonthlyGenerator;
use App\Factory\Generators\Appointment\WeeklyAppointmentGenerator;
use App\Interfaces\AppointmentsGenerated;

abstract class GenerateAppointmentFactory
{
    static function createFor(RecurrenceType $type, array $data): AppointmentsGenerated
    {
        if ($type->value === RecurrenceType::weekly()) {
            return new WeeklyAppointmentGenerator($data);
        }

        if ($type->value === RecurrenceType::biweekly() || $type->value === RecurrenceType::monthly()) {
            return new BiweeklyMonthlyGenerator($data);
        }

        throw new \Exception("Invalid recurrence type.");

    }
}
