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
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('pnr');
            $table->string('email')->nullable(); // Email del pasajero
            $table->string('phone')->nullable(); // Teléfono del pasajero
            $table->string('document_number')->nullable(); // Número de documento
            $table->integer('age')->nullable(); // Edad del pasajero

            $table->foreignId('contingency_id')->constrained('contingencies')->onDelete('cascade'); // Relación con contingencia
            $table->foreignId('form_response_id')->nullable()->constrained('form_responses')->onDelete('set null'); // Relación con respuesta del formulario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
