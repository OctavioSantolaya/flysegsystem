<?php

namespace Database\Factories;

use App\Models\Contingency;
use App\Models\Base;
use App\Models\Airline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contingency>
 */
class ContingencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contingencyTypes = array_keys(Contingency::getContingencyTypes());
        
        // Escalas como rutas de vuelo
        $scales = [
            'Madrid-Buenos Aires',
            'Mendoza-Santiago',
            'Buenos Aires-Miami',
            'Córdoba-Lima',
            'Bariloche-São Paulo',
            'Salta-La Paz',
            'Iguazú-Asunción',
            'Ushuaia-Montevideo',
            'Mar del Plata-Rio de Janeiro',
            'Tucumán-Bogotá',
            'Buenos Aires-Madrid',
            'Santiago-Buenos Aires',
            'Lima-Córdoba',
            'São Paulo-Bariloche',
            'La Paz-Salta'
        ];

        // Generar ID como número de vuelo + 2 dígitos extra
        $flightNumber = $this->faker->regexify('[A-Z]{2}[0-9]{3,4}');
        $contingencyId = $flightNumber . $this->faker->numberBetween(10, 99);

        return [
            'contingency_id' => $contingencyId,
            'flight_number' => $flightNumber,
            'contingency_type' => $this->faker->randomElement($contingencyTypes),
            'scale' => $this->faker->randomElement($scales),
            'date' => $this->faker->dateTimeBetween('-30 days', '+7 days'),
            'finished' => $this->faker->boolean(30), // 30% de probabilidad de estar finalizada
            // Las relaciones se asignarán en el seeder
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Contingency $contingency) {
            // Crear entre 40 y 200 pasajeros por contingencia
            $passengerCount = $this->faker->numberBetween(40, 200);
            
            \App\Models\Passenger::factory($passengerCount)->create([
                'contingency_id' => $contingency->id,
            ]);
        });
    }
}
