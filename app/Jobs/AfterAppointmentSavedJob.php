<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\notifications\NotifcationSchedulerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AfterAppointmentSavedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Appointment| array $appointment;
    /**
     * Create a new job instance.
     */
    public function __construct(Appointment| array $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->scheduleNotification();
    }

    private function scheduleNotification(): void
    {
        if (is_array($this->appointment)) {
            NotifcationSchedulerService::scheduleForMultipleAppointments($this->appointment);
            return;
        }
        NotifcationSchedulerService::scheduleForAnAppointment($this->appointment);
    }
}
