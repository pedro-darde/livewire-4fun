<?php

namespace App\Services\appointment;

use App\Enums\AppointmentStatus;
use App\Enums\RecurrenceType;
use App\Jobs\AfterAppointmentSavedJob;
use App\Models\Appointment;
use App\Models\AppointmentPatient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateAppointmentService extends BaseAppointmentService
{

    public function execute(): void
    {
        $this->validateAppointmentData();
        DB::beginTransaction();
        try {
            $isRecurrence = $this->appointmentData['recurrence_type']['value'] !== RecurrenceType::none();
            $idsAppointments = [];
            if ($isRecurrence) {

                if ($this->isBiweeklyOrMonthly()) {
                    $idsAppointments = $this->createBiweeklyMonthlyRecurrences();
                } else {
                    $idsAppointments = $this->createWeeklyRecurrences();
                }
            } else {
                $appointment = new Appointment();
                $appointment->fill($this->appointmentData);
                $appointment->user_id = auth()->user()->id;
                $appointment->save();
                $idsAppointments[] = $appointment->id;
            }

            $appointments = Appointment::query()->whereIn('id', $idsAppointments)->get()->all();

            foreach($appointments as $appointment) {
                AppointmentPatient::insert(
                    array_map(
                        fn ($patientId) => [
                            'appointment_id' => $appointment->id,
                            'patient_id' => $patientId,
                        ],
                        $this->appointmentData['patients']
                    )
                );
            }

            AfterAppointmentSavedJob::dispatch($appointments);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createBiweeklyMonthlyRecurrences()
    {
        $useCustomRecurrence = $this->appointmentData['useCustomRecurrence'];
        $appointmentsToCreate = [];

        $recurrenceType = $this->appointmentData['recurrence_type']['value'];

        $loopQty = $useCustomRecurrence ? $this->appointmentData['numberOfRecurrences'] : ($recurrenceType == RecurrenceType::MONTHLY->value ? self::MAX_MONTHLY_APPOINTMENTS :  self::MAX_BIWEEKLY_APPOINTMENTS);

        $lastStartDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['start'], 'America/Sao_Paulo');
        $lastEndDate = Carbon::createFromFormat('Y-m-d H:i', $this->appointmentData['end'],  'America/Sao_Paulo');

        for ($i = 0; $i < $loopQty; $i++) {
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

            if ($recurrenceType == RecurrenceType::MONTHLY->value) {
                $lastStartDate->addMonths();
                $lastEndDate->addMonths();
                continue;
            }
            $lastStartDate->addWeeks(2);
            $lastEndDate->addWeeks(2);
        }

        Appointment::insert($appointmentsToCreate);
        $lastId = Appointment::orderByDesc('id')->first()->id;
        return range($lastId - count($appointmentsToCreate) + 1, $lastId);
    }

    private function createWeeklyRecurrences()
    {
        $referenceStartDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['start'], 'America/Sao_Paulo');
        $refenceEndDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['end'], 'America/Sao_Paulo');
        $appointmentsToCreate = [];

        if ($this->appointmentData['useCustomRecurrence']) {
           $appointmentsToCreate = $this->getAppointmentsForDefaultWeekRecurrence();
        } else {

        }
    }


    private function getAppointmentsForDefaultWeekRecurrence() {
        $appointmentsToCreate = [];
        $referenceStartDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['start'], 'America/Sao_Paulo');
        $refenceEndDate = Carbon::createFromFormat( 'Y-m-d H:i', $this->appointmentData['end'], 'America/Sao_Paulo');

        // vai criar 8 sessoes (~2 meses~) semanais.
        for ($i = 0; $i < self::MAX_WEEKLY_APPOINTMENTS ; $i++) {

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

        return $appointmentsToCreate;
    }

    private function getAppointmentsForCustomWeekRecurrence()
    {
        $appointmentsToCreate = [];
        if ($this->appointmentData['useDefaultRecurrenceWeek']) {
            // TODO: create the appointments on the days inputted by the user.
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
    }

    private function isBiweeklyOrMonthly() {
        return $this->appointmentData['recurrence_type']['value'] === RecurrenceType::BIWEEKLY->value
                || $this->appointmentData['recurrence_type']['value'] === RecurrenceType::MONTHLY->value;
    }
}
