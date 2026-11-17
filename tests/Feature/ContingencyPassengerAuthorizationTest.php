<?php

use App\Models\Contingency;
use App\Models\Passenger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('saves verified passengers within the contingency in the same session', function () {
    $base = DB::table('bases')->insertGetId(['name' => 'Airport', 'slug' => 'airport']);
    $airline = DB::table('airlines')->insertGetId(['name' => 'Airline', 'slug' => 'airline']);
    $contingency = Contingency::withoutEvents(fn () => Contingency::create([
        'contingency_id' => 'test-flight',
        'flight_number' => 'FL101',
        'contingency_type' => 'retraso',
        'date' => now(),
        'base_id' => $base,
        'airline_id' => $airline,
    ]));
    $passenger = Passenger::create([
        'contingency_id' => $contingency->id,
        'name' => 'Test',
        'surname' => 'Gomez',
        'pnr' => 'ABC123',
        'document_number' => 'private-test-value',
    ]);
    $secondPassenger = Passenger::create([
        'contingency_id' => $contingency->id,
        'name' => 'Second',
        'surname' => 'Gomez',
        'pnr' => 'XYZ987',
    ]);

    $submission = [
        'main_passenger_id' => $passenger->id,
        'passengers' => [
            ['id' => $passenger->id, 'age' => 31, 'email' => 'test@example.invalid', 'phone' => '5551234567'],
            ['id' => $secondPassenger->id, 'age' => 12],
        ],
    ];
    $path = '/contingencias/test-flight/';
    $this->postJson($path.'guardar-formulario', $submission)->assertForbidden();
    $this->postJson($path.'buscar-pasajero', ['pnr' => 'abc123', 'last_name' => 'gomez'])
        ->assertOk()
        ->assertJsonPath('passengers.0.id', $passenger->id)
        ->assertJsonMissingPath('passengers.0.document_number');
    $this->postJson($path.'guardar-formulario', $submission)->assertForbidden();
    $this->assertDatabaseCount('form_responses', 0);
    $this->postJson($path.'buscar-pasajero-adicional', ['pnr' => 'XYZ987', 'last_name' => 'Gomez'])->assertOk();
    $this->postJson($path.'guardar-formulario', $submission)->assertOk()->assertJson(['success' => true]);
    $this->assertDatabaseCount('form_responses', 1);
    expect($passenger->fresh()->form_response_id)->not->toBeNull()
        ->toBe($secondPassenger->fresh()->form_response_id);
});
