<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'contingency_id',
        'needs_transport',
        'transport_address',
        'luggage_count',
        'needs_accommodation',
        'has_medical_condition',
        'medical_condition_details',
        'has_flight_reprogramming',
        'reprogrammed_flight_number',
        'reprogrammed_flight_date',
        'assigned_accommodation_info',
        'assigned_transport_info',
    ];

    protected $casts = [
        'needs_transport' => 'boolean',
        'needs_accommodation' => 'boolean',
        'has_medical_condition' => 'boolean',
        'has_flight_reprogramming' => 'boolean',
        'luggage_count' => 'integer',
        'reprogrammed_flight_date' => 'date',
    ];

    /**
     * Relación: Una respuesta pertenece a una contingencia
     */
    public function contingency()
    {
        return $this->belongsTo(Contingency::class);
    }

    /**
     * Relación: Una respuesta puede tener múltiples pasajeros
     */
    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    /**
     * Obtener la cantidad de niños (menores de 18 años)
     */
    public function getChildrenCountAttribute()
    {
        return $this->passengers()->where('age', '<', 18)->count();
    }

    /**
     * Obtener las edades de los niños
     */
    public function getChildrenAgesAttribute()
    {
        return $this->passengers()
            ->where('age', '<', 18)
            ->pluck('age')
            ->toArray();
    }

    /**
     * Validar que el primer pasajero tenga datos de contacto completos
     */
    public function hasValidPrimaryContact()
    {
        $firstPassenger = $this->passengers()->first();
        
        if (!$firstPassenger) {
            return false;
        }

        return $this->isValidEmail($firstPassenger->email) && 
               $this->isValidPhone($firstPassenger->phone);
    }

    /**
     * Validar formato de email
     */
    private function isValidEmail($email)
    {
        return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validar formato de teléfono
     */
    private function isValidPhone($phone)
    {
        // Remover espacios y caracteres especiales
        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Validar que tenga al menos 10 dígitos y máximo 15
        return !empty($cleanPhone) && 
               strlen($cleanPhone) >= 10 && 
               strlen($cleanPhone) <= 15 &&
               preg_match('/^[\+]?[0-9]{10,15}$/', $cleanPhone);
    }
}
