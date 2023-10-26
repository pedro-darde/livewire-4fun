<?php

namespace App\Interfaces;

interface AppointmentsGenerated
{
    public function getAppointmentsInserted(): array;

    public function execute(): void;
}
