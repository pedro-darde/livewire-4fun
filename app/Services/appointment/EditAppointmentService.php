<?php

namespace App\Services\appointment;

use App\Jobs\AfterAppointmentSavedJob;
use App\Models\Appointment;
use App\Models\AppointmentPatient;
use App\Models\Notification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditAppointmentService extends BaseAppointmentService
{
    public function execute(): void
    {
        $this->validateAppointmentData();
        DB::beginTransaction();

        try {
            $patients = $this->appointmentData['patients'];
            $appointment = Appointment::find($this->appointmentData['id']);

            Log::info('Datas', [$this->appointmentData['start'], $appointment->start]);
            $carbonStartBeforeChange = Carbon::createFromFormat('Y-m-d H:i', $this->appointmentData['start'], 'America/Sao_Paulo');
            $carbonStartUpdated = Carbon::createFromFormat('Y-m-d H:i:s', $appointment->start, 'America/Sao_Paulo');

            $appointment->fill($this->appointmentData);
            $appointment->save();

            $currentPatients = $appointment->patients->pluck('id')->toArray();
            $patientsToAttach = array_diff($patients, $currentPatients);
            $patientsToDetach = array_diff($currentPatients, $patients);

            if (count($patientsToAttach)) {
                AppointmentPatient::insert(
                    array_map(
                        fn ($patientId) => [
                            'appointment_id' => $appointment->id,
                            'patient_id' => $patientId,
                        ],
                        $patientsToAttach
                    )
                );
            }

            if (count($patientsToDetach)) {
                AppointmentPatient::where('appointment_id', $appointment->id)
                    ->whereIn('patient_id', $patientsToDetach)
                    ->delete();
            }

            DB::commit();

            if ($carbonStartBeforeChange->isAfter($carbonStartUpdated)) {
                Log::info("A hora da consulta mudou", [$this->appointmentData['start'], $appointment->start, $this->appointmentData['start'] == $appointment->start]);
                Notification::where('relationed_id', $appointment->id)
                    ->where("relationed_type", Appointment::class)
                    ->delete();
                AfterAppointmentSavedJob::dispatch($appointment);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
