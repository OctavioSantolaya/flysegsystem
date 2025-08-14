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
        Schema::create('contingencies', function (Blueprint $table) {
            $table->id();
            $table->string('contingency_id')->unique(); // ID personalizado
            $table->string('flight_number');
            $table->enum('contingency_type', [
                'retraso',
                'cancelacion',
                'sobre_venta',
            ]);
            $table->string('scale')->nullable();
            $table->datetime('date');
            $table->boolean('finished')->default(false); // Campo para marcar si está finalizada
            $table->foreignId('base_id')->constrained()->onDelete('cascade');
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Usuario que creó la contingencia
            $table->timestamps();

            $table->index(['base_id', 'date']);
            $table->index(['airline_id', 'date']);
            $table->index(['contingency_type', 'date']);
            $table->index(['finished', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contingencies');
    }
};
