<?php

namespace App\Services\Appointment;

abstract class BaseAppointmentService
{
    protected array $appointmentData;

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
