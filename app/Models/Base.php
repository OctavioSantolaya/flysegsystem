<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Base extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'location',
        'description',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($base) {
            if (empty($base->slug) && !empty($base->name)) {
                $base->slug = Str::slug($base->name);
            }
        });

        static::updating(function ($base) {
            if ($base->isDirty('name')) {
                $base->slug = Str::slug($base->name);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Relación muchos a muchos con usuarios
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'base_user');
    }

    /**
     * Relación muchos a muchos con aerolíneas
     */
    public function airlines()
    {
        return $this->belongsToMany(Airline::class, 'airline_base');
    }

    /**
     * Relación con contingencias
     */
    public function contingencies()
    {
        return $this->hasMany(Contingency::class);
    }
}
