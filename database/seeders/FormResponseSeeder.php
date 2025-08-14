<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las contingencias existentes
        $contingencies = \App\Models\Contingency::with('passengers')->get();

        foreach ($contingencies as $contingency) {
            // Solo crear respuestas si la contingencia tiene pasajeros
            if ($contingency->passengers->count() > 0) {
                // Crear entre 1 y 3 respuestas por contingencia
                $responseCount = rand(1, min(3, ceil($contingency->passengers->count() / 2)));
                
                for ($i = 0; $i < $responseCount; $i++) {
                    // Crear la respuesta
                    $formResponse = \App\Models\FormResponse::factory()->create([
                        'contingency_id' => $contingency->id,
                    ]);

                    // Obtener pasajeros disponibles (sin respuesta asignada)
                    $availablePassengers = $contingency->passengers()
                        ->whereNull('form_response_id')
                        ->get();

                    if ($availablePassengers->count() > 0) {
                        // Asignar entre 1 y 4 pasajeros a esta respuesta
                        $passengersToAssign = $availablePassengers
                            ->random(min(rand(1, 4), $availablePassengers->count()));

                        foreach ($passengersToAssign as $passenger) {
                            $passenger->update(['form_response_id' => $formResponse->id]);
                        }
                    }
                }
            }
        }
    }
}
