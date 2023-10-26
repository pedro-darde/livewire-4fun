<?php

namespace App\Factory\Generators\Appointment;

use App\Enums\AppointmentStatus;
use App\Enums\RecurrenceType;
use App\Models\Appointment;
use Carbon\Carbon;

class BiweeklyMonthlyGenerator extends BaseAppointmentGenerator
{

    public function __construct(array $appointmentData)
    {
        parent::__construct($appointmentData);
    }

    /**
     * @return void
     */
    function execute(): void
    {
        $useCustomRecurrence = $this->appointmentData['useCustomRecurrence'];
        $appointmentsToCreate = [];

        $recurrenceType = $this->appointmentData['recurrence_type']['value'];

        $loopQty = $useCustomRecurrence ? $this->appointmentData['numberOfRecurrences'] : ($recurrenceType == RecurrenceType::MONTHLY->value ? Appointment::MAX_MONTHLY_APPOINTMENTS :  Appointment::MAX_BIWEEKLY_APPOINTMENTS);

        $lastStartDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['start'], 'America/Sao_Paulo');
        $lastEndDate = Carbon::createFromFormat('Y-m-d H:i', $this->appointmentData['end'],  'America/Sao_Paulo');

        for ($i = 0; $i < $loopQty; $i++) {
            if ($lastStartDate->isSunday()) {
                $lastStartDate->addDays(1);
                $lastEndDate->addDays(1);
            }

            if ($lastStartDate->isSaturday()) {
                $lastStartDate->addDays(2);
                $lastEndDate->addDays(2);
            }

            $appointmentsToCreate[] = [
                'start' => $lastStartDate->format('Y-m-d H:i'),
                'end' => $lastEndDate->format('Y-m-d H:i'),
                'about' => $this->appointmentData['about'],
                'status' => AppointmentStatus::PENDING(),
                'id_service_supplied' => $this->appointmentData['id_service_supplied'],
                'is_recurrence' => true,
                'user_id' => auth()->user()->id,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            if ($recurrenceType == RecurrenceType::MONTHLY()) {
                $lastStartDate->addMonths();
                $lastEndDate->addMonths();
                continue;
            }

            $lastStartDate->addWeeks(2);
            $lastEndDate->addWeeks(2);

        }

        Appointment::insert($appointmentsToCreate);
        $lastId = Appointment::orderByDesc('id')->first()->id;
        $this->appointmentInsertedIDS = range($lastId - count($appointmentsToCreate) + 1, $lastId);
    }
}
