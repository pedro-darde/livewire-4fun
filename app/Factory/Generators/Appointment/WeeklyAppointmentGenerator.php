<?php

namespace App\Factory\Generators\Appointment;

use App\Enums\AppointmentStatus;
use App\Interfaces\AppointmentsGenerated;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class WeeklyAppointmentGenerator extends BaseAppointmentGenerator
{
    public function __construct(array $appointmentData) {
        parent::__construct($appointmentData);
    }

    public function execute(): void
    {
        if ($this->appointmentData['useDefaultRecurrence']) {
            $appointmentsToCreate = $this->getAppointmentsForDefaultWeekRecurrence();
        } else {
            $appointmentsToCreate = $this->getAppointmentsForCustomWeekRecurrence();
        }
        Appointment::insert($appointmentsToCreate);
        $lastId = Appointment::orderByDesc('id')->first()->id;
        $this->appointmentInsertedIDS = range($lastId - count($appointmentsToCreate) + 1, $lastId);
    }

    private function getAppointmentsForDefaultWeekRecurrence() {
        $appointmentsToCreate = [];
        $referenceStartDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['start'], 'America/Sao_Paulo');
        $refenceEndDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['end'], 'America/Sao_Paulo');

        // vai criar 8 sessoes (~2 meses~) semanais.
        for ($i = 0; $i < Appointment::MAX_WEEKLY_APPOINTMENTS ; $i++) {

            if ($referenceStartDate->isSunday()) {
                $referenceStartDate->addDay();
                $refenceEndDate->addDay();
            }

            if ($referenceStartDate->isSaturday()) {
                $referenceStartDate->addDays(2);
                $refenceEndDate->addDays(2);
            }

            $appointmentsToCreate[] = [
                'start' => $referenceStartDate->format('Y-m-d H:i'),
                'end' => $refenceEndDate->format('Y-m-d H:i'),
                'about' => $this->appointmentData['about'],
                'status' => AppointmentStatus::PENDING(),
                'id_service_supplied' => $this->appointmentData['id_service_supplied'],
                'is_recurrence' => true,
                'user_id' => auth()->user()->id,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            $referenceStartDate->addWeek();
            $refenceEndDate->addWeek();
        }

        return $appointmentsToCreate;
    }

    private function getAppointmentsForCustomWeekRecurrence()
    {
        $appointmentsToCreate = [];

        $referenceStartDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['start'], 'America/Sao_Paulo');
        $refenceEndDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['end'], 'America/Sao_Paulo');

        if ($this->appointmentData['useDefaultRecurrenceWeek']) {
            for ($i = 0; $i < Appointment::MAX_WEEKLY_APPOINTMENTS; $i++) {
                foreach($this->appointmentData['recurrenceWeeklyDays'] as $dayConfig) {
                    $referenceStartDate->setDay($dayConfig['day']);
                    $refenceEndDate->setDay($dayConfig['day']);

                    [$hourStart, $minuteStart] = explode(':', $dayConfig['start']);
                    [$hourEnd, $minuteEnd] = explode(':', $dayConfig['end']);

                    $referenceStartDate->setTime($hourStart, $minuteStart);
                    $refenceEndDate->setTime($hourEnd, $minuteEnd);

                    if ($referenceStartDate->isSunday()) {
                        $referenceStartDate->addDay();
                        $refenceEndDate->addDay();
                    }

                    if ($referenceStartDate->isSaturday()) {
                        $referenceStartDate->addDays(2);
                        $refenceEndDate->addDays(2);
                    }

                    $appointmentsToCreate[] = [
                        'start' => $referenceStartDate->format('Y-m-d H:i'),
                        'end' => $refenceEndDate->format('Y-m-d H:i'),
                        'about' => $this->appointmentData['about'],
                        'status' => AppointmentStatus::PENDING(),
                        'id_service_supplied' => $this->appointmentData['id_service_supplied'],
                        'is_recurrence' => true,
                        'user_id' => auth()->user()->id,
                        'created_at' => now()->format('Y-m-d H:i:s'),
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                    ];
                }

//                $referenceStartDate = $immutableStartDate->copy();
//                $refenceEndDate = $immutableEndDate->copy();

                $referenceStartDate->addWeek();
                $refenceEndDate->addWeek();
            }
        } else {
            $qtdWeeks = $this->appointmentData['numberOfRecurrences'];
            $referencedStarDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['start'], 'America/Sao_Paulo');
            $referencedEndDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['end'], 'America/Sao_Paulo');

            for ($i = 0; $i < $qtdWeeks; $i++) {
                if ($referencedStarDate->isSunday()) {
                    $referencedStarDate->addDay();
                    $referencedEndDate->addDay();
                }

                if ($referencedStarDate->isSaturday()) {
                    $referencedStarDate->addDays(2);
                    $referencedEndDate->addDays(2);
                }

                $appointmentsToCreate[] = [
                    'about' => $this->appointmentData['about'],
                    'user_id' => auth()->user()->id,
                    'start' => $referencedStarDate->format('Y-m-d H:i'),
                    'end' => $referencedEndDate->format('Y-m-d H:i'),
                    'status' => AppointmentStatus::PENDING(),
                    'id_service_supplied' => $this->appointmentData['id_service_supplied'],
                    'is_recurrence' => true,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $appointmentsToCreate;
    }
}
