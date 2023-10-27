<?php

namespace App\Services\appointment;

use App\Enums\RecurrenceType;
use App\Factory\Appointment\GenerateAppointmentFactory;
use App\Jobs\AfterAppointmentSavedJob;
use App\Models\Appointment;
use App\Models\AppointmentPatient;
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

            $recurrenceType = RecurrenceType::tryFrom($this->appointmentData['recurrence_type']['value']);
            if (!$recurrenceType) {
                throw new \Exception("Invalid recurrence type.");
            }

            if ($isRecurrence) {
                $generator = GenerateAppointmentFactory::createFor($recurrenceType, $this->appointmentData);
                $generator->execute();
                $idsAppointments = $generator->getAppointmentsInserted();
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
            DB::commit();
            AfterAppointmentSavedJob::dispatch($appointments);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
