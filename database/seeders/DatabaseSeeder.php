<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Appointment;
use App\Models\Patient;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(50)->create();
        Patient::factory()->count(50)->create();
        Appointment::factory()->count(50)->create();
    }
}
