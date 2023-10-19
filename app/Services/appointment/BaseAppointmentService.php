<?php

namespace App\Services\Appointment;

abstract class BaseAppointmentService
{
    protected array $appointmentData;
    const MAX_WEEKLY_APPOINTMENTS = 8;
    const MAX_MONTHLY_APPOINTMENTS = 6;
    const MAX_BIWEEKLY_APPOINTMENTS = 28;

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
