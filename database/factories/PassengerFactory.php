<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Passenger>
 */
class PassengerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generar PNR realista (6 caracteres alfanuméricos)
        $pnr = strtoupper($this->faker->regexify('[A-Z0-9]{6}'));
        
        // Generar edad realista: 80% adultos (18-65), 15% niños (1-17), 5% seniors (66-85)
        $ageRange = $this->faker->randomElement([
            ['min' => 18, 'max' => 65, 'weight' => 80], // Adultos
            ['min' => 1, 'max' => 17, 'weight' => 15],   // Niños
            ['min' => 66, 'max' => 85, 'weight' => 5]    // Seniors
        ]);
        
        return [
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'pnr' => $pnr,
            'email' => $this->faker->optional(0.8)->email(), // 80% tienen email
            'phone' => $this->faker->optional(0.7)->phoneNumber(), // 70% tienen teléfono
            'document_number' => $this->faker->optional(0.9)->regexify('[A-Z0-9]{8,12}'), // 90% tienen documento
            'age' => $this->faker->numberBetween($ageRange['min'], $ageRange['max']),
            // contingency_id se asignará desde el ContingencyFactory o seeder
        ];
    }
}
