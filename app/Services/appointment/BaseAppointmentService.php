<?php

namespace App\Services\Appointment;

use App\Models\Appointment;

abstract class BaseAppointmentService
{
    protected array $appointmentData;
    const MAX_WEEKLY_APPOINTMENTS = Appointment::MAX_WEEKLY_APPOINTMENTS;
    const MAX_MONTHLY_APPOINTMENTS = Appointment::MAX_MONTHLY_APPOINTMENTS;
    const MAX_BIWEEKLY_APPOINTMENTS = Appointment::MAX_BIWEEKLY_APPOINTMENTS;

    abstract public function execute(): void;

    public function withAppointmentData(array $appointmentData): BaseAppointmentService
    {
        $this->appointmentData = $appointmentData;
        return $this;
    }

    protected function validateAppointmentData(): void
    {
        if (empty($this->appointmentData)) {
            throw new \Exception("Please provide appointment data.");
        }
    }
}
