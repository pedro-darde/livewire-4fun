<?php

namespace App\Factory\Generators\Appointment;

use App\Interfaces\AppointmentsGenerated;

abstract class BaseAppointmentGenerator implements AppointmentsGenerated
{
    protected array $appointmentData;
    protected array $appointmentInsertedIDS = [];

    public function __construct(array $appointmentData) {
        $this->appointmentData = $appointmentData;
    }

    abstract  function execute(): void;

    public function getAppointmentsInserted(): array
    {
        return $this->appointmentInsertedIDS;
    }

}
