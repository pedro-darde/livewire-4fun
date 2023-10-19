<?php

namespace App\Services\notifications;

use App\Enums\NotificationPriority;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Models\Appointment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotifcationSchedulerService
{
    protected static array $lackMinutesPriorityMap = [
        NotificationPriority::MEDIUM->value => 30,
        NotificationPriority::HIGH->value => 15,
        NotificationPriority::URGENT->value => 5,
    ];

    static function sendByAppointmentID(int $appointmentID)
    {
        $appointment = Appointment::find($appointmentID);
        if (!$appointment) {
            throw new \Exception("Appointment not found");
        }

        static::scheduleForAnAppointment($appointment);
    }

    public static function scheduleForMultipleAppointments(array $appointments) {
        DB::beginTransaction();
        try {
            foreach($appointments as $appointment) {
                static::scheduleForAnAppointment($appointment);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function scheduleForAnAppointment(Appointment $appointment)
    {
        $exists = Notification::query()
            ->where('relationed_id', $appointment->id)
            ->where('relationed_type', Appointment::class)
            ->exists();

        if ($exists) return;

        $startAppointment = $appointment->start;
        if (!$startAppointment instanceof Carbon) {
            Log::info("Vou criar um novo carbon", [$startAppointment]);
            $startAppointment = Carbon::createFromFormat('Y-m-d H:i:s', $startAppointment, 'America/Sao_Paulo');
        }

        $lowPriorityNotification = $startAppointment->copy()->setHour(8)->setMinute(0)->setSecond(0);
        $mediumPriorityNotification = $startAppointment->copy()->subMinutes(30);
        $highPriorityNotification = $startAppointment->copy()->subMinutes(15);
        $urgentPriorityNotification = $startAppointment->copy()->subMinutes(5);

        $notificationPriorityAndHourToSent = [
            NotificationPriority::LOW->value => $lowPriorityNotification,
            NotificationPriority::MEDIUM->value => $mediumPriorityNotification,
            NotificationPriority::HIGH->value => $highPriorityNotification,
            NotificationPriority::URGENT->value => $urgentPriorityNotification,
        ];

        $notifications = [];
        $patients = $appointment->patients()->get()->map(fn ($patient) => $patient->name . ' ' . $patient->last_name)->join(', ');
        $now = Carbon::now('America/Sao_Paulo');
        foreach ($notificationPriorityAndHourToSent as $priority => $hourToSent) {
            $usFormat = $hourToSent->format('Y-m-d H:i:s');
            $hour = $hourToSent->format('H:i');
            $hourToSent->getTimezone();

            if ($hourToSent->lessThan($now)) {
                Log::info("Irei continuar", [$usFormat, $hourToSent->getTimezone()]);
                continue;
            }
            Log::info("Irei enviar", [$usFormat, $now->format('Y-m-d H:i:s')]);

            if ($priority == NotificationPriority::LOW->value) {
                $description = "Você tem uma consulta marcada para hoje as $hour com o(s) paciente(s) {$patients}";
            } else {
                Log::info("priority: $priority");
                $lackMinitues = static::$lackMinutesPriorityMap[$priority] ?? "";
                $description = "Faltam $lackMinitues minutos para a consulta do(s) paciente(s) {$patients} marcada para as $hour";
            }


            $notifications[] = [
                'user_id' => $appointment->user_id,
                'priority' => $priority,
                'scheduled_at' => $usFormat,
                'title' => 'Lembrete de consulta',
                'description' =>  $description,
                'type' => NotificationType::APPOINTMENT->value,
                'relationed_id' => $appointment->id,
                'relationed_type' => Appointment::class,
                'payload' => json_encode($appointment),
                'status' => NotificationStatus::PENDING->value,
            ];
        }

        Log::info("Notifications to be created: ", $notifications);

        Notification::insert($notifications);
    }
}
