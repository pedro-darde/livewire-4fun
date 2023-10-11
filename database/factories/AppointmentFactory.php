<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Appointment::class;

    public function definition(): array
    {
        return [

            'start' => $this->faker->dateTimeBetween('now', '+1 hour'),
            'end' => $this->faker->dateTimeBetween('now', '+2 hour'),
            'about' => $this->faker->text(100),
            'user_id' => User::all()->first()->id
        ];
    }
}
