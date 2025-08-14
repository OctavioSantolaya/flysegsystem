<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Airline>
 */
class AirlineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $airlines = [
            'Aerolíneas Argentinas',
            'Flybondi',
            'JetSMART Argentina',
            'LATAM Airlines',
            'American Airlines',
            'Copa Airlines',
            'Gol Linhas Aéreas',
            'Avianca',
            'Sky Airline',
            'Azul Brazilian Airlines',
            'Air Europa',
            'Iberia',
            'KLM',
            'Air France',
            'Lufthansa',
            'Delta Air Lines',
            'United Airlines',
            'Qatar Airways',
            'Emirates',
            'Turkish Airlines'
        ];

        static $usedAirlines = [];
        
        $availableAirlines = array_diff($airlines, $usedAirlines);
        if (empty($availableAirlines)) {
            $availableAirlines = $airlines;
            $usedAirlines = [];
        }
        
        $airlineName = $this->faker->randomElement($availableAirlines);
        $usedAirlines[] = $airlineName;

        return [
            'name' => $airlineName,
            'email' => $this->faker->boolean(70) ? $this->faker->companyEmail() : null,
            'phone' => $this->faker->boolean(60) ? $this->faker->phoneNumber() : null,
            'website' => $this->faker->boolean(50) ? $this->faker->url() : null,
        ];
    }
}
