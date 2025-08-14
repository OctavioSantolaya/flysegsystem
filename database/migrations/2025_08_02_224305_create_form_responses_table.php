<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contingency_id')->constrained('contingencies')->onDelete('cascade'); // Relación con contingencia
            $table->boolean('needs_transport')->default(false);
            $table->string('transport_address')->nullable(); // Dirección para el transporte
            $table->integer('luggage_count')->default(1);
            $table->boolean('needs_accommodation')->default(false);
            $table->boolean('has_medical_condition')->default(false);
            $table->text('medical_condition_details')->nullable();
            $table->boolean('has_flight_reprogramming')->default(false); // ¿Recibió reprogramación de vuelo?
            $table->string('reprogrammed_flight_number')->nullable(); // Número de vuelo reprogramado
            $table->date('reprogrammed_flight_date')->nullable(); // Fecha del vuelo reprogramado
            $table->text('assigned_accommodation_info')->nullable(); // Campo para información de alojamiento asignado por el operador
            $table->text('assigned_transport_info')->nullable(); // Campo para información de traslado asignado por el operador
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_responses');
    }
};
