<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Airline;
use App\Models\Base;
use App\Models\Contingency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear el usuario principal
        $mainUser = User::create([
            'name' => 'Octavio Santolaya',
            'email' => 'octaviosantolaya@gmail.com',
            'password' => Hash::make('Santolaya46208016.'),
            'email_verified_at' => now(),
        ]);

        // 2. Crear 9 usuarios adicionales (total 10)
        $users = User::factory(9)->create();
        $allUsers = $users->prepend($mainUser);

        // 3. Crear 15 aerolíneas
        $airlines = Airline::factory(15)->create();

        // 4. Crear 8 bases (aeropuertos argentinos)
        $bases = Base::factory(8)->create();

        // 5. Asociar usuarios y aerolíneas a bases aleatoriamente
        foreach ($bases as $base) {
            // Cada base tendrá entre 2-5 usuarios
            $randomUsers = $allUsers->random(rand(2, 5));
            $base->users()->attach($randomUsers->pluck('id'));

            // Cada base tendrá entre 3-8 aerolíneas
            $randomAirlines = $airlines->random(rand(3, 8));
            $base->airlines()->attach($randomAirlines->pluck('id'));
        }

        // 6. Crear contingencias realistas
        foreach ($bases as $base) {
            $baseUsers = $base->users;
            $baseAirlines = $base->airlines;

            // Crear entre 5-15 contingencias por base
            $contingencyCount = rand(5, 15);
            
            for ($i = 0; $i < $contingencyCount; $i++) {
                $randomUser = $baseUsers->random();
                $randomAirline = $baseAirlines->random();

                // Usar factory mejorado para crear contingencias
                Contingency::factory()->create([
                    'base_id' => $base->id,
                    'airline_id' => $randomAirline->id,
                    'user_id' => $randomUser->id,
                ]);
            }
        }

        // 7. Crear respuestas de formulario para algunas contingencias
        $this->call(FormResponseSeeder::class);

        $this->command->info('✅ Base de datos poblada exitosamente:');
        $this->command->info("   - {$allUsers->count()} usuarios (incluyendo tu usuario)");
        $this->command->info("   - {$airlines->count()} aerolíneas");
        $this->command->info("   - {$bases->count()} bases");
        $this->command->info("   - " . Contingency::count() . " contingencias");
        $this->command->info("   - " . \App\Models\FormResponse::count() . " respuestas de formulario");
    }
}
