<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewContingencyMail;

class Contingency extends Model
{
    use HasFactory;
    protected $fillable = [
        'contingency_id',
        'flight_number',
        'contingency_type',
        'scale',
        'date',
        'finished',
        'base_id',
        'airline_id',
        'user_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'finished' => 'boolean',
    ];

    /**
     * Boot del modelo para eventos
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($contingency) {
            // Cargar las relaciones necesarias
            $contingency->load(['user', 'airline', 'base']);
            
            // Enviar correo a usuarios con roles 'administrador' y 'gestor'
            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['administrador', 'gestor']);
            })->get();

            if ($users->count() > 0) {
                foreach ($users as $user) {
                    try {
                        // Enviar correo en segundo plano usando dispatch
                        Mail::to($user->email)->queue(new NewContingencyMail($contingency, $user));
                        Log::info("Correo de nueva contingencia encolado para: {$user->email}");
                    } catch (\Exception $e) {
                        // Log del error sin interrumpir el proceso
                        Log::error("Error encolando correo para {$user->email}: " . $e->getMessage());
                    }
                }
            } else {
                Log::warning("No se encontraron usuarios con roles 'administrador' o 'gestor' para enviar notificación de contingencia {$contingency->contingency_id}");
            }
        });
    }

    /**
     * Tipos de contingencia disponibles
     */
    public static function getContingencyTypes()
    {
        return [
            'retraso' => 'Retraso',
            'cancelacion' => 'Cancelación',
            'sobre_venta' => 'Sobre venta',
        ];
    }

    /**
     * Relación con Base
     */
    public function base()
    {
        return $this->belongsTo(Base::class);
    }

    /**
     * Relación con Aerolínea
     */
    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    /**
     * Relación con Usuario (quien creó la contingencia)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Pasajeros
     */
    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    /**
     * Relación con Respuestas de Formulario
     */
    public function formResponses()
    {
        return $this->hasMany(FormResponse::class);
    }

    /**
     * Genera un slug único para la contingencia basado en el ID visible
     */
    public function getSlugAttribute()
    {
        return $this->contingency_id;
    }

    /**
     * Genera un nombre descriptivo para la contingencia
     */
    public function getNameAttribute()
    {
        return "Contingencia {$this->contingency_id} - Vuelo {$this->flight_number}";
    }

    /**
     * Scope para filtrar por base del usuario
     */
    public function scopeForUserBases($query, $userId)
    {
        return $query->whereHas('base.users', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function getRouteKeyName()
    {
        return 'contingency_id';
    }
}
