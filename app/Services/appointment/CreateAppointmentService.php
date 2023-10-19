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

        $loopQty = $useCustomRecurrence ? $this->appointmentData['numberOfRecurrences'] : ($recurrenceType == RecurrenceType::MONTHLY->value ? 6 :  28);

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

    private function createWeeklyRecurrences() {

    }

    private function isBiweeklyOrMonthly() {
        return $this->appointmentData['recurrence_type']['value'] === RecurrenceType::BIWEEKLY->value
                || $this->appointmentData['recurrence_type']['value'] === RecurrenceType::MONTHLY->value;
    }
}
