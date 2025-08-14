<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Base>
 */
class BaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $argentineAirports = [
            ['name' => 'Aeropuerto Internacional Ezeiza', 'location' => 'Buenos Aires, Argentina'],
            ['name' => 'Aeropuerto Jorge Newbery Airfield', 'location' => 'Buenos Aires, Argentina'],
            ['name' => 'Aeropuerto Internacional Córdoba', 'location' => 'Córdoba, Argentina'],
            ['name' => 'Aeropuerto Internacional Mendoza', 'location' => 'Mendoza, Argentina'],
            ['name' => 'Aeropuerto Internacional Bariloche', 'location' => 'San Carlos de Bariloche, Argentina'],
            ['name' => 'Aeropuerto Internacional Salta', 'location' => 'Salta, Argentina'],
            ['name' => 'Aeropuerto Internacional Iguazú', 'location' => 'Puerto Iguazú, Argentina'],
            ['name' => 'Aeropuerto Internacional Ushuaia', 'location' => 'Ushuaia, Argentina'],
            ['name' => 'Aeropuerto Internacional Mar del Plata', 'location' => 'Mar del Plata, Argentina'],
            ['name' => 'Aeropuerto Internacional Tucumán', 'location' => 'San Miguel de Tucumán, Argentina']
        ];

        static $usedAirports = [];
        
        $availableAirports = array_diff_key($argentineAirports, array_flip($usedAirports));
        if (empty($availableAirports)) {
            $availableAirports = $argentineAirports;
            $usedAirports = [];
        }
        
        $airport = $this->faker->randomElement($availableAirports);
        $usedAirports[] = array_search($airport, $argentineAirports);

        return [
            'name' => $airport['name'],
            'location' => $airport['location'],
            'description' => $this->faker->boolean(70) ? $this->faker->paragraph() : null,
        ];
    }
}
