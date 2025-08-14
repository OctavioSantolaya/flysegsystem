<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Airline extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'website',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($airline) {
            if (empty($airline->slug) && !empty($airline->name)) {
                $airline->slug = Str::slug($airline->name);
            }
        });

        static::updating(function ($airline) {
            if ($airline->isDirty('name')) {
                $airline->slug = Str::slug($airline->name);
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Relación muchos a muchos con bases
     */
    public function bases()
    {
        return $this->belongsToMany(Base::class, 'airline_base');
    }

    /**
     * Relación con contingencias
     */
    public function contingencies()
    {
        return $this->hasMany(Contingency::class);
    }
}
