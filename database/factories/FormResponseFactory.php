<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormResponse>
 */
class FormResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $needsTransport = $this->faker->boolean(60); // 60% probabilidad de necesitar transporte
        $needsAccommodation = $this->faker->boolean(40); // 40% probabilidad de necesitar alojamiento
        $hasMedicalCondition = $this->faker->boolean(15); // 15% probabilidad de condición médica
        $hasFlightReprogramming = $this->faker->boolean(30); // 30% probabilidad de tener reprogramación

        return [
            'contingency_id' => \App\Models\Contingency::factory(),
            'needs_transport' => $needsTransport,
            'transport_address' => $needsTransport ? $this->faker->address() : null,
            'luggage_count' => $this->faker->numberBetween(1, 5),
            'needs_accommodation' => $needsAccommodation,
            'has_medical_condition' => $hasMedicalCondition,
            'medical_condition_details' => $hasMedicalCondition ? $this->faker->sentence() : null,
            'has_flight_reprogramming' => $hasFlightReprogramming,
            'reprogrammed_flight_number' => $hasFlightReprogramming ? $this->faker->regexify('[A-Z]{2}[0-9]{3,4}') : null,
            'reprogrammed_flight_date' => $hasFlightReprogramming ? $this->faker->dateTimeBetween('now', '+7 days')->format('Y-m-d') : null,
            'assigned_accommodation_info' => $needsAccommodation ? $this->faker->optional(0.7)->paragraph() : null,
            'assigned_transport_info' => $needsTransport ? $this->faker->optional(0.6)->paragraph() : null,
        ];
    }
}
